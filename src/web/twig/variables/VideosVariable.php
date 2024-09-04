<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\web\twig\variables;

use Craft;
use plcdnl\videos\models\Video;
use plcdnl\videos\Plugin as Videos;

class VideosVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Get Embed.
     *
     * @param       $videoUrl
     * @param array $embedOptions
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function getEmbed($videoUrl, array $embedOptions = [])
    {
        return Videos::$plugin->getVideos()->getEmbed($videoUrl, $embedOptions);
    }

    /**
     * Get a video from its URL.
     *
     * @param      $videoUrl
     * @param bool $enableCache
     * @param int|null $cacheExpiry
     * @return Video|null
     */
    public function getVideoByUrl($videoUrl, bool $enableCache = true, int $cacheExpiry = null): ?\plcdnl\videos\models\Video
    {
        try {
            return Videos::$plugin->getVideos()->getVideoByUrl($videoUrl, $enableCache, $cacheExpiry);
        } catch (\Exception $exception) {
            Craft::info('Couldn’t get video from its url (' . $videoUrl . '): ' . $exception->getMessage(), __METHOD__);
        }

        return null;
    }

    /**
     * Alias for the `getVideoByUrl()` method.
     *
     * @param      $videoUrl
     * @param bool $enableCache
     * @param int|null $cacheExpiry
     * @return Video|null
     */
    public function url($videoUrl, bool $enableCache = true, int $cacheExpiry = null): ?\plcdnl\videos\models\Video
    {
        return $this->getVideoByUrl($videoUrl, $enableCache, $cacheExpiry);
    }
}
