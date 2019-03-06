<?php

namespace superbig\reports\migrations;

use Craft;
use craft\db\Migration;
use craft\db\mysql\ColumnSchema;
use superbig\reports\records\ReportsTargetsRecord;

require_once __DIR__ . '/m190305_191905_add_targets_reports_relationship.php';

/**
 * m190306_140704_fix_double_table migration.
 */
class m190306_140704_fix_double_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchema = Craft::$app->db->schema->getTableSchema(ReportsTargetsRecord::tableName());
        if ($tableSchema !== null) {
            if (isset($tableSchema->columns['dateLastRun'])) {
                $migration = new m190305_191905_add_targets_reports_relationship();

                $this->dropTableIfExists(ReportsTargetsRecord::tableName());
                $migration->safeUp();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190306_140704_fix_double_table cannot be reverted.\n";

        return false;
    }
}
