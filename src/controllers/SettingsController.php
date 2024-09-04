<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\controllers;

use Craft;
use craft\web\Controller;
use plcdnl\videos\Plugin as Videos;
use plcdnl\videos\web\assets\videos\VideosAsset;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Settings controller
 */
class SettingsController extends Controller
{
    /**
     * Settings Index.
     *
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionIndex(): Response
    {
        $accounts = [];
        $accountErrors = [];

        $gateways = Videos::$plugin->getGateways()->getGateways(false);

        foreach ($gateways as $gateway) {
            try {
                $accounts[$gateway->getHandle()] = $gateway->getAccount();
                $accountErrors[$gateway->getHandle()] = false;
            } catch (IdentityProviderException $identityProviderException) {
                $error = $identityProviderException->getMessage();

                $data = $identityProviderException->getResponseBody();

                if (isset($data['error_description'])) {
                    $error = $data['error_description'];
                }

                $accounts[$gateway->getHandle()] = false;
                $accountErrors[$gateway->getHandle()] = $error;
            }
        }

        Craft::$app->getView()->registerAssetBundle(VideosAsset::class);

        return $this->renderTemplate('videos/settings/_index', [
            'gateways' => $gateways,
            'accounts' => $accounts,
            'accountErrors' => $accountErrors,
        ]);
    }

    /**
     * Gateway.
     *
     * @param $gatewayHandle
     *
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionGateway($gatewayHandle): Response
    {
        $gateway = Videos::$plugin->getGateways()->getGateway($gatewayHandle, false);
        $account = null;

        try {
            $account = $gateway->getAccount();
        } catch (IdentityProviderException $identityProviderException) {
            $error = $identityProviderException->getMessage();
            $data = $identityProviderException->getResponseBody();

            if (isset($data['error_description'])) {
                $error = $data['error_description'];
            }
        }

        return $this->renderTemplate('videos/settings/_gateway', [
            'gatewayHandle' => $gatewayHandle,
            'gateway' => $gateway,
            'account' => $account,
            'error' => $error ?? null
        ]);
    }

    /**
     * Gateway OAuth.
     *
     * @param $gatewayHandle
     *
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionGatewayOauth($gatewayHandle): Response
    {
        $gateway = Videos::$plugin->getGateways()->getGateway($gatewayHandle, false);

        return $this->renderTemplate('videos/settings/_oauth', [
            'gatewayHandle' => $gatewayHandle,
            'gateway' => $gateway,
        ]);
    }

    /**
     * Save gateway.
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws \craft\errors\MissingComponentException
     */
    public function actionSaveGateway(): Response
    {
        $this->requireAdmin();

        $gatewayHandle = Craft::$app->getRequest()->getParam('gatewayHandle');
        $gateway = Videos::$plugin->getGateways()->getGateway($gatewayHandle, false);

        $clientId = Craft::$app->getRequest()->getParam('clientId');
        $clientSecret = Craft::$app->getRequest()->getParam('clientSecret');

        $configData = [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
        ];

        $key = 'plugins.videos.settings.oauthProviderOptions';
        $configPath = $key . '.' . $gateway->getHandle();

        Craft::$app->getProjectConfig()->set($configPath, $configData, sprintf('Save the “%s” integration', $gateway->getHandle()));

        Craft::$app->getSession()->setNotice(Craft::t('videos', 'Gateway’s OAuth settings saved.'));

        return $this->redirectToPostedUrl();
    }
}
