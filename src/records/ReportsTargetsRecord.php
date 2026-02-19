<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\records;

use craft\db\ActiveRecord;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 *
 * @property int    $id
 * @property string $targetId
 * @property string $reportId
 */
class ReportsTargetsRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%reports_targets_reports}}';
    }
}
