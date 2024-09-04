<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\services;

use plcdnl\videos\models\Token;
use Exception;
use League\OAuth2\Client\Token\AccessToken;
use yii\base\Component;
use plcdnl\videos\Plugin;
use League\OAuth2\Client\Grant\RefreshToken;

/**
 * Class Oauth service.
 *
 * An instance of the Oauth service is globally accessible via [[Plugin::oauth `Plugin::$plugin->getOauth()`]].
 *
 * @author plcdnl <support@plcd.nl>
 * @since  2.0
 */
class Oauth extends Component
{
    // Public Methods
    // =========================================================================
    /**
     * Get a token by its gateway handle.
     *
     * @param $gatewayHandle
     * @return AccessToken|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getToken(string $gatewayHandle, bool $refresh = true): ?\League\OAuth2\Client\Token\AccessToken
    {
        $token = Plugin::getInstance()->getTokens()->getToken($gatewayHandle);

        if (!$token instanceof \plcdnl\videos\models\Token) {
            return null;
        }

        return $this->createTokenFromData($gatewayHandle, $token->accessToken, $refresh);
    }

    /**
     * Saves a token.
     *
     * @param $gatewayHandle
     * @param AccessToken $token
     * @return bool
     * @throws Exception
     */
    public function saveToken($gatewayHandle, AccessToken $token): bool
    {
        $tokenModel = Plugin::getInstance()->getTokens()->getToken($gatewayHandle);

        if ($tokenModel === null) {
            $tokenModel = new Token();
            $tokenModel->gateway = $gatewayHandle;
        }

        $tokenModel->accessToken = [
            'accessToken' => $token->getToken(),
            'expires' => $token->getExpires(),
            'resourceOwnerId' => $token->getResourceOwnerId(),
            'values' => $token->getValues(),
        ];

        if (!empty($token->getRefreshToken())) {
            $tokenModel->accessToken['refreshToken'] = $token->getRefreshToken();
        }

        Plugin::getInstance()->getTokens()->saveToken($tokenModel);

        return true;
    }

    /**
     * Deletes a token.
     *
     * @param $gatewayHandle
     * @return bool
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function deleteToken($gatewayHandle): bool
    {
        $token = Plugin::getInstance()->getTokens()->getToken($gatewayHandle);

        if (!$token instanceof \plcdnl\videos\models\Token) {
            return true;
        }

        return Plugin::getInstance()->getTokens()->deleteTokenById($token->id);
    }

    // Private Methods
    // =========================================================================
    /**
     * Create token from data.
     *
     *
     * @return AccessToken|null
     * @throws \yii\base\InvalidConfigException
     */
    private function createTokenFromData(string $gatewayHandle, array $data, bool $refreshToken = true): ?\League\OAuth2\Client\Token\AccessToken
    {
        if (!isset($data['accessToken'])) {
            return null;
        }

        $token = new AccessToken([
            'access_token' => $data['accessToken'] ?? null,
            'expires' => $data['expires'] ?? null,
            'refresh_token' => $data['refreshToken'] ?? null,
            'resource_owner_id' => $data['resourceOwnerId'] ?? null,
            'values' => $data['values'] ?? null,
        ]);

        // Refresh OAuth token
        if ($refreshToken && !empty($token->getRefreshToken()) && $token->getExpires() && $token->hasExpired()) {
            $gateway = Plugin::$plugin->getGateways()->getGateway($gatewayHandle);
            $provider = $gateway->getOauthProvider();
            $grant = new RefreshToken();
            $newToken = $provider->getAccessToken($grant, ['refresh_token' => $token->getRefreshToken()]);

            $token = new AccessToken([
                'access_token' => $newToken->getToken(),
                'expires' => $newToken->getExpires(),
                'refresh_token' => $token->getRefreshToken(),
                'resource_owner_id' => $newToken->getResourceOwnerId(),
                'values' => $newToken->getValues(),
            ]);

            Plugin::$plugin->getOauth()->saveToken($gateway->getHandle(), $token);
        }

        return $token;
    }
}
