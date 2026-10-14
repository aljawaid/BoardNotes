<?php

// Reuse Kanboard's dashboard sidebar for the current user's private notes.
?>

<li <?= $this->app->checkMenuSelection('BoardNotesController', 'boardNotesShowAll') ?>>
    <?= $this->url->link(t('boardnotes.my_notes'), 'BoardNotesController', 'boardNotesShowAll', array('plugin' => 'BoardNotes')) ?>
</li>
