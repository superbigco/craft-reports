<?php

it('boots Craft and installs the Reports plugin', function () {
    expect(class_exists(\superbig\reports\Reports::class))
        ->toBeTrue('Plugin bootstrap failed');
});
