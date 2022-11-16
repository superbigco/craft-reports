<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\widgets;

use Craft;
use craft\base\Widget;

use superbig\reports\assetbundles\reportswidgetwidget\ReportsWidgetWidgetAsset;
use superbig\reports\Reports;

/**
 * Reports Widget
 *
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportsWidget extends Widget
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $message = 'Hello, world.';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('reports', 'ReportsWidget');
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias("@superbig/reports/assetbundles/reportswidgetwidget/dist/img/ReportsWidget-icon.svg");
    }

    public static function maxColspan(): ?int
    {
        return null;
    }

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            [
                ['message', 'string'],
                ['message', 'default', 'value' => 'Hello, world.'],
            ]
        );
        return $rules;
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
