<?php if ($this->user->isAdmin()): ?>
    <li class="">
	 <?= $this->url->link(t('Boardnotes'), 'BoardNotesController', 'boardNotesShowProject', array('project_id' => $project['id']'plugin' => 'BoardNotes')) ?>
    </li>
<?php endif ?>
