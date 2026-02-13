<?php

use superbig\reports\Reports;
use superbig\reports\services\Report as ReportService;

it('can access the report service from the plugin', function () {
    $plugin = Reports::getInstance();
    expect($plugin)->not->toBeNull();
    expect($plugin->getReport())->toBeInstanceOf(ReportService::class);
});

it('can access the target service from the plugin', function () {
    $plugin = Reports::getInstance();
    expect($plugin->getTarget())->toBeInstanceOf(\superbig\reports\services\Target::class);
});

it('can access the export service from the plugin', function () {
    $plugin = Reports::getInstance();
    expect($plugin->getExport())->toBeInstanceOf(\superbig\reports\services\Export::class);
});

it('can access the email service from the plugin', function () {
    $plugin = Reports::getInstance();
    expect($plugin->getEmail())->toBeInstanceOf(\superbig\reports\services\Email::class);
});

it('can access the chart service from the plugin', function () {
    $plugin = Reports::getInstance();
    expect($plugin->getChart())->toBeInstanceOf(\superbig\reports\services\Chart::class);
});

it('can access the widget service from the plugin', function () {
    $plugin = Reports::getInstance();
    expect($plugin->getWidget())->toBeInstanceOf(\superbig\reports\services\Widget::class);
});
