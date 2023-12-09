<li <?= $this->app->checkMenuSelection('BoardNotesController', 'boardNotesShowAll') ?>>
    <?= $this->url->link(t('My notes'), 'BoardNotesController', 'boardNotesShowAll', array('user_id' => $user['id'], 'plugin' => 'BoardNotes')) ?>
</li>
