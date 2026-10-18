<?php

/**
 * Verify BoardNotes documentation remains current and preserves original attribution.
 *
 * Related files: README.md, changelog.md and Plugin.php.
 */

$pluginRoot = dirname(__DIR__);
$readme = file_get_contents($pluginRoot . '/README.md');
$changelog = file_get_contents($pluginRoot . '/changelog.md');
$plugin = file_get_contents($pluginRoot . '/Plugin.php');
$failures = array();

foreach (array(
    '## Features',
    '## Screenshots',
    '## Usage',
    '## Installation & Compatibility',
    '## Security & Privacy',
    '## Troubleshooting',
    '## Authors & Contributors',
    'TTJ (ThomasTJ)',
    'rel="noopener noreferrer"',
) as $snippet) {
    if (strpos($readme, $snippet) === false) {
        $failures[] = 'Missing README contract: ' . $snippet;
    }
}

if (strpos($changelog, 'Version 0.1.4') === false || strpos($changelog, 'issue #2') === false) {
    $failures[] = 'The release history does not record the hardening and issue closure.';
}
if (strpos($plugin, "return '0.1.4';") === false) {
    $failures[] = 'Plug-in metadata does not match the documented release.';
}

if ($failures !== array()) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo 'BoardNotes documentation contract passed.' . PHP_EOL;
