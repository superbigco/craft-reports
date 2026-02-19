<?php

declare(strict_types=1);

namespace superbig\reports\models;

use craft\base\Model;
use craft\helpers\Json;
use superbig\reports\Reports;

class ReportTarget extends Model
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $handle = null;
    public ?string $targetClass = null;
    public mixed $settings = null;

    public function init(): void
    {
        if (!$this->targetClass) {
            $this->targetClass = Reports::getInstance()->getTarget()->getDefaultTargetType();
        }

        if (\is_string($this->settings)) {
            $this->settings = Json::decodeIfJson($this->settings);
        }
    }

    public function getTargetType(): ?\superbig\reports\targets\ReportTarget
    {
        $selectedDefinition = array_merge(
            $this->settings[$this->targetClass] ?? [],
            ['type' => $this->targetClass]
        );

        return Reports::getInstance()->getTarget()->createTargetType($selectedDefinition);
    }

    public function rules(): array
    {
        return [];
    }
}
