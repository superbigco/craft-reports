<?php

namespace superbig\reports\migrations;

use Craft;
use craft\db\Migration;
use superbig\reports\records\ReportsRecord;

/**
 * m191020_204608_add_fieldvalues migration.
 */
class m191020_204608_add_fieldvalues extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(ReportsRecord::tableName(), 'fieldValues', $this->longText()->after('settings'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m191020_204608_add_fieldvalues cannot be reverted.\n";

        return false;
    }
}
