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
use superbig\reports\records\ReportsTargetsRecord;
use superbig\reports\records\TargetRecord;

use Craft;
use craft\base\Component;
use superbig\reports\Reports;
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

    public function getConnectedTargetsForReport(\superbig\reports\models\Report $report): array
    {
        if (!$report->id) {
            return [];
        }

        $targetIds = (new Query())
            ->select('targetId')
            ->from(ReportsTargetsRecord::tableName())
            ->where(
                'reportId = :reportId',
                [':reportId' => $report->id]
            )
            ->column();

        return array_filter(
            $this->getAllReportTargets(),
            function(ReportTargetModel $reportTarget) use ($targetIds) {
                return \in_array($reportTarget->id, $targetIds, false);
            });
    }

    public function getConnectedReportIds(ReportTargetModel $target): array
    {
        return (new Query())
            ->select('reportId')
            ->from(ReportsTargetsRecord::tableName())
            ->where(
                'targetId = :targetId',
                [':targetId' => $target->id]
            )
            ->column();
    }

    public function getConnectedReportsForTarget(\superbig\reports\models\ReportTarget $target): array
    {
        if (!$target->id) {
            return [];
        }

        $reportIds = (new Query())
            ->select('reportId')
            ->from(ReportsTargetsRecord::tableName())
            ->where(
                'targetId = :targetId',
                [':targetId' => $target->id]
            )
            ->column();

        return array_filter(
            Reports::$plugin->getReport()->getAllReports(),
            function(\superbig\reports\models\Report $report) use ($reportIds) {
                return \in_array($report->id, $reportIds, false);
            });
    }

    public function syncTargetReportRelationship(ReportTargetModel $target, array $reportIds = [])
    {
        // Delete existing relationships
        (new Query())
            ->createCommand()
            ->delete(
                ReportsTargetsRecord::tableName(),
                'targetId = :targetId',
                [':targetId' => $target->id]
            )
            ->execute();

        foreach ($reportIds as $reportId) {
            (new Query())
                ->createCommand()
                ->insert(
                    ReportsTargetsRecord::tableName(),
                    [
                        'targetId' => $target->id,
                        'reportID' => $reportId,
                    ]
                )
                ->execute();
        }
    }

    /**
     * @param null $id
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public function runReportTarget($id = null): bool
    {
        $target           = $this->getReportTargetById($id);
        $connectedReports = $this->getConnectedReportsForTarget($target);
        $targetType       = $target->getTargetType();

        return $targetType->send($target, $connectedReports);
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

            $report->id = $record->id;
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
