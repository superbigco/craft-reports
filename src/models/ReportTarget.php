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
use craft\helpers\Json;

use superbig\reports\Reports;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportTarget extends Model
{
    // Public Properties
    // =========================================================================

    public $id;

    public $name;

    public $handle;

    public $targetClass;

    public $settings;

    // Public Methods
    // =========================================================================

    public function init(): void
    {
        if (!$this->targetClass) {
            $this->targetClass = Reports::$plugin->getTarget()->getDefaultTargetType();
        }

        if (\is_string($this->settings)) {
            $this->settings = Json::decodeIfJson($this->settings);
        }
    }

    public function getTargetType(): ?\superbig\reports\targets\ReportTarget
    {
        $selectedDefinition = array_merge(
            $this->settings[ $this->targetClass ] ?? [],
            ['type' => $this->targetClass]
        );

        return Reports::$plugin->getTarget()->createTargetType($selectedDefinition);
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [];
    }
}
