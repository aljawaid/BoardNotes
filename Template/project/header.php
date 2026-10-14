<?php

// Reuse Kanboard's project view switcher for direct access to project notes.
?>

<li <?= $this->app->checkMenuSelection('BoardNotesController') ?>>
    <?= $this->url->icon('sticky-note-o', t('boardnotes.notes'), 'BoardNotesController', 'boardNotesShowProject', array('project_id' => $project['id'], 'plugin' => 'BoardNotes')) ?>
</li>
