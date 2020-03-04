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
use superbig\reports\models\ReportTarget as ReportTargetModel;

use superbig\reports\records\ReportsTargetsRecord;
use superbig\reports\records\TargetRecord;
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
    /**
     * @var mixed[]|ReportTargetModel[]|null
     */
    private ?array $_targets = null;

    // Public Methods
    // =========================================================================
    /**
     * @return array<class-string<\superbig\reports\targets\EmailTarget>>
     */
    public function getTargetTypes(): array
    {
        return [
            EmailTarget::class,
        ];
    }

    public function createTargetType(string | array $config): ?\superbig\reports\targets\ReportTarget
    {
        if (\is_string($config)) {
            $config = ['type' => $config];
        }

        try {
            /** @var ReportTarget $target */
            $target = \craft\helpers\Component::createComponent($config, ReportTargetInterface::class);
        } catch (\Throwable $throwable) {
            $target = null;
            Craft::error($throwable->getMessage(), __METHOD__);
        }

        return $target;
    }


    /**
     * @return null|ReportTargetModel
     */
    public function getReportTargetById(int $id = null): ?ReportTargetModel
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
     * @param string $handle
     *
     * @return null|ReportTargetModel
     */
    public function getReportTargetByHandle($handle = null): ?ReportTargetModel
    {
        $query = $this
            ->_createQuery()
            ->where(['handle' => $handle])
            ->one();

        if (!$query) {
            return null;
        }

        return new ReportTargetModel($query);
    }

    /**
     * @return array<int, ReportTargetModel>
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
     * @return array<int, ReportTarget>
     */
    public function getConnectedTargetsForReport(\superbig\reports\models\Report $report): array
    {
        if (!$report->id) {
            return [];
        }

        $targetIds = (new Query())
            ->select('targetId')
            ->from(ReportsTargetsRecord::tableName())
            ->where(
                '[[reportId]] = :reportId',
                [':reportId' => $report->id]
            )
            ->column();

        return array_filter(
            $this->getAllReportTargets(),
            static fn(ReportTargetModel $reportTarget): bool => \in_array($reportTarget->id, $targetIds, false));
    }

    /**
     * @return mixed[]
     */
    public function getConnectedReportIds(ReportTargetModel $target): array
    {
        return (new Query())
            ->select('reportId')
            ->from(ReportsTargetsRecord::tableName())
            ->where(
                '[[targetId]] = :targetId',
                [':targetId' => $target->id]
            )
            ->column();
    }

    /**
     * @return mixed[]
     */
    public function getConnectedReportsForTarget(\superbig\reports\models\ReportTarget $target): array
    {
        if (!$target->id) {
            return [];
        }

        $reportIds = (new Query())
            ->select('reportId')
            ->from(ReportsTargetsRecord::tableName())
            ->where(
                '[[targetId]] = :targetId',
                [':targetId' => $target->id]
            )
            ->column();

        return array_filter(
            Reports::$plugin->getReport()->getAllReports(),
            static fn(\superbig\reports\models\Report $report): bool => \in_array($report->id, $reportIds, false));
    }

    public function syncTargetReportRelationship(ReportTargetModel $target, array $reportIds = []): void
    {
        // Delete existing relationships
        (new Query())
            ->createCommand()
            ->delete(
                ReportsTargetsRecord::tableName(),
                '[[targetId]] = :targetId',
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
     * @throws \yii\base\Exception
     */
    public function runReportTarget(int $id = null): bool
    {
        $target = $this->getReportTargetById($id);
        $connectedReports = $this->getConnectedReportsForTarget($target);
        $targetType = $target->getTargetType();

        return $targetType->send($target, $connectedReports);
    }

    /**
     * @throws Exception
     */
    public function saveReportTarget(ReportTargetModel $report): bool
    {
        if ($report->id) {
            $record = TargetRecord::findOne($report->id);

            if ($record->id === 0) {
                $error = Craft::t(
                    'reports',
                    'No report exists with the id {id}',
                    ['id' => $report->id]
                );

                throw new Exception($error);
            }
        } else {
            $record = new TargetRecord();
        }

        $record->name        = $report->name;
        $record->handle      = $report->handle;
        $record->targetClass = $report->targetClass;
        $record->settings = $report->settings;
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
    public function deleteReportTarget($id = null): bool
    {
        return (bool)TargetRecord::deleteAll('[[id]] = :id', [':id' => $id]);
    }

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
