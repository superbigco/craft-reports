<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\assetbundles\reports;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportsAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@superbig/reports/assetbundles/reports/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/codemirror/lib/codemirror.js',
            'js/codemirror/addon/mode/overlay.js',
            'js/codemirror/mode/twig/twig.js',
            'js/codemirror/mode/htmlmixed/htmlmixed.js',
            'js/Reports.js',
        ];

        $this->css = [
            'js/codemirror/lib/codemirror.css',
            'css/Reports.css',
        ];

        parent::init();
    }
}
