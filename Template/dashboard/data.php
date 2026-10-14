<?php

/**
 * Render the personal BoardNotes dashboard and project filter.
 *
 * Related test: tests/OutputSecurityContractTest.php.
 */

$tabId = isset($tab_id) ? (int) $tab_id : 0;
$selectedProject = array('id' => 0, 'name' => t('boardnotes.all_notes'), 'is_custom' => true);

if ($tabId > 0 && isset($projectsAccess[$tabId - 1])) {
    $projectAccess = $projectsAccess[$tabId - 1];
    $selectedProject = array(
        'id' => (int) $projectAccess['project_id'],
        'name' => $projectAccess['project_name'],
        'is_custom' => (bool) $projectAccess['is_custom'],
    );
}
?>

<div class="page-header">
    <h2><?= t('boardnotes.my_notes') ?></h2>
</div>

<div class="sidebar-container">
    <aside class="sidebar" aria-label="<?= t('boardnotes.filter_by_project') ?>">
        <ul>
            <li<?= $tabId === 0 ? ' class="active"' : '' ?>>
                <a href="<?= $this->url->href('BoardNotesController', 'boardNotesShowAll', array('plugin' => 'BoardNotes', 'tab_id' => 0)) ?>"><?= t('boardnotes.all_notes') ?></a>
            </li>
            <?php foreach ($projectsAccess as $index => $projectAccess): ?>
                <li<?= $tabId === $index + 1 ? ' class="active"' : '' ?>>
                    <a href="<?= $this->url->href('BoardNotesController', 'boardNotesShowAll', array('plugin' => 'BoardNotes', 'tab_id' => $index + 1)) ?>">
                        <?= $this->text->e($projectAccess['project_name']) ?>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </aside>

    <main class="sidebar-content">
        <?= $this->render('BoardNotes:project/data', array(
            'projectsAccess' => $projectsAccess,
            'project' => $selectedProject,
            'project_id' => $selectedProject['id'],
            'user' => $user,
            'user_id' => $user_id,
            'is_refresh' => false,
            'is_dashboard_view' => true,
            'data' => $data,
            'categories' => $categories,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
        )) ?>
    </main>
</div>
