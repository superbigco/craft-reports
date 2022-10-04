<?php

namespace superbig\reports\migrations;

use Craft;
use craft\db\Migration;
use superbig\reports\records\ReportsRecord;
use superbig\reports\records\ReportsTargetsRecord;
use superbig\reports\records\TargetRecord;

/**
 * m190305_191905_add_targets_reports_relationship migration.
 */
class m190305_191905_add_targets_reports_relationship extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchema = Craft::$app->db->schema->getTableSchema(ReportsTargetsRecord::tableName());
        if ($tableSchema === null) {
            $this->createTable(
                ReportsTargetsRecord::tableName(),
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'reportId' => $this->integer()->notNull(),
                    'targetId' => $this->integer()->notNull(),
                ]
            );

            $this->addForeignKey(
                $this->db->getForeignKeyName(ReportsTargetsRecord::tableName(), 'reportId'),
                ReportsTargetsRecord::tableName(),
                'reportId',
                ReportsRecord::tableName(),
                'id',
                'CASCADE',
                'CASCADE'
            );

            $this->addForeignKey(
                $this->db->getForeignKeyName(ReportsTargetsRecord::tableName(), 'targetId'),
                ReportsTargetsRecord::tableName(),
                'targetId',
                TargetRecord::tableName(),
                'id',
                'CASCADE',
                'CASCADE'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190305_191905_add_targets_reports_relationship cannot be reverted.\n";

        return false;
    }
}
