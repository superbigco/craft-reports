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
class ReportSettings extends Model
{
    // Public Properties
    // =========================================================================

    /**  @var Fields */
    public $fields;

    // Public Methods
    // =========================================================================

    public function init()
    {
        if (!$this->fields) {
            $this->fields = new Fields();
        }
    }

    /**
     * @return Fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    public function hasFields()
    {
        return count($this->getFields()->fields) > 0;
    }
}
