<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\variables;

use superbig\reports\Reports;

use superbig\reports\targets\ReportTargetInterface;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportsVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param string|array $config
     *
     * @return ReportTargetInterface
     */
    public function createTargetType($config): ReportTargetInterface
    {
        return Reports::$plugin->getTarget()->createTargetType($config);
    }
}
