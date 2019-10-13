<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\controllers;

use superbig\reports\models\Report;
use superbig\reports\Reports;

use Craft;
use craft\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportsController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];

    public function beforeAction($action)
    {
        $permissions = [
            'index'  => [Reports::PERMISSION_MANAGE_REPORTS, Reports::PERMISSION_RUN_REPORTS],
            'run'    => [Reports::PERMISSION_MANAGE_REPORTS, Reports::PERMISSION_RUN_REPORTS],
            'create' => [Reports::PERMISSION_MANAGE_REPORTS],
            'edit'   => [Reports::PERMISSION_MANAGE_REPORTS],
            'delete' => [Reports::PERMISSION_MANAGE_REPORTS],
        ];

        if (isset($permissions[ $action->id ])) {
            $users     = Craft::$app->getUser();
            $checks    = array_map(function($permission) use ($users) {
                return $users->checkPermission($permission);
            }, $permissions[ $action->id ]);
            $canAccess = \in_array(true, $checks);


            if (!$canAccess) {
                throw new ForbiddenHttpException('User is not permitted to perform this action');
            }
        }

        return parent::beforeAction($action);
    }

    // Public Methods
    // =========================================================================

    public function actionIndex(): \yii\web\Response
    {
        return $this->renderTemplate('reports/reports/index', [
            'reports' => Reports::$plugin->getReport()->getAllReports(),
        ]);
    }

    public function actionNew()
    {
        $report = new Report();

        $this->renderTemplate('reports/reports/edit', [
            'report'           => $report,
            'connectedTargets' => $report->getConnectedTargets(),
        ]);
    }

    public function actionEdit(int $id = null)
    {
        $report = Reports::$plugin->getReport()->getReportById($id);

        return $this->renderTemplate('reports/reports/edit', [
            'report'           => $report,
            'connectedTargets' => $report->getConnectedTargets(),
        ]);
    }

    /**
     * @param int|null $id
     *
     * @return \yii\web\Response
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionRun(int $id = null)
    {
        /** @var Report $report */
        $report = Reports::$plugin->getReport()->getReportById($id);
        $result = $report->run();

        return $this->renderTemplate('reports/reports/run', [
            'report'           => $report,
            'result'           => $result,
            'connectedTargets' => $report->getConnectedTargets(),
        ]);
    }

    /**
     * @param int|null $id
     *
     * @return \craft\web\Response|\yii\console\Response
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionExport(int $id = null)
    {
        /** @var Report $report */
        $report = Reports::$plugin->getReport()->getReportById($id);
        $info   = Reports::$plugin->getExport()->csv($report);

        return Craft::$app->getResponse()->sendFile($info['path'], $info['filename'], [
            'mimeType' => $info['mimeType'],
        ]);
    }


    public function actionSave()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $id      = $request->getParam('id');
        $report  = Reports::$plugin->getReport()->getReportById($id);

        if (!$report) {
            $report = new Report();
        }

        $report->siteId   = Craft::$app->getSites()->getPrimarySite()->id;
        $report->name     = $request->getParam('name');
        $report->handle   = $request->getParam('handle');
        $report->content  = $request->getParam('content');
        $report->settings = $request->getParam('settings');

        // Save it
        if (!Reports::$plugin->getReport()->saveReport($report)) {
            Craft::$app->getUrlManager()->setRouteParams([
                'report' => $report,
            ]);

            return;
        }

        $notice = Craft::t(
            'reports',
            'Report was saved'
        );

        Craft::$app->getSession()->setNotice($notice);

        return $this->redirectToPostedUrl($report, 'reports');
    }

    public function actionDelete()
    {
        $id = Craft::$app->getRequest()->getRequiredParam('id');

        return $this->asJson([
            'success' => Reports::$plugin->getReport()->deleteReport($id),
        ]);
    }
}
