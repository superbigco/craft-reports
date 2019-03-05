<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\targets;

use Craft;
use craft\mail\Message;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use superbig\reports\models\Report;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 *
 * @property array  $emails  The email recipients
 * @property string $subject The email subject
 */
class EmailTarget extends ReportTarget
{
    public $emails  = [];
    public $subject = '';
    public $body    = '';

    public static function displayName(): string
    {
        return \Craft::t('reports', 'Email');
    }

    public function send(Report $report): bool
    {
        $renderVariables = [

        ];

        $originalLanguage     = Craft::$app->language;
        $view                 = Craft::$app->getView();
        $oldTemplateMode      = $view->getTemplateMode();
        Craft::$app->language = $originalLanguage;

        $view->setTemplateMode($view::TEMPLATE_MODE_SITE);

        $newEmail = new Message();
        $newEmail->setTo($this->emails);
        $newEmail->setFrom(\craft\helpers\App::mailSettings()->fromEmail);
        $newEmail->setSubject('Report');

        if ($templatePath && $view->doesTemplateExist($templatePath)) {
            $body = $view->renderTemplate($templatePath, $renderVariables);
            $newEmail->setHtmlBody($body);
        }

        //$file = Craft::$app->getPath()->getTempPath() . DIRECTORY_SEPARATOR . 'craft-reports' . DIRECTORY_SEPARATOR . $fileName;

        /*if (file_exists($file)) {
            $newEmail->attach($file, [
                'fileName' => $fileName,
            ]);
        }*/

        if (!Craft::$app->getMailer()->send($newEmail)) {

            $error = Craft::t(
                'reports',
                'Email Error: {error}',
                [
                    'error' => 'Failed to send email',
                ]);

            Craft::error($error, __METHOD__);

            Craft::$app->language = $originalLanguage;

            $view->setTemplateMode($oldTemplateMode);

            return false;
        }

        Craft::$app->language = $originalLanguage;

        $view->setTemplateMode($oldTemplateMode);
    }

    public function formatMessage(Report $report)
    {

    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('reports/_targets/email/settings', [
            'targetType' => $this,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            [['subject'], 'required'],
            //[['apiKey'], 'default', 'value' => ''],
            //[['apiKey'], 'string'],
        ]);

        return $rules;
    }

}