<?php if ($this->user->isAdmin() ) { ?>
    <li>
	 <?= $this->url->link(t('Boardnotes'), 'BoardnotesController', 'boardnotesShowProject', array('plugin' => 'boardnotes', 'project_id' => $project['id'])) ?>

    </li>
<?php } ?>
