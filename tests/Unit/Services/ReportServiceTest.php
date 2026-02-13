<?php

use superbig\reports\services\Chart;
use superbig\reports\services\Email;
use superbig\reports\services\Export;
use superbig\reports\services\Report;
use superbig\reports\services\Target;
use superbig\reports\services\Widget;

it('can instantiate the report service', function () {
    expect(new Report())->toBeInstanceOf(Report::class);
});

it('can instantiate the target service', function () {
    expect(new Target())->toBeInstanceOf(Target::class);
});

it('can instantiate the export service', function () {
    expect(new Export())->toBeInstanceOf(Export::class);
});

it('can instantiate the email service', function () {
    expect(new Email())->toBeInstanceOf(Email::class);
});

it('can instantiate the chart service', function () {
    expect(new Chart())->toBeInstanceOf(Chart::class);
});

it('can instantiate the widget service', function () {
    expect(new Widget())->toBeInstanceOf(Widget::class);
});
