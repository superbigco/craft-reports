<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\jobs;

use Craft;
use craft\helpers\App;
use craft\queue\BaseJob;

use superbig\reports\models\ReportTarget;
use superbig\reports\Reports;
use yii\base\Exception;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ExportJob extends BaseJob
{
    public int $targetId;
    private ReportTarget $_target;

    public function execute($queue): void
    {
        App::maxPowerCaptain();

        $updateProgress = function($step) use ($queue) {
            $this->setProgress($queue, $step);
        };

        // $target = Reports::getInstance()->getTarget()->getReportTargetById($this->targetId);
        $result = Reports::getInstance()->getTarget()->runReportTarget($this->targetId);

        if (!$result) {
            throw new Exception('Failed to run export target');
        }
    }

    protected function defaultDescription(): string
    {
        $targetName = $this->getTarget() ? $this->getTarget()->name : 'Reports Target';

        return Craft::t('reports', $targetName);
    }

    public function getTarget(): ReportTarget|null
    {
        if (!$this->_target) {
            $this->_target = Reports::getInstance()->getTarget()->getReportTargetById($this->targetId);
        }

        return $this->_target;
    }
}
