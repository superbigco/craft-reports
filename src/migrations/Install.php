<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\migrations;

use Craft;
use craft\db\Migration;
use superbig\reports\records\ReportsRecord;

use superbig\reports\records\ReportsTargetsRecord;
use superbig\reports\records\TargetRecord;
use superbig\reports\Reports;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            //$this->createIndexes();
            //$this->addForeignKeys();

            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema(ReportsRecord::tableName());
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                ReportsRecord::tableName(),
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    'name' => $this->string(255)->notNull()->defaultValue(''),
                    'handle' => $this->string(255)->notNull()->defaultValue(''),
                    'content' => $this->longText(),
                    'settings' => $this->longText(),
                    'fieldValues' => $this->longText(),
                    'dateLastRun' => $this->dateTime()->null(),
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(TargetRecord::tableName());
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                TargetRecord::tableName(),
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'name' => $this->string(255)->notNull()->defaultValue(''),
                    'handle' => $this->string(255)->notNull()->defaultValue(''),
                    'targetClass' => $this->string(255)->notNull()->defaultValue(''),
                    'settings' => $this->text(),
                ]
            );
        }

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
        }

        return $tablesCreated;
    }

    protected function createIndexes(): void
    {
        $this->createIndex(
            null,
            ReportsRecord::tableName(),
            'some_field',
            true
        );
    }

    protected function addForeignKeys(): void
    {
        $this->addForeignKey(
            null,
            ReportsRecord::tableName(),
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            null,
            ReportsTargetsRecord::tableName(),
            'reportId',
            ReportsRecord::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            null,
            ReportsTargetsRecord::tableName(),
            'targetId',
            TargetRecord::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists(ReportsRecord::tableName());
        $this->dropTableIfExists(TargetRecord::tableName());
        $this->dropTableIfExists(ReportsTargetsRecord::tableName());
    }
}
