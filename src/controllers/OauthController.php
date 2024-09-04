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
use yii\base\InvalidConfigException;
use yii\web\Response;

/**
 * OAuth controller
 */
class OauthController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Connect.
     *
     * @return Response
     * @throws InvalidConfigException
     * @throws \craft\errors\MissingComponentException
     */
    public function actionConnect(): Response
    {
        $gatewayHandle = Craft::$app->getRequest()->getParam('gateway');

        $gateway = Videos::$plugin->getGateways()->getGateway($gatewayHandle, false);

        Craft::$app->getSession()->set('videos.oauthGateway', $gatewayHandle);

        return $gateway->oauthConnect();
    }

    /**
     * Callback.
     *
     * @return Response
     * @throws InvalidConfigException
     * @throws \craft\errors\MissingComponentException
     */
    public function actionCallback(): Response
    {
        $gatewayHandle = Craft::$app->getSession()->get('videos.oauthGateway');

        $gateway = Videos::$plugin->getGateways()->getGateway($gatewayHandle, false);

        return $gateway->oauthCallback();
    }

    /**
     * Disconnect.
     *
     * @return Response
     * @throws InvalidConfigException
     * @throws \craft\errors\MissingComponentException
     */
    public function actionDisconnect(): Response
    {
        $gatewayHandle = Craft::$app->getRequest()->getParam('gateway');
        $gateway = Videos::$plugin->getGateways()->getGateway($gatewayHandle, false);

        Videos::$plugin->getOauth()->deleteToken($gateway->getHandle());

        // set notice
        Craft::$app->getSession()->setNotice(Craft::t('videos', 'Disconnected.'));


        // redirect

        $redirect = Craft::$app->getRequest()->referrer;

        return $this->redirect($redirect);
    }
}
