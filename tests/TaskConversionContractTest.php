<?php

/**
 * Verify task conversion cannot discard a note before successful task creation.
 *
 * Related file: Controller/BoardNotesController.php.
 */

$controller = file_get_contents(dirname(__DIR__) . '/Controller/BoardNotesController.php');
$creationPosition = strpos($controller, '$this->taskCreationModel->create(');
$successCheckPosition = strpos($controller, 'if (! $task_id)');
$deletionPosition = strpos($controller, '$this->boardNotesModel->boardNotesDeleteNote(', $creationPosition);
$failures = array();

if ($creationPosition === false || $successCheckPosition === false || $deletionPosition === false) {
    $failures[] = 'The task conversion success sequence is incomplete.';
} elseif (! ($creationPosition < $successCheckPosition && $successCheckPosition < $deletionPosition)) {
    $failures[] = 'The source note can be deleted before task creation succeeds.';
}

foreach (array(
    "\$this->request->getRawValue('remove_note') === '1'",
    "'note_removed' => \$note_removed",
    "'task_id' => (int) \$task_id",
    "\$this->helper->url->to('TaskViewController', 'show'",
) as $snippet) {
    if (strpos($controller, $snippet) === false) {
        $failures[] = 'Missing task conversion contract: ' . $snippet;
    }
}

if (strpos($controller, "'task_url' => \$this->helper->url->href(") !== false) {
    $failures[] = 'The JSON task redirect still contains an HTML-escaped URL.';
}

if ($failures !== array()) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo 'BoardNotes task conversion contract passed.' . PHP_EOL;
