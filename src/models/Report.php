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

use craft\helpers\Json;
use putyourlightson\logtofile\LogToFile;
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

    public  $id;
    public  $siteId;
    public  $name;
    public  $handle;
    public  $content;
    public  $settings;
    public  $fieldValues = [];
    public  $dateLastRun;
    private $_targets;
    private $_settings;
    private $_fieldParamNamePrefix;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        if (is_string($this->fieldValues)) {
            LogToFile::$handle = 'reports';
            LogToFile::info('Set field values ' . $this->fieldValues);
            $this->fieldValues = Json::decodeIfJson($this->fieldValues);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'settings'], 'string'],
        ];
    }

    /**
     * @return ReportResult
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function run()
    {
        return Reports::$plugin->getReport()->runReport($this);
    }

    public function reportSettings()
    {
        if (!$this->_settings) {
            $this->_settings = Reports::$plugin->getReport()->settingsForReport($this);
        }

        return $this->_settings;
    }

    public function getConnectedTargets()
    {
        if (!$this->_targets) {
            $this->_targets = Reports::$plugin->getTarget()->getConnectedTargetsForReport($this);
        }

        return $this->_targets;
    }

    public function canManage()
    {
        return Craft::$app->getUser()->checkPermission(Reports::PERMISSION_MANAGE_REPORTS);
    }

    public function setFieldValuesFromRequest(string $paramNamespace = '')
    {
        LogToFile::$handle = 'reports';

        $values = Craft::$app->getRequest()->getBodyParam($paramNamespace, []);

        LogToFile::info('Got field values ' . \superbig\vipps\helpers\LogToFile::encodeForLog($values));

        foreach ($this->reportSettings()->getFields() as $field) {
            // Do we have any post data for this field?
            if (isset($values[ $field->getHandle() ])) {
                $value = $values[ $field->handle ];
            }
            else {
                continue;
            }

            $this->setFieldValue($field->getHandle(), $value);

            // Normalize it now in case the system language changes later
            // $this->normalizeFieldValue($field->handle);
        }
    }

    public function setFieldValue(string $handle, $value)
    {
        $this->fieldValues[ $handle ] = $value;
    }

    public function getFieldValue(string $handle)
    {
        $field = $this->reportSettings()->getField($handle);

        if ($field) {
            $value = $this->fieldValues[ $handle ] ?? false;

            return $field->normalizeValue($value !== false ? $value : $field->defaultValue);
        }
    }

    /**
     * @inheritdoc
     */
    public function getFieldParamNamespace()
    {
        return $this->_fieldParamNamePrefix;
    }

    /**
     * @inheritdoc
     */
    public function setFieldParamNamespace(string $namespace)
    {
        $this->_fieldParamNamePrefix = $namespace;
    }
}
