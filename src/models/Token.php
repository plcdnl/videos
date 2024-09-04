<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\models;

use craft\base\Model;
use craft\helpers\Json;

class Token extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var int|null ID
     */
    public $id;

    /**
     * @var string|null Gateway
     */
    public $gateway;


    /**
     * @var string|null Access token
     */
    public $accessToken;

    /**
     * @var \DateTime|null Date updated
     */
    public $dateUpdated;

    /**
     * @var \DateTime|null Date created
     */
    public $dateCreated;

    /**
     * @var string|null Uid
     */
    public $uid;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        if (is_string($this->accessToken)) {
            $this->accessToken = Json::decode($this->accessToken);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id'], 'number', 'integerOnly' => true],
        ];
    }
}
