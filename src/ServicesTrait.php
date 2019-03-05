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

use superbig\reports\services\Chart;
use superbig\reports\services\Email;
use superbig\reports\services\Export;
use superbig\reports\services\Report;
use superbig\reports\services\Target;
use superbig\reports\services\Widget;

trait ServicesTrait
{
    public function initServices()
    {
        $this->setComponents([
            'email'  => Email::class,
            'export' => Export::class,
            'report' => Report::class,
            'chart'  => Chart::class,
            'widget' => Widget::class,
            'target' => Target::class,
        ]);
    }

    /**
     * @return Report
     */
    public function getReport(): Report
    {
        return $this->get('report');
    }

    public function getExport(): Export
    {
        return $this->get('export');
    }

    public function getEmail(): Email
    {
        return $this->get('email');
    }

    public function getChart(): Chart
    {
        return $this->get('chart');
    }

    public function getWidget(): Widget
    {
        return $this->get('widget');
    }

    public function getTarget(): Target
    {
        return $this->get('target');
    }
}
