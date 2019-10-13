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

use craft\helpers\ArrayHelper;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
use superbig\reports\jobs\ExportJob;
use superbig\reports\models\Report;
use superbig\reports\models\ReportTarget;
use superbig\reports\Reports;

use Craft;
use craft\web\Controller;
use superbig\reports\targets\ReportTargetInterface;
use yii\web\ForbiddenHttpException;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class TargetsController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        $permissions = [
            // Allow users that can run reports to also run export targets
            'queue-run' => [Reports::PERMISSION_RUN_REPORTS, Reports::PERMISSION_MANAGE_REPORTS],
            'run'       => [Reports::PERMISSION_MANAGE_REPORTS, Reports::PERMISSION_RUN_REPORTS],

            'index'     => [Reports::PERMISSION_MANAGE_TARGETS],
            'new'       => [Reports::PERMISSION_MANAGE_TARGETS],
            'create'    => [Reports::PERMISSION_MANAGE_TARGETS],
            'save'      => [Reports::PERMISSION_MANAGE_TARGETS],
            'edit'      => [Reports::PERMISSION_MANAGE_TARGETS],
            'delete'    => [Reports::PERMISSION_MANAGE_TARGETS],
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
        return $this->renderTemplate('reports/_targets/index', [
            'targets' => Reports::$plugin->getTarget()->getAllReportTargets(),
        ]);
    }

    public function actionNew()
    {
        $target = new ReportTarget();

        return $this->renderTemplate('reports/_targets/edit', [
            'target'             => $target,
            'reportOptions'      => $this->_getReportOptions(),
            'connectedReportIds' => Reports::$plugin->getTarget()->getConnectedReportIds($target),
            'typeSettingsHtml'   => $this->_editView($target),
        ]);
    }

    public function actionEdit(int $id = null)
    {
        $target = Reports::$plugin->getTarget()->getReportTargetById($id);

        return $this->renderTemplate('reports/_targets/edit', [
            'target'             => $target,
            'reportOptions'      => $this->_getReportOptions(),
            'connectedReportIds' => Reports::$plugin->getTarget()->getConnectedReportIds($target),
            'typeSettingsHtml'   => $this->_editView($target),
        ]);
    }

    /**
     * @param int|null $id
     *
     * @return \yii\web\Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionRun(int $id = null)
    {
        $target = Reports::$plugin->getTarget()->getReportTargetById($id);
        $result = Reports::$plugin->getTarget()->runReportTarget($id);

        if (!$result) {
            $error = 'Failed to run ' . $target->name;
            Craft::$app->getSession()->setError($error);

            return $this->redirect(UrlHelper::cpUrl('reports/targets'));
        }

        $notice = 'Successfully ran ' . $target->name;
        Craft::$app->getSession()->setNotice($notice);

        return $this->goBack(UrlHelper::cpUrl('reports/targets'));
    }

    /**
     * @param int|null $id
     *
     * @return \yii\web\Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionQueueRun()
    {
        $id  = Craft::$app->getRequest()->getRequiredParam('id');
        $job = new ExportJob([
            'targetId' => $id,
        ]);

        return $this->asJson([
            'success' => (bool)Craft::$app->getQueue()->push($job),
        ]);
    }

    public function actionSave()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $id      = $request->getParam('id');
        $target  = Reports::$plugin->getTarget()->getReportTargetById($id);

        if (!$target) {
            $target = new ReportTarget();
        }

        $target->name        = $request->getParam('name');
        $target->handle      = $request->getParam('handle');
        $target->targetClass = $request->getParam('targetClass');
        $target->settings    = $request->getParam('settings');
        $connectedReportIds  = $request->getParam('connectedReportIds');

        // Save it
        if (!Reports::$plugin->getTarget()->saveReportTarget($target)) {
            Craft::$app->getUrlManager()->setRouteParams([
                'target' => $target,
            ]);

            return;
        }

        Reports::$plugin->getTarget()->syncTargetReportRelationship($target, $connectedReportIds);

        $notice = Craft::t(
            'reports',
            'Target was saved'
        );

        Craft::$app->getSession()->setNotice($notice);

        return $this->redirectToPostedUrl($target, 'reports/targets');
    }

    public function actionDelete()
    {
        $id = Craft::$app->getRequest()->getRequiredParam('id');

        return $this->asJson([
            'success' => Reports::$plugin->getTarget()->deleteReportTarget($id),
        ]);
    }

    /**
     * @inheritdoc
     */
    private function _editView(ReportTarget $target)
    {
        // Get the image transform types
        $allTargetTypes     = Reports::$plugin->getTarget()->getTargetTypes();
        $selectedDefinition = array_merge(
            $target->settings[ $target->targetClass ] ?? [],
            ['type' => $target->targetClass]
        );
        $selectedType       = Reports::$plugin->getTarget()->createTargetType($selectedDefinition);
        $targetOptions      = [];

        /** @var ReportTargetInterface $class */
        foreach ($allTargetTypes as $class) {
            $targetOptions[] = [
                'value' => $class,
                'label' => $class::displayName(),
            ];
        }

        // Sort them by name
        ArrayHelper::multisort($targetOptions, 'label');

        // Render the settings template
        try {
            $result = Craft::$app->getView()->renderTemplate(
                'reports/_targets/targetSettings',
                [
                    'target'             => $target,
                    'allTargetTypes'     => $allTargetTypes,
                    'targetOptions'      => $targetOptions,
                    'selectedTargetType' => $selectedType,
                ]
            );

            return Template::raw($result);
        } catch (\Twig_Error_Loader $e) {
            Craft::error($e->getMessage(), __METHOD__);
        } catch (Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }

        return '';
    }

    private function _getReportOptions()
    {
        return array_map(
            function(Report $report) {
                return [
                    'label' => $report->name,
                    'value' => $report->id,
                ];
            },
            Reports::$plugin->getReport()->getAllReports()
        );
    }
}
