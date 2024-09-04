<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\models;

use craft\base\Model;

/**
 * Collection model class.
 *
 * @author plcdnl <support@plcd.nl>
 * @since  2.0
 */
class Collection extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string|null Name
     */
    public $name;

    /**
     * @var string|null Method
     */
    public $method;

    /**
     * @var mixed|null Options
     */
    public $options;

    /**
     * @var string|null Icon
     */
    public $icon;
}
