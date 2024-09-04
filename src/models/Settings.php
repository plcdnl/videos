<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\models;

use craft\base\Model;

/**
 * Settings model class.
 *
 * @author plcdnl <support@plcd.nl>
 * @since  2.0
 */
class Settings extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string The amount of time cache should last.
     *
     * @see http://www.php.net/manual/en/dateinterval.construct.php
     */
    public $cacheDuration = 'PT15M';

    /**
     * @var bool Whether request to APIs should be cached or not.
     */
    public $enableCache = true;

    /**
     * @var array OAuth provider options.
     */
    public $oauthProviderOptions = [];

    /**
     * @var int The number of videos per page in the explorer.
     */
    public $videosPerPage = 30;
}
