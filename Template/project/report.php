<?php

/**
 * Render a printable, escaped BoardNotes report.
 *
 * Related test: tests/OutputSecurityContractTest.php.
 */

?>

<?= $this->asset->css('plugins/BoardNotes/Assets/css/style.css') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/boardnotes.js') ?>

<div class="page-header">
    <h2><?= t('boardnotes.report_for', $this->text->e($project['name'])) ?></h2>
</div>

<form method="get" action="<?= $this->url->href('BoardNotesController', 'boardNotesReport') ?>" class="boardnotes__actions">
    <input type="hidden" name="controller" value="BoardNotesController">
    <input type="hidden" name="action" value="boardNotesReport">
    <input type="hidden" name="plugin" value="BoardNotes">
    <input type="hidden" name="project_cus_id" value="<?= (int) $project_id ?>">
    <label for="boardnotes-report-category"><?= t('boardnotes.category') ?></label>
    <select id="boardnotes-report-category" name="category">
        <option value=""><?= t('boardnotes.all_categories') ?></option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $this->text->e($category['name']) ?>"<?= $selected_category === $category['name'] ? ' selected' : '' ?>>
                <?= $this->text->e($category['name']) ?>
            </option>
        <?php endforeach ?>
    </select>
    <button type="submit" class="btn"><?= t('boardnotes.filter') ?></button>
    <button type="button" class="btn" data-boardnotes-print><?= t('boardnotes.print') ?></button>
</form>

<?php if (empty($data)): ?>
    <p class="boardnotes__empty"><?= t('boardnotes.empty_report') ?></p>
<?php else: ?>
    <table class="table-striped">
        <thead>
            <tr>
                <th><?= t('boardnotes.title') ?></th>
                <th><?= t('boardnotes.description_label') ?></th>
                <th><?= t('boardnotes.category') ?></th>
                <th><?= t('boardnotes.status') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $note): ?>
                <?php
                $status = (int) $note['is_active'];
                $statusLabel = $status === 0 ? t('boardnotes.completed') : ($status === 2 ? t('boardnotes.in_progress') : t('boardnotes.open'));
                ?>
                <tr>
                    <td><?= $this->text->e($note['title']) ?></td>
                    <td class="boardnotes-report__description"><?= $this->text->e($note['description']) ?></td>
                    <td><?= $this->text->e($note['category']) ?></td>
                    <td><?= $this->text->e($statusLabel) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>

<p>
    <a class="btn" href="<?= $project['is_custom']
        ? $this->url->href('BoardNotesController', 'boardNotesShowAll', array('plugin' => 'BoardNotes'))
        : $this->url->href('BoardNotesController', 'boardNotesShowProject', array('plugin' => 'BoardNotes', 'project_id' => $project_id)) ?>">
        <?= t('boardnotes.back_to_notes') ?>
    </a>
</p>
