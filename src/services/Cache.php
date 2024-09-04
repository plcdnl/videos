<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\services;

use Craft;
use plcdnl\videos\Plugin as VideosPlugin;
use yii\base\Component;
use DateInterval;
use craft\helpers\DateTimeHelper;

/**
 * Class Cache service.
 *
 * An instance of the Cache service is globally accessible via [[Plugin::cache `VideosPlugin::$plugin->getCache()`]].
 *
 * @author plcdnl <support@plcd.nl>
 * @since  2.0
 */
class Cache extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get cache.
     *
     * @param $id
     *
     * @return mixed
     */
    public function get(array $id)
    {
        $cacheKey = $this->getCacheKey($id);

        return Craft::$app->getCache()->get($cacheKey);
    }

    /**
     * Set cache.
     *
     * @param      $id
     * @param      $value
     * @param null $expire
     * @param null $dependency
     * @param null $enableCache
     *
     * @return bool|null
     * @throws \Exception
     */
    public function set($id, $value, $expire = null, $dependency = null, $enableCache = null)
    {
        if (null === $enableCache) {
            $enableCache = VideosPlugin::$plugin->getSettings()->cacheDuration;
        }

        if ($enableCache) {
            $cacheKey = $this->getCacheKey($id);

            if (!$expire) {
                $duration = VideosPlugin::$plugin->getSettings()->cacheDuration;
                $interval = new DateInterval($duration);
                $expire = DateTimeHelper::intervalToSeconds($interval);
            }

            return Craft::$app->cache->set($cacheKey, $value, $expire, $dependency);
        }

        return null;
    }

    // Private Methods
    // =========================================================================

    /**
     * Return the cache key
     *
     * @param array $request
     *
     * @return string
     */
    private function getCacheKey(array $request): string
    {
        unset($request['CRAFT_CSRF_TOKEN']);

        $hash = md5(serialize($request));

        return 'videos.' . $hash;
    }
}
