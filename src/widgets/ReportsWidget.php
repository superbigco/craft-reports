<?php

declare(strict_types=1);

namespace superbig\reports\widgets;

use Craft;
use craft\base\Widget;
use superbig\reports\assetbundles\reportswidgetwidget\ReportsWidgetWidgetAsset;

class ReportsWidget extends Widget
{
    public string $message = 'Hello, world.';

    public static function displayName(): string
    {
        return Craft::t('reports', 'ReportsWidget');
    }

    public static function maxColspan(): ?int
    {
        return null;
    }

    public function rules(): array
    {
        $rules = parent::rules();

        return array_merge(
            $rules,
            [
                ['message', 'string'],
                ['message', 'default', 'value' => 'Hello, world.'],
            ]
        );
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'reports/_components/widgets/ReportsWidget_settings',
            [
                'widget' => $this,
            ]
        );
    }

    public function getBodyHtml(): ?string
    {
        Craft::$app->getView()->registerAssetBundle(ReportsWidgetWidgetAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'reports/_components/widgets/ReportsWidget_body',
            [
                'message' => $this->message,
            ]
        );
    }
}
