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
class Widget extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (Reports::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
