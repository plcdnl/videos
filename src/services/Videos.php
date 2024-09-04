<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\services;

use Craft;
use plcdnl\videos\models\Video;
use yii\base\Component;
use plcdnl\videos\Plugin as VideosPlugin;
use yii\base\InvalidConfigException;

/**
 * Class Videos service.
 *
 * An instance of the Videos service is globally accessible via [[Plugin::videos `Videos::$plugin->getVideos()`]].
 *
 * @author plcdnl <support@plcd.nl>
 * @since  2.0
 */
class Videos extends Component
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool Whether the devServer should be used
     */
    public $useDevServer = false;

    /**
     * {@inheritdoc}
     * @var bool
     */
    public $pluginDevMode = false;

    // Public Methods
    // =========================================================================

    /**
     * Returns the HTML of the embed from a video URL.
     *
     * @param       $videoUrl
     * @param array $embedOptions
     *
     * @return null
     * @throws \yii\base\InvalidConfigException
     */
    public function getEmbed($videoUrl, array $embedOptions = []): ?\Twig\Markup
    {
        $video = $this->getVideoByUrl($videoUrl);

        if ($video !== null) {
            return $video->getEmbed($embedOptions);
        }

        return null;
    }

    /**
     * Get video by ID.
     *
     * @param $gateway
     * @param $id
     *
     * @return mixed|null|string
     * @throws \yii\base\InvalidConfigException
     */
    public function getVideoById($gateway, $id)
    {
        $video = $this->requestVideoById($gateway, $id);

        if ($video) {
            return $video;
        }

        return null;
    }

    /**
     * Get video by URL.
     *
     * @param      $videoUrl
     * @param bool $enableCache
     * @param int|null $cacheExpiry
     * @return Video|null
     * @throws InvalidConfigException
     */
    public function getVideoByUrl($videoUrl, bool $enableCache = true, int $cacheExpiry = null)
    {
        $video = $this->requestVideoByUrl($videoUrl, $enableCache, $cacheExpiry);

        if ($video) {
            return $video;
        }

        return null;
    }

    // Private Methods
    // =========================================================================
    /**
     * Request video by ID.
     *
     * @param      $gatewayHandle
     * @param      $id
     * @param bool $enableCache
     * @param int|null $cacheExpiry
     * @return Video|mixed
     * @throws InvalidConfigException
     */
    private function requestVideoById($gatewayHandle, $id, bool $enableCache = true, int $cacheExpiry = null)
    {
        $enableCache = VideosPlugin::$plugin->getSettings()->enableCache === false ? false : $enableCache;

        if ($enableCache) {
            $key = 'videos.video.' . $gatewayHandle . '.' . md5($id);

            $response = VideosPlugin::$plugin->getCache()->get([$key]);

            if ($response) {
                return $response;
            }
        }

        $gateway = VideosPlugin::$plugin->getGateways()->getGateway($gatewayHandle);

        $response = $gateway->getVideoById($id);

        if ($enableCache) {
            VideosPlugin::$plugin->getCache()->set([$key], $response, $cacheExpiry);
        }

        return $response;
    }

    /**
     * Request video by URL.
     *
     * @param      $videoUrl
     *
     * @return bool|mixed
     * @throws \yii\base\InvalidConfigException
     */
    private function requestVideoByUrl($videoUrl, bool $enableCache = true, int $cacheExpiry = null)
    {
        $key = 'videos.video.' . md5($videoUrl);
        $enableCache = VideosPlugin::$plugin->getSettings()->enableCache === false ? false : $enableCache;

        if ($enableCache) {
            $response = VideosPlugin::$plugin->getCache()->get([$key]);

            if ($response) {
                return $response;
            }
        }

        return $this->findVideoByUrl($videoUrl, $enableCache, $key, $cacheExpiry);
    }

    /**
     * Find video by URL, by looping through all video gateways until a video if found.
     *
     * @param $videoUrl
     * @param $enableCache
     * @param $key
     * @param $cacheExpiry
     *
     * @return bool|mixed
     * @throws \yii\base\InvalidConfigException
     */
    private function findVideoByUrl($videoUrl, $enableCache, $key, $cacheExpiry)
    {
        foreach (VideosPlugin::$plugin->getGateways()->getGateways() as $gateway) {
            $params = [
                'url' => $videoUrl
            ];

            try {
                $video = $gateway->getVideoByUrl($params);

                if ($video) {
                    if ($enableCache) {
                        VideosPlugin::$plugin->getCache()->set([$key], $video, $cacheExpiry);
                    }

                    return $video;
                }
            } catch (\Exception $exception) {
                Craft::info('Couldn’t get video: ' . $exception->getMessage(), __METHOD__);
            }
        }

        return false;
    }
}
