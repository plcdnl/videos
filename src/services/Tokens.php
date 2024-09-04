<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\services;

use Craft;
use plcdnl\analytics\errors\InvalidViewException;
use plcdnl\videos\errors\TokenNotFoundException;
use plcdnl\videos\models\Token;
use plcdnl\videos\records\Token as TokenRecord;
use Exception;
use yii\base\Component;

/**
 * Class Tokens service.
 *
 * An instance of the Tokens service is globally accessible via [[Plugin::oauth `VideosPlugin::$plugin->getTokens()`]].
 *
 * @author plcdnl <support@plcd.nl>
 * @since  2.0
 */
class Tokens extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get a token by its gateway handle.
     *
     * @param $gatewayHandle
     * @return Token|null
     */
    public function getToken($gatewayHandle): ?\plcdnl\videos\models\Token
    {
        $result = TokenRecord::find()
            ->where(['gateway' => $gatewayHandle])
            ->one();

        if (!$result) {
            return null;
        }

        return new Token($result->toArray([
            'id',
            'gateway',
            'accessToken',
        ]));
    }

    /**
     * @param Token $token
     * @param bool $runValidation
     * @return bool
     * @throws TokenNotFoundException
     * @throws \yii\db\Exception
     */
    public function saveToken(Token $token, bool $runValidation = true): bool
    {
        if ($runValidation && !$token->validate()) {
            Craft::info('Token not saved due to validation error.', __METHOD__);

            return false;
        }

        if ($token->id) {
            $tokenRecord = TokenRecord::findOne($token->id);

            if (!$tokenRecord) {
                throw new TokenNotFoundException(sprintf("No token exists with the ID '%s'", $token->id));
            }

            $isNewToken = false;
        } else {
            $tokenRecord = new TokenRecord();
            $isNewToken = true;
        }

        $tokenRecord->gateway = $token->gateway;
        $tokenRecord->accessToken = $token->accessToken;

        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            // Is the event giving us the go-ahead?
            $tokenRecord->save(false);

            // Now that we have a view ID, save it on the model
            if ($isNewToken) {
                $token->id = $tokenRecord->id;
            }

            $transaction->commit();
        } catch (Exception $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        return true;
    }

    /**
     * Deletes a token.
     *
     * @param int $id
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteTokenById(int $id): bool
    {
        $tokenRecord = TokenRecord::findOne($id);

        if (!$tokenRecord instanceof \plcdnl\videos\records\Token) {
            return true;
        }

        $tokenRecord->delete();

        return true;
    }
}
