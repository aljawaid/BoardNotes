<?php

/**
 * Verify BoardNotes ownership, project access and task-relation boundaries.
 *
 * Related files: Plugin.php, Controller/BoardNotesController.php and
 * Model/BoardNotesModel.php.
 */

$pluginRoot = dirname(__DIR__);
$plugin = file_get_contents($pluginRoot . '/Plugin.php');
$controller = file_get_contents($pluginRoot . '/Controller/BoardNotesController.php');
$model = file_get_contents($pluginRoot . '/Model/BoardNotesModel.php');
$failures = array();

$requiredControllerSnippets = array(
    'return (int) $this->userSession->getId();',
    'boardNotesGetAccessibleProject($project_id, $user_id)',
    "hasProjectAccess('TaskCreationController', 'show', \$project_id)",
    'boardNotesGetNote($project_id, $user_id, $note_id)',
    "(int) \$column['project_id'] !== (int) \$project_id",
    "(int) \$category['project_id'] !== (int) \$project_id",
    "(int) \$swimlane['project_id'] !== (int) \$project_id",
);

foreach ($requiredControllerSnippets as $snippet) {
    if (strpos($controller, $snippet) === false) {
        $failures[] = 'Missing controller access contract: ' . $snippet;
    }
}

$forbiddenControllerSnippets = array(
    "getStringParam('user_id')",
    "getIntegerParam('user_id')",
    "\$_SESSION['cached_user_id']",
    "getStringParam('task_title')",
    "getStringParam('task_description')",
);

foreach ($forbiddenControllerSnippets as $snippet) {
    if (strpos($controller, $snippet) !== false) {
        $failures[] = 'Unsafe client-controlled identity or task content remains: ' . $snippet;
    }
}

foreach (array(
    "->eq('project_id', \$project_id)",
    "->eq('user_id', \$user_id)",
    'boardNotesGetAccessibleProject',
    'getActiveProjectsByUser',
    'userSession->isAdmin()',
) as $snippet) {
    if (strpos($model, $snippet) === false) {
        $failures[] = 'Missing model ownership or membership contract: ' . $snippet;
    }
}

foreach (array(
    "projectAccessMap->add('BoardNotesController', '*', Role::PROJECT_VIEWER)",
    "projectAccessMap->add('BoardNotesController', 'boardNotesToTask', Role::PROJECT_MEMBER)",
) as $snippet) {
    if (strpos($plugin, $snippet) === false) {
        $failures[] = 'Missing project role contract: ' . $snippet;
    }
}

if ($failures !== array()) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo 'BoardNotes authorisation contract passed.' . PHP_EOL;
