<?php

// Reuse Kanboard's project menu for direct access to project notes.
?>

<li>
    <?= $this->url->icon('sticky-note-o', t('boardnotes.notes'), 'BoardNotesController', 'boardNotesShowProject', array('project_id' => $project['id'], 'plugin' => 'BoardNotes')) ?>
</li>
