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

use craft\db\Query;
use craft\web\twig\TemplateLoaderException;
use superbig\reports\models\ReportTarget as ReportTargetModel;
use superbig\reports\records\TargetRecord;

use Craft;
use craft\base\Component;
use superbig\reports\targets\EmailTarget;
use superbig\reports\targets\ReportTarget;
use superbig\reports\targets\ReportTargetInterface;
use yii\db\Exception;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Target extends Component
{
    private $_targets;

    // Public Methods
    // =========================================================================

    public function getTargetTypes(): array
    {
        return [
            EmailTarget::class,
        ];
    }

    /**
     * @param $config
     *
     * @return null|ReportTarget
     */
    public function createTargetType($config)
    {
        if (\is_string($config)) {
            $config = ['type' => $config];
        }
        try {
            /** @var ReportTarget $target */
            $target = \craft\helpers\Component::createComponent($config, ReportTargetInterface::class);
        } catch (\Throwable $e) {
            $target = null;
            Craft::error($e->getMessage(), __METHOD__);
        }

        return $target;
    }


    /**
     * @param null $id
     *
     * @return null|ReportTargetModel
     */
    public function getReportTargetById($id = null)
    {
        $query = $this
            ->_createQuery()
            ->where(['id' => $id])
            ->one();

        if (!$query) {
            return null;
        }

        return new ReportTargetModel($query);
    }

    /**
     * @return ReportTargetModel[]
     */
    public function getAllReportTargets(): array
    {
        if (!$this->_targets) {
            $this->_targets = [];

            $query = $this
                ->_createQuery()
                ->all();

            foreach ($query as $row) {
                $this->_targets[] = new ReportTargetModel($row);
            }
        }

        return $this->_targets;
    }

    /**
     * @param null $id
     *
     * @return array
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function runReportTarget($id = null): ReportTargetResult
    {
        $report              = $this->getReportTargetById($id);
        $report->dateLastRun = new \DateTime();

        // @todo try/catch and return error
        $this->saveReportTarget($report);
        $result = new ReportTargetResult();

        $view            = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();
        $view->setTemplateMode($view::TEMPLATE_MODE_SITE);

        try {
            // Render template and allow data and settings to be set in Twig
            $view->renderString($report->content, ['result' => $result]);
            //$view->renderString($report->settings, ['result' => $result]);
        } catch (TemplateLoaderException $e) {
            $error = Craft::t(
                'reports',
                "Template Error: {error}\n{trace}",
                [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            $result->addError('content', $error);
        } catch (\Exception $e) {
            $error = Craft::t(
                'reports',
                'Template Error: {error}',
                [
                    'error' => $e->getMessage(),
                ]
            );
            $result->addError('content', $error);
        }

        $view->setTemplateMode($oldTemplateMode);

        return $result;
    }

    /**
     * @param ReportTargetModel $report
     *
     * @return bool
     * @throws Exception
     */
    public function saveReportTarget(ReportTargetModel $report): bool
    {

        if ($report->id) {
            $record = TargetRecord::findOne($report->id);

            if (!$record->id) {
                $error = Craft::t(
                    'reports',
                    'No report exists with the id {id}',
                    ['id' => $report->id]
                );

                throw new Exception($error);
            }
        }
        else {
            $record = new TargetRecord();
        }

        $record->id          = $report->id;
        $record->name        = $report->name;
        $record->handle      = $report->handle;
        $record->targetClass = $report->targetClass;
        $record->settings    = $report->settings;
        $db                  = Craft::$app->getDb();
        $transaction         = $db->beginTransaction();

        try {
            $record->save(false);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    /**
     * @param null $id
     *
     * @return bool
     */
    public function deleteReportTarget($id = null): bool
    {
        return (bool)TargetRecord::deleteAll('id = :id', [':id' => $id]);
    }

    /**
     * @return Query
     */
    public function _createQuery(): Query
    {
        return (new Query())
            ->from(TargetRecord::tableName())
            ->select([
                'id',
                'name',
                'handle',
                'settings',
                'targetClass',
            ]);
    }

    public function getDefaultTargetType()
    {
        return $this->getTargetTypes()[0];
    }
}
