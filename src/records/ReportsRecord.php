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

use superbig\reports\Reports;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 *
 * @property int       $id
 * @property int       $siteId
 * @property string    $name
 * @property string    $handle
 * @property string    $content
 * @property string    $settings
 * @property \DateTime $dateLastRun
 */
class ReportsRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_reports}}';
    }
}
