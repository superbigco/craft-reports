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

use superbig\reports\Reports;

use Craft;
use craft\queue\BaseJob;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportsTask extends BaseJob
{
    // Public Properties
    // =========================================================================

    /**
     * @var int The report to handle
     */
    public $reportId;

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
        // @todo Execute target
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('reports', 'Reports Export');
    }
}
