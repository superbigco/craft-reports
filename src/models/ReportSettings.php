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
use craft\elements\Entry;
use craft\elements\User;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportSettings extends Model
{
    // Public Properties
    // =========================================================================

    public $report;

    /**  @var Field[] */
    private $_fields;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->_fields;
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
        return count($this->getFields()) > 0;
    }

    public function getField(string $handle)
    {
        return $this->_fields[ $handle ] ?? null;
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

    public function appendField(Field $field)
    {
        $this->_fields[ $field->getHandle() ] = $field;

        return $this;
    }

    public function textField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_TEXT]);
        $this->appendField($field);

        return $this;
    }

    public function dateField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_DATE]);
        $this->appendField($field);

        return $this;
    }


    public function timeField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_TIME]);
        $this->appendField($field);

        return $this;
    }

    public function dateTimeField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_DATETIME]);
        $this->appendField($field);

        return $this;
    }

    public function selectField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_SELECT]);
        $this->appendField($field);

        return $this;
    }

    public function multiselectField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_MULTISELECT]);
        $this->appendField($field);

        return $this;
    }

    public function colorField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_COLOR]);
        $this->appendField($field);

        return $this;
    }

    public function textareaField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_TEXTAREA]);
        $this->appendField($field);

        return $this;
    }

    public function checkboxField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_CHECKBOX]);
        $this->appendField($field);

        return $this;
    }

    public function checkboxGroupField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_CHECKBOX_GROUP]);
        $this->appendField($field);

        return $this;
    }

    public function checkboxSelectField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_CHECKBOX_SELECT]);
        $this->appendField($field);

        return $this;
    }

    public function radioGroupField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_RADIO_GROUP]);
        $this->appendField($field);

        return $this;
    }

    public function lightswitchField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_LIGHTSWITCH]);
        $this->appendField($field);

        return $this;
    }

    public function editableTableField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_EDITABLE_TABLE]);
        $this->appendField($field);

        return $this;
    }


    public function entriesField(array $config = [])
    {
        $config = array_merge($config, [
            'elementType' => Entry::class,
        ]);
        $field  = new Field(['config' => $config, 'type' => Field::TYPE_ELEMENT_SELECT]);
        $this->appendField($field);

        return $this;
    }

    public function usersField(array $config = [])
    {
        $config = array_merge($config, [
            'elementType' => User::class,
        ]);
        $field  = new Field(['config' => $config, 'type' => Field::TYPE_ELEMENT_SELECT]);
        $this->appendField($field);

        return $this;
    }

    public function elementSelectField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_ELEMENT_SELECT]);
        $this->appendField($field);

        return $this;
    }

    public function autosuggestField(array $config = [])
    {
        $field = new Field(['config' => $config, 'type' => Field::TYPE_AUTOSUGGEST]);
        $this->appendField($field);

        return $this;
    }
}
