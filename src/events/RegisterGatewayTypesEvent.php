<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\events;

use yii\base\Event;

/**
 * RegisterGatewayTypesEvent class.
 *
 * @author plcdnl <support@plcd.nl>
 * @since  2.0
 */
class RegisterGatewayTypesEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var array The registered login providers.
     */
    public $gatewayTypes = [];
}
