<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\controllers;

use Craft;
use craft\helpers\Json;
use craft\web\Controller;
use plcdnl\videos\errors\GatewayNotFoundException;
use plcdnl\videos\helpers\VideosHelper;
use plcdnl\videos\Plugin as Videos;
use plcdnl\videos\Plugin;
use yii\base\InvalidConfigException;
use yii\web\Response;

/**
 * Vue controller
 */
class ExplorerController extends Controller
{
    /**
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionGetGateways(): Response
    {
        $gateways = Videos::$plugin->getGateways()->getGateways();

        $gatewaysArray = [];

        foreach ($gateways as $gateway) {
            $gatewaysArray[] = [
                'name' => $gateway->getName(),
                'handle' => $gateway->getHandle(),
                'sections' => $gateway->getExplorerSections()
            ];
        }

        return $this->asJson($gatewaysArray);
    }

    /**
     * @return Response
     * @throws GatewayNotFoundException
     * @throws InvalidConfigException
     * @throws \plcdnl\videos\errors\GatewayMethodNotFoundException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionGetVideos(): Response
    {
        $this->requireAcceptsJson();

        $rawBody = Craft::$app->getRequest()->getRawBody();
        $payload = Json::decodeIfJson($rawBody);

        $gatewayHandle = strtolower($payload['gateway']);
        $method = $payload['method'];
        $options = $payload['options'] ?? [];

        $gateway = Videos::$plugin->getGateways()->getGateway($gatewayHandle);

        if (!$gateway instanceof \plcdnl\videos\base\Gateway) {
            throw new GatewayNotFoundException('Gateway not found.');
        }

        $videosResponse = $gateway->getVideos($method, $options);

        $videos = [];

        foreach ($videosResponse['videos'] as $video) {
            $videos[] = VideosHelper::videoToArray($video);
        }

        return $this->asJson([
            'videos' => $videos,
            'more' => $videosResponse['more'],
            'moreToken' => $videosResponse['moreToken']
        ]);
    }

    public function actionGetVideo(): \yii\web\Response
    {
        $this->requireAcceptsJson();

        $rawBody = Craft::$app->getRequest()->getRawBody();
        $payload = Json::decodeIfJson($rawBody);
        $url = $payload['url'];

        $video = Plugin::getInstance()->getVideos()->getVideoByUrl($url);

        if ($video === null) {
            return $this->asErrorJson(Craft::t('videos', 'Unable to find the video.'));
        }

        $videoArray = VideosHelper::videoToArray($video);

        return $this->asJson($videoArray);
    }

    public function actionGetVideoEmbedHtml(): Response
    {
        $this->requireAcceptsJson();

        $rawBody = Craft::$app->getRequest()->getRawBody();
        $payload = Json::decodeIfJson($rawBody);

        $gatewayHandle = strtolower($payload['gateway']);
        $videoId = $payload['videoId'];

        $video = Videos::$plugin->getVideos()->getVideoById($gatewayHandle, $videoId);

        $html = Craft::$app->getView()->renderTemplate('videos/_elements/embedHtml', [
            'video' => $video
        ]);

        return $this->asJson([
            'html' => $html
        ]);
    }
}
