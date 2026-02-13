<?php

declare(strict_types=1);

namespace superbig\reports\models;

use Craft;
use craft\base\Model;
use superbig\reports\Reports;

class Report extends Model
{
    public ?int $id = null;
    public ?int $siteId = null;
    public string $name = '';
    public string $handle = '';
    public string $content = '';
    public string $settings = '';
    public mixed $fieldValues = null;
    public ?\DateTime $dateLastRun = null;

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
        return Reports::getInstance()->getReport()->runReport($this);
    }

    public function reportSettings(): ReportSettings
    {
        return Reports::getInstance()->getReport()->settingsForReport($this);
    }

    /**
     * @return ReportTarget[]
     */
    public function getConnectedTargets(): array
    {
        return $this->_targets ??= Reports::getInstance()->getTarget()->getConnectedTargetsForReport($this);
    }

    public function canManage(): bool
    {
        return Craft::$app->getUser()->checkPermission(Reports::PERMISSION_MANAGE_REPORTS);
    }
}
