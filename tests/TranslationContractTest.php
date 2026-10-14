<?php

/**
 * Verify BoardNotes uses isolated translation keys with US and British English catalogues.
 *
 * Related files: Plugin.php and Locale/en_US/translations.php.
 */

$pluginRoot = dirname(__DIR__);
$plugin = file_get_contents($pluginRoot . '/Plugin.php');
$sources = $plugin . "\n" . file_get_contents($pluginRoot . '/Controller/BoardNotesController.php');
foreach (glob($pluginRoot . '/Template/*/*.php') as $template) {
    $sources .= "\n" . file_get_contents($template);
}

$translations = require $pluginRoot . '/Locale/en_US/translations.php';
$britishTranslations = require $pluginRoot . '/Locale/en_GB/translations.php';
$failures = array();

preg_match_all("/[te]\\('(?<key>boardnotes\\.[a-z0-9_]+)'/", $sources, $matches);
foreach (array_unique($matches['key']) as $key) {
    if (! isset($translations[$key])) {
        $failures[] = 'Missing English translation: ' . $key;
    }
}

if (strpos($plugin, "Translator::load('en_US'") === false) {
    $failures[] = 'The English fallback catalogue is not loaded.';
}
if (! isset($britishTranslations['boardnotes.empty_analytics'])) {
    $failures[] = 'The British English catalogue is incomplete.';
}

if ($failures !== array()) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo 'BoardNotes translation contract passed.' . PHP_EOL;
