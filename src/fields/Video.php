<?php

/**
 * @link      https://plcd.nl/videos/
 * @copyright Copyright (c) plcdnl
 * @license   https://github.com/plcdnl/videos/blob/v2/LICENSE.md
 */

namespace plcdnl\videos\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use plcdnl\videos\helpers\VideosHelper;
use plcdnl\videos\Plugin as Videos;
use plcdnl\videos\web\assets\videos\VideosAsset;
use craft\helpers\Html;

/**
 * Video field
 */
class Video extends Field
{
    // Public Methods
    // =========================================================================

    /**
     * Get the field’s name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Craft::t('videos', 'Videos');
    }

    /**
     * Get Input HTML.
     *
     * @param                       $value
     * @param ElementInterface|null $element
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getInputHtml(mixed $value, ?\craft\base\ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();
        $name = $this->handle;

        // Normalize the element ID into only alphanumeric characters, underscores, and dashes.
        $id = Html::id($name);

        // Init CSRF Token
        $jsTemplate = 'window.csrfTokenName = "' . Craft::$app->getConfig()->getGeneral()->csrfTokenName . '";';
        $jsTemplate .= 'window.csrfTokenValue = "' . Craft::$app->getRequest()->getCsrfToken() . '";';
        $js = $view->renderString($jsTemplate);
        $view->registerJs($js);

        // Asset bundle
        $view->registerAssetBundle(VideosAsset::class);

        // Field value
        $video = null;

        if (is_object($value)) {
            $video = VideosHelper::videoToArray($value);
        }

        // Translations
        $view->registerTranslations('videos', [
            'Browse videos…',
            'Cancel',
            'Enter a video URL from YouTube or Vimeo',
            'Remove',
            'Search {gateway} videos…',
            'Select',
            '{plays} plays',
        ]);

        // Variables
        $variables = [
            'id' => $id,
            'name' => $name,
            'value' => $video,
            'namespaceId' => $view->namespaceInputId($id),
            'namespaceName' => $view->namespaceInputName($id),
        ];

        // Instantiate Videos Field
        // $view->registerJs('new Videos.Field("'.$view->namespaceInputId($id).'");');
        $view->registerJs('new VideoFieldConstructor({data: {fieldVariables: ' . \json_encode($variables) . '}}).$mount("#' . $view->namespaceInputId($id) . '-vue");');

        return $view->renderTemplate('videos/_components/fieldtypes/Video/input', $variables);
    }

    /**
     * @inheritdoc
     */
    public function serializeValue(mixed $value, ?\craft\base\ElementInterface $element = null): mixed
    {
        if (!empty($value->url)) {
            return Db::prepareValueForDb($value->url);
        }

        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue(mixed $videoUrl, ?\craft\base\ElementInterface $element = null): mixed
    {
        if ($videoUrl instanceof \plcdnl\videos\models\Video) {
            return $videoUrl;
        }

        try {
            if (!empty($videoUrl)) {
                $video = Videos::$plugin->getVideos()->getVideoByUrl($videoUrl);

                if ($video !== null) {
                    return $video;
                }

                $video = new \plcdnl\videos\models\Video();
                $video->url = $videoUrl;
                // TODO: Implement error handling
                // $video->addError('url', Craft::t('videos', 'Unable to find the video.'));

                return $video;
            }
        } catch (\Exception $exception) {
            Craft::info("Couldn't get video in field normalizeValue: " . $exception->getMessage(), __METHOD__);
        }

        return null;
    }

    /**
     * Get Search Keywords
     *
     * @param mixed $value
     * @param ElementInterface $element
     *
     * @return string
     */
    public function getSearchKeywords(mixed $value, \craft\base\ElementInterface $element): string
    {
        $keywords = [];

        if ($value instanceof \plcdnl\videos\models\Video) {
            $keywords[] = $value->id;
            $keywords[] = $value->url;
            $keywords[] = $value->gatewayHandle;
            $keywords[] = $value->gatewayName;
            $keywords[] = $value->authorName;
            $keywords[] = $value->authorUsername;
            $keywords[] = $value->title;
            $keywords[] = $value->description;
        }

        $searchKeywords = StringHelper::toString($keywords, ' ');

        return StringHelper::encodeMb4($searchKeywords);
    }
}
