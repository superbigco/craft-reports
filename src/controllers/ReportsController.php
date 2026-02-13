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

use Craft;
use craft\web\Controller;
use superbig\reports\assetbundles\result\ResultAsset;

use superbig\reports\models\Report;
use superbig\reports\Reports;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportsController extends Controller
{
    public function beforeAction($action): bool
    {
        $permissions = [
            'index' => [Reports::PERMISSION_MANAGE_REPORTS, Reports::PERMISSION_RUN_REPORTS],
            'run' => [Reports::PERMISSION_MANAGE_REPORTS, Reports::PERMISSION_RUN_REPORTS],
            'create' => [Reports::PERMISSION_MANAGE_REPORTS],
            'edit' => [Reports::PERMISSION_MANAGE_REPORTS],
            'delete' => [Reports::PERMISSION_MANAGE_REPORTS],
        ];

        if (!isset($permissions[ $action->id ])) {
            return parent::beforeAction($action);

        }

        $users = Craft::$app->getUser();
        $checks = array_map(function($permission) use ($users) {
            return $users->checkPermission($permission);
        }, $permissions[ $action->id ]);
        $canAccess = \in_array(true, $checks);

        if (!$canAccess) {
            throw new ForbiddenHttpException('User is not permitted to perform this action');
        }

        return true;
    }

    public function actionIndex(): \yii\web\Response
    {
        return $this->renderTemplate('reports/reports/index', [
            'reports' => Reports::getInstance()->getReport()->getAllReports(),
        ]);
    }

    public function actionNew()
    {
        $report = new Report();

        $this->renderTemplate('reports/reports/edit', [
            'report' => $report,
            'connectedTargets' => $report->getConnectedTargets(),
        ]);
    }

    public function actionEdit(int $id = null)
    {
        $report = Reports::getInstance()->getReport()->getReportById($id);

        return $this->renderTemplate('reports/reports/edit', [
            'report' => $report,
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
        $request = Craft::$app->getRequest();
        $reportId = $id ?? $request->getParam('id');

        Craft::$app->getView()->registerAssetBundle(ResultAsset::class);

        /** @var Report|null $report */
        $report = Reports::getInstance()->getReport()->getReportById($reportId);

        if (!$report) {
            throw new NotFoundHttpException();
        }

        $result = $report->run();

        $result = $this->renderTemplate('reports/reports/run', [
            'report' => $report,
            'result' => $result,
            'hasFields' => false,
            'connectedTargets' => $report->getConnectedTargets(),
        ]);

        if ($request->getAcceptsJson()) {
            return $result;
        }

        return $result;
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
        $report = Reports::getInstance()->getReport()->getReportById($id);
        $info = Reports::getInstance()->getExport()->csv($report);

        return Craft::$app->getResponse()->sendFile($info['path'], $info['filename'], [
            'mimeType' => $info['mimeType'],
        ]);
    }


    public function actionSave()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $id = $request->getParam('id');
        $report = Reports::getInstance()->getReport()->getReportById($id);

        if (!$report) {
            $report = new Report();
        }

        $report->siteId = Craft::$app->getSites()->getPrimarySite()->id;
        $report->name = $request->getParam('name');
        $report->handle = $request->getParam('handle');
        $report->content = $request->getParam('content');
        $report->settings = $request->getParam('settings');

        // Save it
        if (!Reports::getInstance()->getReport()->saveReport($report)) {
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
            'success' => Reports::getInstance()->getReport()->deleteReport($id),
        ]);
    }
}
