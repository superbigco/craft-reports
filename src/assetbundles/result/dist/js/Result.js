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
var $spinner = $('#js-updateSpinner');
var reportId = $('#settings').data('id');
var $fieldsForm = $('#settings');
var $saveSettingsButton = $('#js-saveSettings');
var $resultsContainer = $('#js-reportResults');

var updateResults = function () {
    var $tempForm = $('<form />').html($fieldsForm.clone());
    var formData = $tempForm.serialize();
    var formDataArray = $tempForm.serializeArray();
    var data = $.param({id: reportId}) + '&' + formData;
    //console.log({data, formDataArray})

    $saveSettingsButton.removeClass('hidden');
    $spinner.removeClass('hidden');
    Craft.postActionRequest('reports/reports/result', data, response => {
        var html = response.html;

        $resultsContainer.html(html);

        $spinner.addClass('hidden');
        Craft.cp.displayNotice('Updated report');
        //console.log(response)
    });
}

$saveSettingsButton.on('click', function (event) {
    event.preventDefault();
    var $tempForm = $('<form />').html($fieldsForm.clone());
    var formData = $tempForm.serialize();
    var formDataArray = $tempForm.serializeArray();
    var data = $.param({id: reportId}) + '&' + formData;

    $spinner.removeClass('hidden');

    Craft.postActionRequest('reports/reports/save-fields', data, response => {
        $spinner.addClass('hidden');
        $saveSettingsButton.addClass('hidden');
        Craft.cp.displayNotice('Saved fields');
        //console.log(response)
    });
})

var handleAddedOrRemovedElementNode = function (mutation) {
    var addedNodes = mutation.addedNodes; // NodeList
    var removedNodes = mutation.removedNodes; // NodeList
    console.log({mutation, addedNodes, removedNodes});

    updateResults();
}

// Event listeners
$(document).on('change input selectElements', '#settings', e => {
    updateResults();
})

var mutationObserver = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
        var target = mutation.target;
        console.log(mutation)
        if (mutation.type === 'childList' && target.classList.contains('elements')) {
            handleAddedOrRemovedElementNode(mutation)
        }
    });
});

mutationObserver.observe(document.getElementById('settings'), {
    attributes: false,
    characterData: false,
    childList: true,
    subtree: true,
    attributeOldValue: false,
    characterDataOldValue: false
});