<?php

declare(strict_types=1);

namespace superbig\reports\models;

use craft\base\Model;

class Settings extends Model
{
    public bool $enableScheduler = true;
    public array $helpers = [];
    public string $pluginName = 'Reports';

    public function rules(): array
    {
        return [];
    }
}
