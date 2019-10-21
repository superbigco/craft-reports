/**
 * Reports plugin for Craft CMS
 *
 * Index Field JS
 *
 * @author    Superbig
 * @copyright Copyright (c) 2019 Superbig
 * @link      https://superbig.co
 * @package   Reports
 * @since     1.0.0
 */
var reportId = $('#settings').data('id');
$(document).on('change', '#settings', e => {
    Craft.cp.displayNotice('Updating report');
    Craft.postActionRequest('reports/reports/run/', { id: reportId }, response => {
        console.log(response)
    });
})