<?php
namespace Codemonkey1988\BeGoogleAuth\UserProvider;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\Google\Gsuite;
use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;
use Codemonkey1988\BeGoogleAuth\UserProvider\Permission\AdminByFileBackendUserPermission;
use Codemonkey1988\BeGoogleAuth\UserProvider\Permission\BackendUserPermissionInterface;
use Codemonkey1988\BeGoogleAuth\UserProvider\Permission\InvalidPermissionException;
use Codemonkey1988\BeGoogleAuth\UserProvider\Permission\SimpleBackendUserPermission;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;
use TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility;

/**
 * Class BackendUserProvider
 */
class BackendUserProvider implements UserProviderInterface
{
    /**
     * @var array
     */
    protected $authenticationInformation;

    /**
     * @var Gsuite
     */
    protected $gsuite;

    /**
     * @var BackendUserPermissionInterface
     */
    protected $permissionProvider;

    /**
     * @var ExtensionConfiguration
     */
    protected $extensionConfiguration;

    /**
     * AbstractUserProvider constructor.
     *
     * @param array $authenticationInformation
     */
    public function __construct(array $authenticationInformation)
    {
        $this->authenticationInformation = $authenticationInformation;
    }

    /**
     * @param ConfigurationService $configurationService
     */
    public function injectConfigurationService(ConfigurationService $configurationService)
    {
        $this->gsuite = GeneralUtility::makeInstance(Gsuite::class);
        $this->gsuite->injectConfigurationService($configurationService);
        $this->extensionConfiguration = $configurationService->getConfiguration();
    }

    /**
     * Creates a new backend user for google oAuth2 usage.
     *
     * @param string $email
     * @param string $name
     * @throws InvalidPermissionException
     * @return void
     */
    public function createUser(string $email, string $name)
    {
        $permission = $this->getBackendUserPermission();
        $data = [
            'username' => $email,
            'password' => $this->generatePassword(),
            'email' => $email,
            'realName' => $name,
            'tstamp' => $GLOBALS['EXEC_TIME'],
            'crdate' => $GLOBALS['EXEC_TIME'],
            'description' => 'Auto generated by be_google_auth plugin',
            'google_oauth' => 1,
        ];

        if ($permission->isAdmin($email)) {
            $data['admin'] = 1;
        } else {
            $data['usergroup'] = implode(',', $permission->getUserGroupUids($email));
        }

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('be_users')
            ->insert('be_users', $data);
    }

    /**
     * Fetch an existing backend user by its email address.
     *
     * @param string $email
     * @param bool $respectEnableFields When false, also deleted and disabled users will be fetched.
     * @return array
     */
    public function getUserByEmail(string $email, $respectEnableFields = true): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->authenticationInformation['db_user']['table']);

        // Remove all restrictions - Restrictions will be set by $this->authenticationInformation['db_user']['enable_clause']
        $queryBuilder->getRestrictions()->removeAll();

        $conditions = [
            $queryBuilder->expr()->eq(
                'email',
                $queryBuilder->createNamedParameter($email, \PDO::PARAM_STR)
            ),
        ];

        if ($respectEnableFields) {
            $conditions[] = $this->authenticationInformation['db_user']['enable_clause'];
        }

        $records = $queryBuilder
            ->select('*')
            ->from($this->authenticationInformation['db_user']['table'])
            ->where(...$conditions)
            ->execute()
            ->fetchAll();

        $count = count($records);
        if ($count > 1) {
            throw new \UnexpectedValueException(sprintf('Too many records found for email address "%s".', $email), 1527920312);
        }

        return $count === 1 ? $records[0] : [];
    }

    /**
     * Sets the deleted flag to 0 for the given user record.
     *
     * @param array $userRecord
     * @throws InvalidPermissionException
     * @return void
     */
    public function restoreUser(array $userRecord)
    {
        $permission = $this->getBackendUserPermission();
        $admin = $permission->isAdmin($userRecord['username']) ? 1 : 0;
        $userGroupUids = $admin ? [] : $permission->getUserGroupUids($userRecord['username']);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->authenticationInformation['db_user']['table']);
        $queryBuilder->update($this->authenticationInformation['db_user']['table'])
            ->set('deleted', 0)
            ->set('admin', $admin)
            ->set('usergroup', implode(',', $userGroupUids))
            ->execute();
    }

    /**
     * @return string
     */
    protected function generatePassword(): string
    {
        $generator = new ComputerPasswordGenerator();
        $generator
            ->setUppercase()
            ->setLowercase()
            ->setNumbers()
            ->setSymbols()
            ->setLength(64);

        if (SaltedPasswordsUtility::isUsageEnabled()) {
            $objInstanceSaltedPW = SaltFactory::getSaltingInstance();
            $password = $objInstanceSaltedPW->getHashedPassword($generator->generatePassword());
        }

        return $password;
    }

    /**
     * @throws InvalidPermissionException
     * @return BackendUserPermissionInterface
     */
    protected function getBackendUserPermission(): BackendUserPermissionInterface
    {
        $providerClass = null;

        if ($this->extensionConfiguration->getGsuite()->isEnabled() &&
            $this->extensionConfiguration->getGsuite()->getAdminByFilePath()) {
            $providerClass = AdminByFileBackendUserPermission::class;
        } else {
            $providerClass = SimpleBackendUserPermission::class;
        }

        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['be_google_auth']['permissionProvider']) &&
            class_exists($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['be_google_auth']['permissionProvider'])) {
            $providerClass = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['be_google_auth']['permissionProvider'];
        }

        $permission = GeneralUtility::makeInstance($providerClass);

        if (!$permission instanceof BackendUserPermissionInterface) {
            throw new InvalidPermissionException(
                'The given permission provider dies not implement "BackendUserPermissionInterface".',
                1549912013
            );
        }

        return $permission;
    }
}
