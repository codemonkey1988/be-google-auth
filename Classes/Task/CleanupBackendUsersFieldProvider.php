<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Task;

use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Class CleanupBackendUsersFieldProvider
 */
class CleanupBackendUsersFieldProvider implements AdditionalFieldProviderInterface
{
    const INPUT_HTML = '<input type="%s" name="tx_scheduler[%s]" id="%s" value="%s" />';
    const FIELD_DAYS_SINCE_LAST_LOGIN = 'daysSinceLastLogin';
    const FIELD_DAYS_SINCE_LAST_LOGIN_DEFAULT_VALUE = 30;

    /**
     * @param array $taskInfo
     * @param AbstractTask $task
     * @param SchedulerModuleController $schedulerModule
     * @return array
     */
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $schedulerModule): array
    {
        if (!isset($taskInfo['daysSinceLastLogin'])) {
            $taskInfo['daysSinceLastLogin'] = self::FIELD_DAYS_SINCE_LAST_LOGIN_DEFAULT_VALUE;

            if ($schedulerModule->getCurrentAction()->equals('edit')) {
                $taskInfo['daysSinceLastLogin'] = (int)$task->daysSinceLastLogin;
            }
        }

        return [
            'daysSinceLastLogin' => [
                'code' => sprintf(
                    self::INPUT_HTML,
                    'number',
                    self::FIELD_DAYS_SINCE_LAST_LOGIN,
                    self::FIELD_DAYS_SINCE_LAST_LOGIN,
                    $taskInfo['daysSinceLastLogin']
                ),
                'label' => 'LLL:EXT:be_google_auth/Resources/Private/Language/locallang_be.xlf:scheulder.task.field.daysSinceLastLogin',
                'cshKey' => '_MOD_tools_txschedulerM1',
                'cshLabel' => self::FIELD_DAYS_SINCE_LAST_LOGIN,
            ],
        ];
    }

    /**
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @return bool
     * @throws Exception
     */
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $schedulerModule): bool
    {
        if (!MathUtility::canBeInterpretedAsInteger($submittedData[self::FIELD_DAYS_SINCE_LAST_LOGIN])) {
            $this->addFlashMessage($GLOBALS['LANG']->sL('Please enter an integer value.'), '', FlashMessage::ERROR);

            return false;
        } elseif ($submittedData[self::FIELD_DAYS_SINCE_LAST_LOGIN] < 0) {
            $this->addFlashMessage($GLOBALS['LANG']->sL('Please enter a value greater or equal 0.'), '', FlashMessage::ERROR);

            return false;
        }

        return true;
    }

    /**
     * @param array $submittedData
     * @param AbstractTask $task
     */
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
        if ($task instanceof CleanupBackendUsersTask) {
            $task->daysSinceLastLogin = (int)$submittedData[self::FIELD_DAYS_SINCE_LAST_LOGIN];
        }
    }

    /**
     * @param $messageBody
     * @param string $messageTitle
     * @param int $severity
     * @throws Exception
     * @see ModuleTemplate::addFlashMessage()
     */
    protected function addFlashMessage($messageBody, $messageTitle = '', $severity = AbstractMessage::OK)
    {
        if (!is_string($messageBody)) {
            throw new \InvalidArgumentException('The message body must be of type string, "' . gettype($messageBody) . '" given.', 1622835067);
        }
        /* @var FlashMessage $flashMessage */
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $messageBody,
            $messageTitle,
            $severity,
            true
        );
        $this->getFlashMessageService()->getMessageQueueByIdentifier()->enqueue($flashMessage);
    }

    /**
     * @return FlashMessageService
     */
    protected function getFlashMessageService()
    {
        return GeneralUtility::makeInstance(FlashMessageService::class);
    }
}
