<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports;

use Craft;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Dashboard;
use craft\services\Plugins;
use craft\web\twig\variables\CraftVariable;

use craft\web\UrlManager;
use superbig\reports\models\Settings;
use superbig\reports\services\Chart as ChartService;
use superbig\reports\services\Email as EmailService;
use superbig\reports\services\Report as ReportService;
use superbig\reports\services\Target;
use superbig\reports\services\Widget as WidgetService;
use superbig\reports\twigextensions\ReportsTwigExtension;
use superbig\reports\variables\ReportsVariable;
use superbig\reports\widgets\ReportsWidget as ReportsWidgetWidget;

use yii\base\Event;

/**
 * Class Reports
 *
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 *
 * @property  EmailService  $email
 * @property  ReportService $report
 * @property  ChartService  $chart
 * @property  WidgetService $widget
 * @property  Target        $target
 *
 * @method Settings getSettings()
 */
class Reports extends Plugin
{
    use ServicesTrait;
    use UserPermissionsTrait;

    public const PERMISSION_RUN_REPORTS = 'reports:runReports';
    public const PERMISSION_MANAGE_REPORTS = 'reports:manageReports';
    public const PERMISSION_MANAGE_TARGETS = 'reports:manageExportTargets';

    public static $plugin;
    public string $schemaVersion = '1.0.3';
    public bool $hasCpSettings = true;
    public bool $hasCpSection = true;

    public function getPluginName()
    {
        return Craft::t('reports', $this->getSettings()->pluginName);
    }

    /**
     * @return mixed[]
     */
    public function getCpNavItem(): array
    {
        $navItem = parent::getCpNavItem();
        $navItem['label'] = $this->getPluginName();

        if (Craft::$app->getUser()->checkPermission(self::PERMISSION_MANAGE_REPORTS) || Craft::$app->getUser()->checkPermission(self::PERMISSION_RUN_REPORTS)) {
            $navItem['subnav']['reports'] = [
                'label' => Craft::t('reports', 'Reports'),
                'url' => 'reports',
            ];
        }

        if (Craft::$app->getUser()->checkPermission(self::PERMISSION_MANAGE_TARGETS)) {
            $navItem['subnav']['targets'] = [
                'label' => Craft::t('reports', 'Report Targets'),
                'url' => 'reports/targets',
            ];
        }

        return $navItem;
    }

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        $this->initServices();
        $this->initPermissions();
        $this->initEventListeners();

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'superbig\reports\console\controllers';
        }
    }

    public function initEventListeners(): void
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            static function (RegisterUrlRulesEvent $event) : void {
                $event->rules['reports/schedule/run'] = 'reports/schedule/run';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event): void {
                $event->rules = array_merge($event->rules, $this->getCpRoutes());
            }
        );


        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            static function (Event $event) : void {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('reports', ReportsVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function(PluginEvent $event): void {
                if ($event->plugin === $this) {
                }
            }
        );

        // @todo: Add Widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            static function (RegisterComponentTypesEvent $event) : void {
                $event->types[] = ReportsWidgetWidget::class;
            }
        );
    }

    public function getCpRoutes(): array
    {
        return [
            'reports' => 'reports/reports/index',
            'reports/edit/<id:\d+>' => 'reports/reports/edit',
            'reports/run/<id:\d+>' => 'reports/reports/run',
            'reports/export/<id:\d+>' => 'reports/reports/export',
            'reports/new' => 'reports/reports/new',

            // Targets
            'reports/targets' => 'reports/targets/index',
            'reports/targets/edit/<id:\d+>' => 'reports/targets/edit',
            'reports/targets/run/<id:\d+>' => 'reports/targets/run',
            'reports/targets/new' => 'reports/targets/new',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): \superbig\reports\models\Settings
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'reports/settings',
            [
                'settings' => $this->getSettings(),
            ]
        );
    }
}
