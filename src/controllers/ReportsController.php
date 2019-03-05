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
            'index'  => Reports::PERMISSION_ACCESS,
            'run'    => Reports::PERMISSION_RUN,
            'create' => Reports::PERMISSION_CREATE,
            'edit'   => Reports::PERMISSION_EDIT,
            'delete' => Reports::PERMISSION_DELETE,
        ];

        if (isset($permissions[ $action->id ])) {
            $this->requirePermission($permissions[ $action->id ]);
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
            'connectedTargets' => Reports::$plugin->getTarget()->getConnectedTargetsForReport($report),
        ]);
    }

    public function actionEdit(int $id = null)
    {
        $report = Reports::$plugin->getReport()->getReportById($id);

        return $this->renderTemplate('reports/reports/edit', [
            'report'           => $report,
            'connectedTargets' => Reports::$plugin->getTarget()->getConnectedTargetsForReport($report),
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
        $report = Reports::$plugin->getReport()->getReportById($id);
        $result = Reports::$plugin->getReport()->runReport($id);

        return $this->renderTemplate('reports/reports/run', [
            'report'           => $report,
            'result'           => $result,
            'connectedTargets' => Reports::$plugin->getTarget()->getConnectedTargetsForReport($report),
        ]);
    }

    public function actionExport(int $id = null)
    {
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
