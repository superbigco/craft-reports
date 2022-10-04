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
    // Public Properties
    // =========================================================================

    /**
     * @var int The target ID
     */
    public $targetId;

    /**
     * @var ReportTarget The target
     */
    private $_target;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        App::maxPowerCaptain();

        $updateProgress = function($step) use ($queue) {
            $this->setProgress($queue, $step);
        };

        $target = Reports::$plugin->getTarget()->getReportTargetById($this->targetId);
        $result = Reports::$plugin->getTarget()->runReportTarget($this->targetId);

        if (!$result) {
            throw new Exception('Failed to run export target');
        }

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        $targetName = $this->getTarget() ? $this->getTarget()->name : 'Reports Target';

        return Craft::t('reports', $targetName);
    }

    /**
     * @return ReportTarget|null
     */
    public function getTarget()
    {
        if (!$this->_target) {
            $this->_target = Reports::$plugin->getTarget()->getReportTargetById($this->targetId);
        }

        return $this->_target;
    }
}
