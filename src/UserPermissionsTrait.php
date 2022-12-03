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
                $event->permissions[] = [
                    'heading' => $this->name,
                    'permissions' => $this->_getPermissions(),
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
                'type' => [Reports::PERMISSION_MANAGE_REPORTS, Reports::PERMISSION_RUN_REPORTS],
                'id' => 'reports',
                'config' => [
                    'label' => 'Reports',
                    'url' => 'reports',
                ],
            ],
            [
                'type' => Reports::PERMISSION_MANAGE_TARGETS,
                'id' => 'targets',
                'config' => [
                    'label' => 'Report Targets',
                    'url' => 'reports/targets',
                ],
            ],
        ];

        $checkPermissions = function($permissions) use ($currentUser) {
            $results = array_map(function($permission) use ($currentUser) {
                return $currentUser->can($permission);
            }, $permissions);

            return \in_array(true, $results);
        };

        foreach ($permissions as $permission) {
            $canAccess = !\is_array($permission['type']) ? $currentUser->can($permission['type']) : $checkPermissions($permission['type']);

            if ($canAccess) {
                $subNav[ $permission['id'] ] = $permission['config'];
            }
        }

        $navItem = array_merge($navItem, [
            'subnav' => $subNav,
        ]);

        return $navItem;
    }

    private function _getPermissions()
    {
        $permissions = [
            Reports::PERMISSION_RUN_REPORTS => 'Run Reports',
            Reports::PERMISSION_MANAGE_REPORTS => 'Manage Reports',
            Reports::PERMISSION_MANAGE_TARGETS => 'Manage Export Targets',
        ];

        $result = [];

        foreach ($permissions as $key => $label) {
            $result[ $key ] = ['label' => Craft::t('reports', $label)];
        }

        return $result;
    }
}
