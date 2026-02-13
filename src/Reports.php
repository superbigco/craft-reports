<?php

declare(strict_types=1);

namespace superbig\reports;

use Craft;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Dashboard;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use superbig\reports\models\Settings;
use superbig\reports\variables\ReportsVariable;
use superbig\reports\widgets\ReportsWidget as ReportsWidgetWidget;
use yii\base\Event;

/**
 * @property  services\Email  $email
 * @property  services\Report $report
 * @property  services\Export $export
 * @property  services\Chart  $chart
 * @property  services\Widget $widget
 * @property  services\Target $target
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

    public string $schemaVersion = '1.0.3';
    public bool $hasCpSettings = true;
    public bool $hasCpSection = true;

    public function getPluginName(): string
    {
        return Craft::t('reports', $this->getSettings()->pluginName);
    }

    public function init(): void
    {
        parent::init();

        $this->initServices();
        $this->initPermissions();
        $this->initEventListeners();

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'superbig\\reports\\console\\controllers';
        }
    }

    public function initEventListeners(): void
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            static function (RegisterUrlRulesEvent $event): void {
                $event->rules['reports/schedule/run'] = 'reports/schedule/run';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event): void {
                $event->rules = array_merge($event->rules, $this->getCpRoutes());
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            static function (Event $event): void {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('reports', ReportsVariable::class);
            }
        );

        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            static function (RegisterComponentTypesEvent $event): void {
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

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

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
