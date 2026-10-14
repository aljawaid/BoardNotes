<?php

/**
 * Verify BoardNotes request and output security contracts.
 *
 * Related files: Controller/BoardNotesController.php, Template/project/data.php,
 * Template/project/report.php and Assets/js/boardnotes.js.
 */

$pluginRoot = dirname(__DIR__);
$controller = file_get_contents($pluginRoot . '/Controller/BoardNotesController.php');
$templates = implode("\n", array_map('file_get_contents', glob($pluginRoot . '/Template/*/*.php')));
$javascript = file_get_contents($pluginRoot . '/Assets/js/boardnotes.js');
$plugin = file_get_contents($pluginRoot . '/Plugin.php');
$failures = array();

foreach (array(
    'checkReusableCSRFParam()',
    "getRawValue('description')",
    'response->json(',
    'getValidatedNoteInput',
) as $snippet) {
    if (strpos($controller, $snippet) === false) {
        $failures[] = 'Missing request security contract: ' . $snippet;
    }
}

foreach (array('/kanboard/', 'http://', '.html(', '<script', '<style', 'onclick=', 'user_id=') as $snippet) {
    if (stripos($templates . $javascript, $snippet) !== false) {
        $failures[] = 'Legacy or unsafe output remains: ' . $snippet;
    }
}

foreach (array("setContentSecurityPolicy", "unsafe-inline", "unsafe-eval") as $snippet) {
    if (strpos($plugin, $snippet) !== false) {
        $failures[] = 'Global Content Security Policy weakening remains: ' . $snippet;
    }
}

foreach (array(
    "\$this->text->e(\$note['title'])",
    "\$this->text->e(\$note['description'])",
    "\$this->text->e(\$note['category'])",
    "\$this->url->href('BoardNotesController'",
) as $snippet) {
    if (strpos($templates, $snippet) === false) {
        $failures[] = 'Missing escaped or generated output contract: ' . $snippet;
    }
}

if ($failures !== array()) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo 'BoardNotes output security contract passed.' . PHP_EOL;
