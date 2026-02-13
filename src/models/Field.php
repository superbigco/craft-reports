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

use Craft;
use craft\base\Model;

use craft\helpers\Template;
use superbig\reports\Reports;

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

    public const TYPE_TEXT = 'textField';
    public const TYPE_DATE = 'dateField';
    public const TYPE_TIME = 'timeField';
    public const TYPE_DATETIME = 'dateTimeField';
    public const TYPE_SELECT = 'selectField';
    public const TYPE_MULTISELECT = 'multiselectField';
    public const TYPE_COLOR = 'colorField';
    public const TYPE_TEXTAREA = 'textareaField';
    public const TYPE_CHECKBOX = 'checkboxField';
    public const TYPE_CHECKBOX_GROUP = 'checkboxGroupField';
    public const TYPE_CHECKBOX_SELECT = 'checkboxSelectField';
    public const TYPE_RADIO_GROUP = 'radioGroupField';
    public const TYPE_LIGHTSWITCH = 'lightswitchField';
    public const TYPE_EDITABLE_TABLE = 'editableTableField';
    public const TYPE_ELEMENT_SELECT = 'elementSelectField';
    public const TYPE_AUTOSUGGEST = 'autosuggestField';

    public bool $first = false;
    public array $config = [];
    public string $label;
    public string $name;
    public $instructions;
    public string $type = self::TYPE_TEXT;
    public $value;
    public $placeholder;
    public $fieldLabel;
    public $defaultValue;
    public $warning;
    public $options;
    public $labelId;

    public function init(): void
    {
        if (empty($this->config['value']) && isset($this->config['defaultValue'])) {
            $this->config['value'] = $this->config['defaultValue'];
        }

        $this->config['id'] = $this->config['name'];

        $g = [
            'label' => $this->label,
            'name' => $this->name,
            'id' => $this->name,
            'value' => $this->value,
            'placeholder' => $this->placeholder,
            'options' => $this->options,
            'warning' => $this->warning,
            'labelId' => $this->labelId,
            //'errors' => $this->getErrors('placeholder'),
            'instructions' => $this->instructions,
        ];
    }

    public function renderField(): \Twig\Markup
    {
        $html = Craft::$app->getView()->renderTemplate('_includes/forms/' . $this->type, $this->config);

        return Template::raw($html);
    }

    public function rules(): array
    {
        $rules = parent::rules();


        return $rules;
    }
}
