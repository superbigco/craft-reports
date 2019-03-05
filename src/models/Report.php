<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\models;

use superbig\reports\Reports;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Report extends Model
{
    // Public Properties
    // =========================================================================

    public $id;
    public $siteId;
    public $name;
    public $handle;
    public $content;
    public $settings;
    public $dateLastRun;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'settings'], 'string'],
        ];
    }
}
