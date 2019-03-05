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
use superbig\reports\Reports;

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

    public function send(\superbig\reports\models\ReportTarget $target, array $reports = []): bool
    {
        $view            = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();

        $view->setTemplateMode($view::TEMPLATE_MODE_SITE);

        $message = new Message();
        $message->setTo($this->_getEmails());
        $message->setFrom(\craft\helpers\App::mailSettings()->fromEmail);
        $message->setSubject($this->subject);

        $variables = [
            'reports' => $reports,
        ];
        $body      = $view->renderString($this->body, $variables);
        $message->setHtmlBody($body);

        foreach ($reports as $report) {
            $info = Reports::$plugin->getExport()->csv($report);

            $path = $info['path'];

            if (file_exists($path)) {
                $message->attach($path, [
                    'fileName' => $info['filename'],
                ]);
            }
        }

        if (!Craft::$app->getMailer()->send($message)) {
            $error = Craft::t(
                'reports',
                'Email Error: {error}',
                [
                    'error' => 'Failed to send email',
                ]);

            Craft::error($error, __METHOD__);
            $view->setTemplateMode($oldTemplateMode);

            return false;
        }

        $view->setTemplateMode($oldTemplateMode);

        return true;
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

    private function _getEmails()
    {
        return array_map(function($row) {
            return $row['email'];
        }, $this->emails);
    }

}