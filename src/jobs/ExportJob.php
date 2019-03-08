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

use craft\helpers\App;
use superbig\reports\Reports;

use Craft;
use craft\queue\BaseJob;
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
     * @var int The target
     */
    public $targetId;

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
        return Craft::t('reports', 'Reports Target');
    }
}
