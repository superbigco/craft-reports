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

use craft\base\Model;

use superbig\reports\Reports;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $enableScheduler = true;
    public $helpers = [];
    public $pluginName = 'Reports';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }
}
