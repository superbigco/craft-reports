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

use superbig\reports\Reports;

use Craft;
use craft\base\Component;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class Schedule extends Component
{
    const TYPE_TABLE   = 'table';
    const TYPE_CHART   = 'chart';
    const TARGET_EMAIL = 'email';
    const TARGET_SLACK = 'slack';

    public $type;
    public $target;

    // Public Methods
    // =========================================================================

}
