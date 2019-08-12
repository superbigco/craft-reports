<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports;

use Craft;
use craft\elements\User;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;
use yii\base\Event;

trait UserPermissionsTrait
{
    public function initPermissions()
    {
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function(RegisterUserPermissionsEvent $event) {

                $event->permissions[ $this->name ] = [
                    Reports::PERMISSION_ACCESS => ['label' => Craft::t('reports', 'Access Reports')],
                    Reports::PERMISSION_CREATE => ['label' => Craft::t('reports', 'Create Reports')],
                    Reports::PERMISSION_EDIT   => ['label' => Craft::t('reports', 'Edit Reports')],
                    Reports::PERMISSION_RUN    => ['label' => Craft::t('reports', 'Run Reports')],
                    Reports::PERMISSION_EXPORT => ['label' => Craft::t('reports', 'Export Reports')],
                    Reports::PERMISSION_DELETE => ['label' => Craft::t('reports', 'Delete Reports')],
                ];
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem()
    {
        $navItem = parent::getCpNavItem();
        $navItem['label'] = $this->getPluginName();
        $subNav = [];

        /** @var User $currentUser */
        $currentUser = Craft::$app->getUser()->getIdentity();

        $permissions = [
            [
                'type'   => Reports::PERMISSION_ACCESS,
                'id'     => 'reports',
                'config' => [
                    'label' => 'Reports',
                    'url'   => 'reports',
                ],
            ],
            [
                'type'   => Reports::PERMISSION_TARGETS,
                'id'     => 'targets',
                'config' => [
                    'label' => 'Report Targets',
                    'url'   => 'reports/targets',
                ],
            ],
        ];

        foreach ($permissions as $permission) {
            if ($currentUser->can($permission['type'])) {
                $subNav[ $permission['id'] ] = $permission['config'];
            }
        }

        $navItem = array_merge($navItem, [
            'subnav' => $subNav,
        ]);

        return $navItem;
    }

}
