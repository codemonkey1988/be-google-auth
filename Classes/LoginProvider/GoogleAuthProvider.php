<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\LoginProvider;

use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;
use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\LoginProviderInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class GoogleAuthProvider
 */
class GoogleAuthProvider implements LoginProviderInterface
{
    /**
     * Renders the login mask for google oAuth2.
     *
     * @param StandaloneView $view
     * @param PageRenderer $pageRenderer
     * @param LoginController $loginController
     */
    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController)
    {
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $configuration = $configurationService->getConfiguration();

        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:be_google_auth/Resources/Private/Templates/Backend.html'));

        $pageRenderer->addHeaderData('
            <meta name="google-signin-client_id" content="' . $configuration->getClientId() . '">
            <meta name="google-signin-scope" content="profile email">
        ');
    }
}
