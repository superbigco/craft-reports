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
use craft\elements\Entry;
use craft\elements\User;

use superbig\reports\Reports;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 *
 * @property array  $fields
 * @property Report $report
 */
class Fields extends Model
{
    // Public Properties
    // =========================================================================

    public $fields = [];
    public Report $report;

    public function setReport(Report $report): static
    {
        $this->report = $report;

        return $this;
    }

    /**
     * @param array $content
     *
     * @return $this
     */
    public function append(array $content = []): self
    {
        if (isset($content[0]) && !\is_array($content[0])) {
            $content = [$content];
        }
        $this->content = array_merge($this->content, $content);

        return $this;
    }

    public function textField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_TEXT]);
        $this->fields[] = $field;

        return $this;
    }

    public function dateField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_DATE]);
        $this->fields[] = $field;

        return $this;
    }


    public function timeField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_TIME]);
        $this->fields[] = $field;

        return $this;
    }

    public function dateTimeField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_DATETIME]);
        $this->fields[] = $field;

        return $this;
    }

    public function selectField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_SELECT]);
        $this->fields[] = $field;

        return $this;
    }

    public function multiselectField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_MULTISELECT]);
        $this->fields[] = $field;

        return $this;
    }

    public function colorField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_COLOR]);
        $this->fields[] = $field;

        return $this;
    }

    public function textareaField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_TEXTAREA]);
        $this->fields[] = $field;

        return $this;
    }

    public function checkboxField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_CHECKBOX]);
        $this->fields[] = $field;

        return $this;
    }

    public function checkboxGroupField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_CHECKBOX_GROUP]);
        $this->fields[] = $field;

        return $this;
    }

    public function checkboxSelectField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_CHECKBOX_SELECT]);
        $this->fields[] = $field;

        return $this;
    }

    public function radioGroupField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_RADIO_GROUP]);
        $this->fields[] = $field;

        return $this;
    }

    public function lightswitchField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_LIGHTSWITCH]);
        $this->fields[] = $field;

        return $this;
    }

    public function editableTableField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_EDITABLE_TABLE]);
        $this->fields[] = $field;

        return $this;
    }


    public function entriesField(array $config = []): static
    {
        $config = array_merge($config, [
            'elementType' => Entry::class,
        ]);
        $field = new Field(['config' => $config, 'type' => Field::TYPE_ELEMENT_SELECT]);
        $this->fields[] = $field;

        return $this;
    }

    public function usersField(array $config = []): static
    {
        $config = array_merge($config, [
            'elementType' => User::class,
        ]);
        $field = new Field(['config' => $config, 'type' => Field::TYPE_ELEMENT_SELECT]);
        $this->fields[] = $field;

        return $this;
    }

    public function elementSelectField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_ELEMENT_SELECT]);
        $this->fields[] = $field;

        return $this;
    }

    public function autosuggestField(array $config = []): static
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_AUTOSUGGEST]);
        $this->fields[] = $field;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [];
    }
}
