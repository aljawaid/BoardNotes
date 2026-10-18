<?php

/**
 * Verify BoardNotes remains theme-aware and contained on narrow layouts.
 *
 * Related file: Assets/css/style.css.
 */

$stylesheet = file_get_contents(dirname(__DIR__) . '/Assets/css/style.css');
$failures = array();

foreach (array(
    'box-sizing: border-box',
    'var(--color-primary)',
    'var(--panel-background-color)',
    'var(--table-list-row-background-color)',
    '@media (max-width: 48rem)',
    'width: 100%',
) as $snippet) {
    if (strpos($stylesheet, $snippet) === false) {
        $failures[] = 'Missing theme or narrow-layout contract: ' . $snippet;
    }
}

if (preg_match('/#[0-9a-f]{3,8}\b/i', $stylesheet)) {
    $failures[] = 'A fixed colour bypasses the active Kanboard theme.';
}

if ($failures !== array()) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo 'BoardNotes theme and layout contract passed.' . PHP_EOL;
