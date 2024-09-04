<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\base;

use plcdnl\videos\Plugin as Videos;

/**
 * PluginTrait implements the common methods and properties for plugin classes.
 *
 * @property \plcdnl\videos\services\Videos $videos     The videos service
 * @property \plcdnl\videos\services\Cache $cache      The cache service
 * @property \plcdnl\videos\services\Gateways $gateways   The gateways service
 * @property \plcdnl\videos\services\Oauth $oauth      The oauth service
 */
trait PluginTrait
{
    /**
     * Returns the videos service.
     *
     * @return \plcdnl\videos\services\Videos The videos service
     * @throws \yii\base\InvalidConfigException
     */
    public function getVideos(): \plcdnl\videos\services\Videos
    {
        /** @var Videos $this */
        return $this->get('videos');
    }

    /**
     * Returns the cache service.
     *
     * @return \plcdnl\videos\services\Cache The cache service
     * @throws \yii\base\InvalidConfigException
     */
    public function getCache(): \plcdnl\videos\services\Cache
    {
        /** @var Videos $this */
        return $this->get('cache');
    }

    /**
     * Returns the gateways service.
     *
     * @return \plcdnl\videos\services\Gateways The gateways service
     * @throws \yii\base\InvalidConfigException
     */
    public function getGateways(): \plcdnl\videos\services\Gateways
    {
        /** @var Videos $this */
        return $this->get('gateways');
    }

    /**
     * Returns the oauth service.
     *
     * @return \plcdnl\videos\services\Oauth The oauth service
     * @throws \yii\base\InvalidConfigException
     */
    public function getOauth(): \plcdnl\videos\services\Oauth
    {
        /** @var Videos $this */
        return $this->get('oauth');
    }

    /**
     * Returns the tokens service.
     *
     * @return \plcdnl\videos\services\Tokens The tokens service
     * @throws \yii\base\InvalidConfigException
     */
    public function getTokens(): \plcdnl\videos\services\Tokens
    {
        /** @var Videos $this */
        return $this->get('tokens');
    }
}
