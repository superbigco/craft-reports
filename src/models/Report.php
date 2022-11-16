<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\models;

use Craft;

use craft\base\Model;
use superbig\reports\Reports;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Report extends Model
{
    public $id;
    public $siteId;
    public string $name;
    public string $handle;
    public string $content;
    public string $settings;
    public $fieldValues;
    public \DateTime $dateLastRun;

    /** @var array<int, ReportTarget> */
    private array $_targets;

    public function rules(): array
    {
        return [
            [['content', 'settings'], 'string'],
        ];
    }

    public function run(): ReportResult
    {
        return Reports::$plugin->getReport()->runReport($this);
    }

    public function reportSettings()
    {
        return Reports::$plugin->getReport()->settingsForReport($this);
    }

    public function getConnectedTargets()
    {
        return $this->_targets ??= Reports::$plugin->getTarget()->getConnectedTargetsForReport($this);
    }

    public function canManage()
    {
        return Craft::$app->getUser()->checkPermission(Reports::PERMISSION_MANAGE_REPORTS);
    }
}
