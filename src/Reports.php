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

use superbig\reports\services\Email as EmailService;
use superbig\reports\services\Report as ReportService;
use superbig\reports\services\Chart as ChartService;
use superbig\reports\services\Target;
use superbig\reports\services\Widget as WidgetService;
use superbig\reports\variables\ReportsVariable;
use superbig\reports\twigextensions\ReportsTwigExtension;
use superbig\reports\models\Settings;
use superbig\reports\widgets\ReportsWidget as ReportsWidgetWidget;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

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

    // Static Properties
    // =========================================================================

    const PERMISSION_RUN_REPORTS    = 'reports:runReports';
    const PERMISSION_MANAGE_REPORTS = 'reports:manageReports';
    const PERMISSION_MANAGE_TARGETS = 'reports:manageExportTargets';

    /**
     * @var Reports
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    public $schemaVersion = '1.0.2';
    public $hasCpSettings = true;
    public $hasCpSection  = true;

    // Public Methods
    // =========================================================================

    public function getPluginName()
    {
        return Craft::t('reports', $this->getSettings()->pluginName);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->initServices();
        $this->initPermissions();
        $this->initEventListeners();

        Craft::$app->view->registerTwigExtension(new ReportsTwigExtension());

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'superbig\reports\console\controllers';
        }
    }

    public function initEventListeners()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['reports/schedule/run'] = 'reports/schedule/run';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules = array_merge($event->rules, $this->getCpRoutes());
            }
        );


        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('reports', ReportsVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'reports',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        // @todo: Add Widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = ReportsWidgetWidget::class;
            }
        );
    }

    public function getCpRoutes(): array
    {
        return [
            'reports'                       => 'reports/reports/index',
            'reports/edit/<id:\d+>'         => 'reports/reports/edit',
            'reports/run/<id:\d+>'          => 'reports/reports/run',
            'reports/export/<id:\d+>'       => 'reports/reports/export',
            'reports/new'                   => 'reports/reports/new',

            // Targets
            'reports/targets'               => 'reports/targets/index',
            'reports/targets/edit/<id:\d+>' => 'reports/targets/edit',
            'reports/targets/run/<id:\d+>'  => 'reports/targets/run',
            'reports/targets/new'           => 'reports/targets/new',
        ];
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
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
