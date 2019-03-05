<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\services;

use superbig\reports\Reports;

use Craft;
use craft\base\Component;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Export extends Component
{
    // Public Methods
    // =========================================================================

    public function exportCsv($id = null)
    {

        if (!$id) {
            Craft::$app->end();
        }

        $report          = $this->getReportById($id);
        $report->lastRun = new \DateTime();
        $this->saveReport($report);
        $data = $this->parseReport($report);
        // Craft::dd($data);
        // Prepare and write temp file to disk
        FileHelper::createDirectory(Craft::$app->getPath()->getTempPath() . DIRECTORY_SEPARATOR . 'craft-reports');
        $fileName = $report->type . "-" . date("YmdHis") . ".csv";
        $tempFile = Craft::$app->getPath()->getTempPath() . DIRECTORY_SEPARATOR . 'craft-reports' . DIRECTORY_SEPARATOR . $fileName;
        if (($handle = fopen($tempFile, 'wb')) === false) {
            throw new Exception('Could not create temp file: ' . $tempFile);
        }
        fclose($handle);
        $csv = Writer::createFromPath(new \SplFileObject($tempFile, 'a+'), 'w');
        if (isset($data['columns'])) {
            $csv->insertOne($data['columns']);
        }
        foreach ($data['rows'] as $row) {
            $csv->insertOne($row);
        }
        // send email with attachment
        Reports::$plugin->emails->sendEmail($fileName, $report->email);
        unlink($tempFile);
    }
}
