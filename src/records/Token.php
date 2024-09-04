<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\records;

use craft\db\ActiveRecord;

/**
 * Token record.
 *
 * @property int $id
 * @property string $gateway
 * @property string $accessToken
 */
class Token extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the associated database table.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%videos_tokens}}';
    }
}
