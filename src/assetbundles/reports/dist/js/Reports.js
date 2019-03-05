/**
 * Reports plugin for Craft CMS
 *
 * Reports JS
 *
 * @author    Superbig
 * @copyright Copyright (c) 2019 Superbig
 * @link      https://superbig.co
 * @package   Reports
 * @since     1.0.0
 */
const $contentEditor = document.querySelector('.js-reportsEditor');
const $settingsEditor = document.querySelector('.js-settingsEditor');
if ($contentEditor) {
    CodeMirror.defineMode('htmltwig', (config, parserConfig) => {
        return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || 'text/html'), CodeMirror.getMode(config, 'twig'));
    });

    const contentEditorInstance = CodeMirror.fromTextArea($contentEditor, {
        mode: 'twig',
        lineNumbers: true,
        indentUnit: 4,
    });
}
if ($settingsEditor) {
    CodeMirror.defineMode('htmltwig', (config, parserConfig) => {
        return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || 'text/html'), CodeMirror.getMode(config, 'twig'));
    });

    const settingsEditorInstance = CodeMirror.fromTextArea($settingsEditor, {
        mode: 'twig',
        lineNumbers: true,
        indentUnit: 4,
    });
}