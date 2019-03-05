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

use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use League\Csv\Writer;
use superbig\reports\Reports;

use Craft;
use craft\base\Component;
use yii\base\Exception;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Export extends Component
{
    // Public Methods
    // =========================================================================

    public function csv(\superbig\reports\models\Report $report)
    {
        // @todo Check if successful
        $result   = Reports::$plugin->getReport()->runReport($report);
        $filename = $result->getFilename() . '-' . date('YmdHis') . '.csv';
        $csv      = Writer::createFromString('');

        if (!empty($result->getHeader())) {
            $csv->insertOne($result->getHeader());

        }

        if (!empty($result->getContent())) {
            $csv->insertAll($result->getContent());
        }

        $mimeType = 'text/csv';
        $path     = $this->_write((string)$csv, $filename, $mimeType);

        return [
            'filename' => $filename,
            'path'     => $path,
            'mimeType' => $mimeType,
        ];
    }

    private function _write($content, $filename, $mimeType): string
    {
        $tempPath     = Craft::$app->path->getTempPath() . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR;
        $tempFilename = StringHelper::randomString(12) . "-{$filename}";
        $config       = [
            'filename'     => $filename,
            'tempFilename' => $tempFilename,
            'mimeType'     => $mimeType,
        ];

        $path = $tempPath . $tempFilename;
        FileHelper::writeToFile($path, $content);

        return $path;
    }
}
