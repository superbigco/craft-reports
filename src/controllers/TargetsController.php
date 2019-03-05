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
use superbig\reports\models\Report;
use superbig\reports\models\ReportTarget;
use superbig\reports\Reports;

use Craft;
use craft\web\Controller;
use superbig\reports\targets\ReportTargetInterface;

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
        return $this->renderTemplate('reports/_targets/index', [
            'targets' => Reports::$plugin->getTarget()->getAllReportTargets(),
        ]);
    }

    public function actionNew()
    {
        $target = new ReportTarget();

        return $this->renderTemplate('reports/_targets/edit', [
            'target'           => $target,
            'typeSettingsHtml' => $this->_editView($target),
        ]);
    }

    public function actionEdit(int $id = null)
    {
        $target = Reports::$plugin->getTarget()->getReportTargetById($id);

        return $this->renderTemplate('reports/_targets/edit', [
            'target'           => $target,
            'typeSettingsHtml' => $this->_editView($target),
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
        $report = Reports::$plugin->getTarget()->getReportTargetById($id);
        $result = Reports::$plugin->getTarget()->runReport($id);

        return $this->renderTemplate('reports/_targets/run', [
            'report' => $report,
            'result' => $result,
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

        // Save it
        if (!Reports::$plugin->getTarget()->saveReportTarget($target)) {
            Craft::$app->getUrlManager()->setRouteParams([
                'target' => $target,
            ]);

            return;
        }

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
}
