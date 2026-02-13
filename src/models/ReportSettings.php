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
class ReportSettings extends Model
{
    // Public Properties
    // =========================================================================

    public ?Fields $fields = null;

    // Public Methods
    // =========================================================================

    public function init(): void
    {
        if (!$this->fields) {
            $this->fields = new Fields();
        }
    }

    public function getFields(): \superbig\reports\models\Fields
    {
        return $this->fields;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [];
    }

    public function hasFields(): bool
    {
        return count($this->getFields()->fields) > 0;
    }
}
