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

    const TYPE_TEXT = 'textField';
    const TYPE_DATE = 'dateField';
    const TYPE_TIME = 'timeField';
    const TYPE_DATETIME = 'dateTimeField';
    const TYPE_SELECT = 'selectField';
    const TYPE_MULTISELECT = 'multiselectField';
    const TYPE_COLOR = 'colorField';
    const TYPE_TEXTAREA = 'textareaField';
    const TYPE_CHECKBOX = 'checkboxField';
    const TYPE_CHECKBOX_GROUP = 'checkboxGroupField';
    const TYPE_CHECKBOX_SELECT = 'checkboxSelectField';
    const TYPE_RADIO_GROUP = 'radioGroupField';
    const TYPE_LIGHTSWITCH = 'lightswitchField';
    const TYPE_EDITABLE_TABLE = 'editableTableField';
    const TYPE_ELEMENT_SELECT = 'elementSelectField';
    const TYPE_AUTOSUGGEST = 'autosuggestField';

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
