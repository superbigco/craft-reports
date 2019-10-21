<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\assetbundles\result;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ResultAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@superbig/reports/assetbundles/result/dist";
        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Result.js',
        ];

        $this->css = [
            'css/Result.css',
        ];

        parent::init();
    }
}
