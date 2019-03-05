<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\targets;

use craft\base\SavableComponentInterface;
use superbig\reports\models\Report;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 *
 */
interface ReportTargetInterface extends SavableComponentInterface
{
    /**
     * @param \superbig\reports\models\ReportTarget $target
     * @param Report[]                              $reports
     *
     * @return bool
     */
    public function send(\superbig\reports\models\ReportTarget $target, array $reports = []): bool;
}