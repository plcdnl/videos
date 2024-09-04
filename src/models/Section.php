<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\models;

use craft\base\Model;

/**
 * Section model class.
 *
 * @author plcdnl <support@plcd.nl>
 * @since  2.0
 */
class Section extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string|null Name
     */
    public $name;

    /**
     * @var mixed|null Collections
     */
    public $collections;
}
