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
use craft\db\Query;
use craft\web\twig\TemplateLoaderException;
use superbig\reports\models\Report as ReportModel;
use superbig\reports\models\ReportResult;

use superbig\reports\models\ReportSettings;
use superbig\reports\records\ReportsRecord;
use yii\db\Exception;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Report extends Component
{
    /**
     * @var mixed[]|ReportModel[]|null
     */
    private ?array $_reports = null;

    // Public Methods
    // =========================================================================

    /**
     * @param null $id
     *
     * @return ReportModel|null
     */
    public function getReportById($id = null): ?ReportModel
    {
        $query = $this
            ->_createQuery()
            ->where(['id' => $id])
            ->one();

        if (!$query) {
            return null;
        }

        return new ReportModel($query);
    }

    /**
     * @return ReportModel[]
     */
    public function getAllReports(): array
    {
        if (!$this->_reports) {
            $this->_reports = [];

            $query = $this
                ->_createQuery()
                ->all();

            foreach ($query as $row) {
                $this->_reports[] = new ReportModel($row);
            }
        }

        return $this->_reports;
    }

    /**
     * @param Report $report
     *
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function runReport(ReportModel $report): ReportResult
    {
        $report->dateLastRun = new \DateTime();

        // @todo try/catch and return error
        $this->saveReport($report);

        $result = new ReportResult();
        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();

        $view->setTemplateMode($view::TEMPLATE_MODE_SITE);

        try {
            // Render template and allow data and settings to be set in Twig
            $view->renderString($report->content, [
                'result' => $result,
                'report' => $report,
            ]);
            //$view->renderString($report->settings, ['result' => $result]);
        } catch (TemplateLoaderException $templateLoaderException) {
            $error = Craft::t(
                'reports',
                "Template Error: {error}\n{trace}",
                [
                    'error' => $templateLoaderException->getMessage(),
                    'trace' => $templateLoaderException->getTraceAsString(),
                ]
            );
            $result->addError('content', $error);
        } catch (\Exception $exception) {
            $error = Craft::t(
                'reports',
                'Template Error: {error}',
                [
                    'error' => $exception->getMessage(),
                ]
            );
            $result->addError('content', $error);
        }

        $view->setTemplateMode($oldTemplateMode);

        return $result;
    }

    /**
     *
     * @throws Exception
     */
    public function saveReport(ReportModel $report, bool $runValidation = true): bool
    {
        if ($report->id) {
            $record = ReportsRecord::findOne($report->id);

            if ($record->id === 0) {
                $error = Craft::t(
                    'reports',
                    'No report exists with the id {id}',
                    ['id' => $report->id]
                );

                throw new Exception($error);
            }
        } else {
            $record = new ReportsRecord();
        }

        if ($runValidation && !$report->validate()) {
            Craft::info('Report not saved due to validation error.', __METHOD__);

            return false;
        }

        $record->siteId = $report->siteId;
        $record->name = $report->name;
        $record->handle = $report->handle;
        $record->content = $report->content;
        $record->settings = $report->settings;
        $record->dateLastRun = $report->dateLastRun;
        $db = Craft::$app->getDb();
        $transaction = $db->beginTransaction();

        try {
            $record->save(false);
            $transaction->commit();

            $report->id = $record->id;
        } catch (\Exception $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        return true;
    }

    /**
     * @param null $id
     */
    public function deleteReport($id = null): bool
    {
        return (bool)ReportsRecord::deleteAll('[[id]] = :id', [':id' => $id]);
    }

    public function _createQuery(): Query
    {
        return (new Query())
            ->from(ReportsRecord::tableName())
            ->select([
                'id',
                'siteId',
                'name',
                'handle',
                'content',
                'settings',
                'dateLastRun',
            ]);
    }

    public function settingsForReport(ReportModel $report): \superbig\reports\models\ReportSettings
    {
        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();
        $reportSettings = new ReportSettings();

        $view->setTemplateMode($view::TEMPLATE_MODE_SITE);

        try {
            // Render template and allow data and settings to be set in Twig
            $view->renderString($report->settings, [
                'settings' => $reportSettings,
                'report' => $report,
            ]);
            //$view->renderString($report->settings, ['result' => $result]);
        } catch (\Exception $exception) {
            $error = Craft::t(
                'reports',
                'Setting Template Error: {error}',
                [
                    'error' => $exception->getMessage(),
                ]
            );
            $report->addError('settings', $error);
        }

        $view->setTemplateMode($oldTemplateMode);

        return $reportSettings;
    }
}
