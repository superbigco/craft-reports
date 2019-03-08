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
$(document).on('click', '.js-queueTargetExport', e => {
    e.preventDefault();

    const $button = $(e.currentTarget);
    const targetId = $button.data('target-id');

    Craft.postActionRequest('reports/targets/queue-run', { id: targetId }, response => {
        console.log(response);

        Craft.cp.displayNotice('Queued export');

        Craft.cp.runQueue();
    });
})