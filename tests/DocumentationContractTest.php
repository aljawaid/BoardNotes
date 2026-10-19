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
$normalisedChangelog = str_replace("\r\n", "\n", $changelog);

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

foreach (array(
    '## v1.0.0',
    '- FIX: Finalise BoardNotes 1.0.0 Release Metadata',
    '## v0.1.4',
    '- FIX: BUG: Contain BoardNotes Narrow Layout',
    '## v0.1.3',
    '- FIX: BUG: Correct BoardNotes Task Redirect',
    '## v0.1.2',
    '- FIX: BUG: Repair BoardNotes Database Installation',
    '## v0.1.1',
    '- DOCS: Standardise BoardNotes Changelog Filename',
    '## v0.1.0',
    '- FIX: DEPENDENCY: Modernise BoardNotes Quality Gates',
    '- FIX: BUG: Harden BoardNotes Database Lifecycle',
    '- SECURITY FIX: Modernise BoardNotes Workflows And Interface',
    '- SECURITY FIX: Enforce BoardNotes Ownership and Project Access',
    '- FIX: Remove BoardNotes Stylelint Findings',
    '- FIX: Remove BoardNotes PHPCS Findings',
    '- FIX: Declare Current Kanboard Compatibility',
) as $releaseHistoryEntry) {
    if (strpos($changelog, $releaseHistoryEntry) === false) {
        $failures[] = 'Missing changelog contract: ' . $releaseHistoryEntry;
    }
}
if (strpos(
    $normalisedChangelog,
    "# Changelog\n\n## v1.0.0\n\n_(most recent changes are listed on top):_\n- "
) !== 0) {
    $failures[] = 'The changelog does not use the shared family heading and list format.';
}
if (preg_match('/^\* /m', $normalisedChangelog) === 1) {
    $failures[] = 'The changelog must use hyphen list markers.';
}
preg_match_all('/^## v/m', $normalisedChangelog, $versionHeadings);
preg_match_all('/\n\n\n## v/', $normalisedChangelog, $versionSeparators);
if (count($versionHeadings[0]) !== count($versionSeparators[0]) + 1) {
    $failures[] = 'The changelog must keep two blank lines between version sections.';
}
if (strpos($plugin, "return '1.0.0';") === false) {
    $failures[] = 'Plug-in metadata does not match the documented release.';
}

if ($failures !== array()) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo 'BoardNotes documentation contract passed.' . PHP_EOL;
