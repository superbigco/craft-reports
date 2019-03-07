<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\console\controllers;

use superbig\reports\Reports;

use Craft;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use superbig\reports\records\ReportsTargetsRecord;

/**
 * Default Command
 *
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class DefaultController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Run a report target by handle/id
     *
     * @param $id
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionRunTarget($id)
    {
        if (\intval($id)) {
            $target = Reports::$plugin->getTarget()->getReportTargetById($id);
        }
        else {
            $target = Reports::$plugin->getTarget()->getReportTargetByHandle($id);
        }

        if (!$target) {
            return ExitCode::NOINPUT;
        }

        $result = Reports::$plugin->getTarget()->runReportTarget($target->id);

        if (!$result) {
            $error = 'Failed to run ' . $target->name . PHP_EOL;
            Console::stdout($this->ansiFormat($error, Console::FG_RED));

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $notice = 'Successfully ran ' . $target->name;
        Console::stdout($this->ansiFormat($notice, Console::FG_GREEN));

        return ExitCode::OK;
    }
}
