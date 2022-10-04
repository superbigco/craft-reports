<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\services;

use Craft;
use craft\base\Component;

use craft\mail\Message;
use superbig\reports\Reports;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Email extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function emailReport(\superbig\reports\models\Report $report, $emails = [])
    {
        $renderVariables = [

        ];

        $originalLanguage = Craft::$app->language;
        $templatePath = Reports::$plugin->getSettings()->emailPath;
        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();
        Craft::$app->language = $originalLanguage;

        $view->setTemplateMode($view::TEMPLATE_MODE_SITE);

        $newEmail = new Message();
        $newEmail->setTo($emails);
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
}
