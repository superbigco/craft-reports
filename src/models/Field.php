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

use craft\helpers\DateTimeHelper;
use craft\helpers\Template;
use superbig\reports\Reports;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 *
 */
class Field extends Model
{
    // Public Properties
    // =========================================================================

    const TYPE_TEXT            = 'textField';
    const TYPE_DATE            = 'dateField';
    const TYPE_TIME            = 'timeField';
    const TYPE_DATETIME        = 'dateTimeField';
    const TYPE_SELECT          = 'selectField';
    const TYPE_MULTISELECT     = 'multiselectField';
    const TYPE_COLOR           = 'colorField';
    const TYPE_TEXTAREA        = 'textareaField';
    const TYPE_CHECKBOX        = 'checkboxField';
    const TYPE_CHECKBOX_GROUP  = 'checkboxGroupField';
    const TYPE_CHECKBOX_SELECT = 'checkboxSelectField';
    const TYPE_RADIO_GROUP     = 'radioGroupField';
    const TYPE_LIGHTSWITCH     = 'lightswitchField';
    const TYPE_EDITABLE_TABLE  = 'editableTableField';
    const TYPE_ELEMENT_SELECT  = 'elementSelectField';
    const TYPE_AUTOSUGGEST     = 'autosuggestField';

    public $first  = false;
    public $config = [];
    public $label;
    public $name;
    public $instructions;
    public $type   = self::TYPE_TEXT;
    public $value;
    public $placeholder;
    public $fieldLabel;
    public $defaultValue;
    public $warning;
    public $options;
    public $labelId;

    public static $arrayFieldtypes = [
        self::TYPE_CHECKBOX_GROUP,
        self::TYPE_RADIO_GROUP,
        self::TYPE_EDITABLE_TABLE,
        self::TYPE_MULTISELECT,
        self::TYPE_ELEMENT_SELECT,
    ];
    public static $dateFieldtypes  = [
        self::TYPE_DATE,
        self::TYPE_TIME,
        self::TYPE_DATETIME,
    ];

    // Public Methods
    // =========================================================================

    public function init()
    {
        if (empty($this->config['value']) && isset($this->config['defaultValue'])) {
            $this->config['value'] = $this->config['defaultValue'];
            $this->defaultValue    = $this->config['defaultValue'];
        }

        $this->config['id'] = $this->config['name'];

        $g = [
            'label'        => $this->label,
            'name'         => $this->name,
            'id'           => $this->name,
            'value'        => $this->value,
            'placeholder'  => $this->placeholder,
            'options'      => $this->options,
            'warning'      => $this->warning,
            'labelId'      => $this->labelId,
            //'errors' => $this->getErrors('placeholder'),
            'instructions' => $this->instructions,
        ];
    }

    public function renderField(Report $report)
    {
        $currentValue =
        $html = Craft::$app->getView()->renderTemplateMacro('_includes/forms', $this->type, [
            'config' => $this->config,
        ]);

        return Template::raw($html);
    }

    public function getHandle()
    {
        return $this->config['name'] ?? null;
    }

    public function getType()
    {
        return $this->type ?? self::TYPE_TEXT;
    }

    public function normalizeValue($value)
    {
        if (in_array($this->getType(), self::$arrayFieldtypes) && !is_array($value)) {
            return (array)$value;
        }

        if (in_array($this->getType(), self::$dateFieldtypes) && $value && ($date = DateTimeHelper::toDateTime($value)) !== false) {
            return $date;
        }

        return $value;
    }

    public function rules()
    {
        $rules = parent::rules();

        return $rules;
    }
}
