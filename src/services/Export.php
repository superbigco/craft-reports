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

use Craft;
use craft\base\Component;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;

use League\Csv\Writer;
use superbig\reports\Reports;
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
    /**
     *
     * @throws Exception
     * @throws \yii\db\Exception
     * @return array<string, string>
     */
    public function csv(\superbig\reports\models\Report $report): array
    {
        // @todo Check if successful
        $result = $report->run();
        $filename = $result->getFilename() . '-' . date('YmdHis') . '.csv';
        $csv = Writer::createFromString('');

        if (!empty($result->getHeader())) {
            $csv->insertOne($result->getHeader());
        }

        if (!empty($result->getContent())) {
            $csv->insertAll($result->getContent());
        }

        // @todo Remove this once all plugins is using 9.0
        $content = method_exists($csv, 'getContent') ? $csv->getContent() : (string)$csv;
        $mimeType = 'text/csv';
        $path = $this->_write($content, $filename);

        return [
            'filename' => $filename,
            'path' => $path,
            'mimeType' => $mimeType,
        ];
    }

    private function _write(string $content, $filename): string
    {
        $tempPath = Craft::$app->path->getTempPath() . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR;
        $tempFilename = StringHelper::randomString(12) . sprintf('-%s', $filename);

        $path = $tempPath . $tempFilename;
        FileHelper::writeToFile($path, $content);

        return $path;
    }
}
