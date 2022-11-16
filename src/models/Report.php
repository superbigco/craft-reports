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
    public $name;
    public $handle;
    public $content;
    public $settings;
    public $fieldValues;
    public $dateLastRun;
    private $_targets;
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
        if (!$this->_targets) {
            $this->_targets = Reports::$plugin->getTarget()->getConnectedTargetsForReport($this);
        }

        return $this->_targets;
    }

    public function canManage()
    {
        return Craft::$app->getUser()->checkPermission(Reports::PERMISSION_MANAGE_REPORTS);
    }
}
