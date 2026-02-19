<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\services;

use craft\base\Component;

use superbig\reports\Reports;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Schedule extends Component
{
    public const TYPE_TABLE = 'table';

    public const TYPE_CHART = 'chart';

    public const TARGET_EMAIL = 'email';

    /**
     * @var string
     */
    public const TARGET_SLACK = 'slack';

    public $type;

    public $target;

    // Public Methods
    // =========================================================================
}
