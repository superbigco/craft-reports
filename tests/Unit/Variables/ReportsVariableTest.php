<?php

use superbig\reports\services\Target;
use superbig\reports\variables\ReportsVariable;

it('can be instantiated with service injection', function () {
    $service = new Target();
    $variable = new ReportsVariable($service);

    expect($variable)->toBeInstanceOf(ReportsVariable::class);
});

it('can be instantiated without service injection (production path)', function () {
    $variable = new ReportsVariable();

    expect($variable)->toBeInstanceOf(ReportsVariable::class);
});
