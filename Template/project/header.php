<li <?= $this->app->checkMenuSelection('BoardNotesController') ?>>
    <?= $this->url->icon('wpforms', t('Notes'), 'BoardNotesController', 'boardNotesShowProject', array('project_id' => $project['id'], 'plugin' => 'BoardNotes')) ?>
</li>