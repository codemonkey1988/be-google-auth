<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Google;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Client
 */
class Client implements SingletonInterface
{
    /**
     * Fetch a user profile using the google oAuth2 API.
     *
     * @param string $token
     * @throws InvalidClientResponseException
     * @return array
     */
    public function fetchUserProfile(string $token): array
    {
        $url = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $token;
        $result = GeneralUtility::getUrl($url);

        if (!$result) {
            throw new InvalidClientResponseException('Google API call returns an empty response', 1544643907);
        }

        return json_decode($result, true);
    }
}
