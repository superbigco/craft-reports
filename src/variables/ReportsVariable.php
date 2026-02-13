<?php

declare(strict_types=1);

namespace superbig\reports\variables;

use superbig\reports\Reports;
use superbig\reports\services\Target;
use superbig\reports\targets\ReportTargetInterface;

class ReportsVariable
{
    public function __construct(
        private ?Target $targetService = null,
    ) {
    }

    private function getTargetService(): Target
    {
        return $this->targetService ?? Reports::getInstance()->getTarget();
    }

    /**
     * @param string|array<mixed> $config
     */
    public function createTargetType(string|array $config): ReportTargetInterface
    {
        return $this->getTargetService()->createTargetType($config);
    }
}
