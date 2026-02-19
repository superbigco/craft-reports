<?php

declare(strict_types=1);

namespace superbig\reports;

use Craft;
use craft\elements\User;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;
use yii\base\Event;

trait UserPermissionsTrait
{
    public function initPermissions(): void
    {
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function(RegisterUserPermissionsEvent $event): void {
                $event->permissions[] = [
                    'heading' => $this->name,
                    'permissions' => $this->_getPermissions(),
                ];
            }
        );
    }

    public function getCpNavItem(): ?array
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

        $checkPermissions = function(array $permissions) use ($currentUser): bool {
            $results = array_map(function($permission) use ($currentUser) {
                return $currentUser->can($permission);
            }, $permissions);

            return \in_array(true, $results);
        };

        foreach ($permissions as $permission) {
            $canAccess = !\is_array($permission['type'])
                ? $currentUser->can($permission['type'])
                : $checkPermissions($permission['type']);

            if ($canAccess) {
                $subNav[$permission['id']] = $permission['config'];
            }
        }

        $navItem = array_merge($navItem, [
            'subnav' => $subNav,
        ]);

        return $navItem;
    }

    /**
     * @return array<string, array{label: string}>
     */
    private function _getPermissions(): array
    {
        $permissions = [
            Reports::PERMISSION_RUN_REPORTS => 'Run Reports',
            Reports::PERMISSION_MANAGE_REPORTS => 'Manage Reports',
            Reports::PERMISSION_MANAGE_TARGETS => 'Manage Export Targets',
        ];

        $result = [];

        foreach ($permissions as $key => $label) {
            $result[$key] = ['label' => Craft::t('reports', $label)];
        }

        return $result;
    }
}
