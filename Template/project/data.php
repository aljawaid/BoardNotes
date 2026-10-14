<?php

/**
 * Render the BoardNotes workspace with server-generated endpoints.
 *
 * Related test: tests/OutputSecurityContractTest.php.
 */

$readonlyNotes = (int) $project_id === 0;
$visibleNotes = array();
$projectNames = array();
$categoryColors = array();

foreach ($projectsAccess as $projectAccess) {
    $projectNames[(int) $projectAccess['project_id']] = $projectAccess['project_name'];
}

foreach ($categories as $category) {
    $categoryColors[$category['name']] = $category['color_id'];
}

foreach ($data as $note) {
    if ($readonlyNotes || (int) $note['project_id'] === (int) $project_id) {
        $visibleNotes[] = $note;
    }
}

$csrfToken = $this->app->getToken()->getReusableCSRFToken();
?>

<?= $this->asset->css('plugins/BoardNotes/Assets/css/style.css') ?>
<?= $this->asset->js('plugins/BoardNotes/Assets/js/boardnotes.js') ?>

<?php if (!$is_dashboard_view): ?>
    <?= $this->projectHeader->render($project, 'BoardNotesController', 'boardNotesShowProject', false, 'BoardNotes') ?>
<?php endif ?>

<section class="boardnotes"
         data-boardnotes-root
         data-reorder-url="<?= $this->url->href('BoardNotesController', 'boardNotesUpdatePosition', array('plugin' => 'BoardNotes', 'project_cus_id' => $project_id)) ?>"
         data-csrf-token="<?= $this->text->e($csrfToken) ?>"
         data-request-error="<?= t('boardnotes.request_failed') ?>"
         data-order-error="<?= t('boardnotes.order_failed') ?>"
         aria-labelledby="boardnotes-title-<?= (int) $project_id ?>">
    <header class="boardnotes__header">
        <div>
            <h2 id="boardnotes-title-<?= (int) $project_id ?>">
                <?= $this->text->e($readonlyNotes ? t('boardnotes.all_notes') : $project['name']) ?>
            </h2>
            <p class="boardnotes__summary">
                <?= e('boardnotes.note_count', count($visibleNotes)) ?>
            </p>
        </div>

        <nav class="boardnotes__actions" aria-label="<?= t('boardnotes.note_tools') ?>">
            <?php if (!$readonlyNotes): ?>
                <a class="btn" href="<?= $this->url->href('BoardNotesController', 'boardNotesAnalytics', array('plugin' => 'BoardNotes', 'project_cus_id' => $project_id)) ?>">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i> <?= t('boardnotes.analytics') ?>
                </a>
                <a class="btn" href="<?= $this->url->href('BoardNotesController', 'boardNotesReport', array('plugin' => 'BoardNotes', 'project_cus_id' => $project_id)) ?>">
                    <i class="fa fa-print" aria-hidden="true"></i> <?= t('boardnotes.print_report') ?>
                </a>
                <form method="post"
                      action="<?= $this->url->href('BoardNotesController', 'boardNotesDeleteAllDoneNotes', array('plugin' => 'BoardNotes', 'project_cus_id' => $project_id)) ?>"
                      data-boardnotes-form
                      data-confirm="<?= t('boardnotes.delete_completed_confirm') ?>">
                    <input type="hidden" name="csrf_token" value="<?= $this->text->e($csrfToken) ?>">
                    <button type="submit" class="btn btn-red">
                        <i class="fa fa-trash" aria-hidden="true"></i> <?= t('boardnotes.delete_completed') ?>
                    </button>
                </form>
            <?php endif ?>
            <button type="button" class="btn" data-boardnotes-expand><?= t('boardnotes.expand_all') ?></button>
            <button type="button" class="btn" data-boardnotes-collapse><?= t('boardnotes.collapse_all') ?></button>
        </nav>
    </header>

    <div class="alert alert-error boardnotes__message" role="alert" data-boardnotes-message hidden></div>

    <?php if (!$readonlyNotes): ?>
        <form class="boardnotes__new"
              method="post"
              action="<?= $this->url->href('BoardNotesController', 'boardNotesAddNote', array('plugin' => 'BoardNotes', 'project_cus_id' => $project_id)) ?>"
              data-boardnotes-form>
            <input type="hidden" name="csrf_token" value="<?= $this->text->e($csrfToken) ?>">
            <div class="form-column">
                <label for="boardnotes-new-title-<?= (int) $project_id ?>"><?= t('boardnotes.new_note') ?></label>
                <input id="boardnotes-new-title-<?= (int) $project_id ?>"
                       name="title"
                       type="text"
                       maxlength="255"
                       required
                       placeholder="<?= t('boardnotes.new_note_placeholder') ?>">
            </div>
            <div class="form-column">
                <label for="boardnotes-new-description-<?= (int) $project_id ?>"><?= t('boardnotes.description_label') ?></label>
                <textarea id="boardnotes-new-description-<?= (int) $project_id ?>" name="description" rows="3"></textarea>
            </div>
            <?php if (!empty($categories)): ?>
                <div class="form-column">
                    <label for="boardnotes-new-category-<?= (int) $project_id ?>"><?= t('boardnotes.category') ?></label>
                    <select id="boardnotes-new-category-<?= (int) $project_id ?>" name="category_id">
                        <option value=""><?= t('boardnotes.no_category') ?></option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>"><?= $this->text->e($category['name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            <?php endif ?>
            <button type="submit" class="btn btn-blue"><?= t('boardnotes.add_note') ?></button>
        </form>
    <?php endif ?>

    <?php if (empty($visibleNotes)): ?>
        <p class="boardnotes__empty"><?= t('boardnotes.empty_view') ?></p>
    <?php else: ?>
        <ol class="boardnotes__list" data-boardnotes-list>
            <?php foreach ($visibleNotes as $note): ?>
                <?php
                $noteProjectId = (int) $note['project_id'];
                $isCompleted = (int) $note['is_active'] === 0;
                $categoryColor = isset($categoryColors[$note['category']]) ? $categoryColors[$note['category']] : '';
                ?>
                <li class="boardnotes__item<?= $isCompleted ? ' boardnotes__item--completed' : '' ?><?= $categoryColor !== '' ? ' color-' . $this->text->e($categoryColor) : '' ?>"
                    data-note-id="<?= (int) $note['id'] ?>"
                    draggable="<?= $readonlyNotes ? 'false' : 'true' ?>">
                    <?php if ($readonlyNotes && isset($projectNames[$noteProjectId])): ?>
                        <p class="boardnotes__project"><?= $this->text->e($projectNames[$noteProjectId]) ?></p>
                    <?php endif ?>

                    <div class="boardnotes__item-header">
                        <form method="post"
                              action="<?= $this->url->href('BoardNotesController', 'boardNotesToggleNote', array('plugin' => 'BoardNotes', 'project_cus_id' => $noteProjectId)) ?>"
                              data-boardnotes-form>
                            <input type="hidden" name="csrf_token" value="<?= $this->text->e($csrfToken) ?>">
                            <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
                            <input type="hidden" name="is_active" value="<?= $isCompleted ? 1 : 0 ?>">
                            <button type="submit" class="boardnotes__status" aria-label="<?= $isCompleted ? t('boardnotes.reopen_note') : t('boardnotes.complete_note') ?>">
                                <i class="fa <?= $isCompleted ? 'fa-check-circle' : 'fa-circle-o' ?>" aria-hidden="true"></i>
                            </button>
                        </form>
                        <strong><?= $this->text->e($note['title']) ?></strong>
                        <?php if ($note['category'] !== ''): ?>
                            <span class="boardnotes__category"><?= $this->text->e($note['category']) ?></span>
                        <?php endif ?>
                        <details class="boardnotes__details">
                            <summary><?= t('boardnotes.details') ?></summary>
                            <?php if (!$readonlyNotes): ?>
                                <form method="post"
                                      action="<?= $this->url->href('BoardNotesController', 'boardNotesUpdateNote', array('plugin' => 'BoardNotes', 'project_cus_id' => $noteProjectId)) ?>"
                                      data-boardnotes-form>
                                    <input type="hidden" name="csrf_token" value="<?= $this->text->e($csrfToken) ?>">
                                    <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
                                    <input type="hidden" name="is_active" value="<?= (int) $note['is_active'] ?>">
                                    <div class="form-column">
                                        <label for="boardnotes-title-<?= (int) $note['id'] ?>"><?= t('boardnotes.title') ?></label>
                                        <input id="boardnotes-title-<?= (int) $note['id'] ?>" name="title" type="text" maxlength="255" required value="<?= $this->text->e($note['title']) ?>">
                                    </div>
                                    <div class="form-column">
                                        <label for="boardnotes-description-<?= (int) $note['id'] ?>"><?= t('boardnotes.description_label') ?></label>
                                        <textarea id="boardnotes-description-<?= (int) $note['id'] ?>" name="description" rows="5"><?= $this->text->e($note['description']) ?></textarea>
                                    </div>
                                    <?php if (!empty($categories)): ?>
                                        <div class="form-column">
                                            <label for="boardnotes-category-<?= (int) $note['id'] ?>"><?= t('boardnotes.category') ?></label>
                                            <select id="boardnotes-category-<?= (int) $note['id'] ?>" name="category_id">
                                                <option value=""><?= t('boardnotes.no_category') ?></option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= (int) $category['id'] ?>"<?= $category['name'] === $note['category'] ? ' selected' : '' ?>><?= $this->text->e($category['name']) ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    <?php endif ?>
                                    <button type="submit" class="btn btn-blue"><?= t('boardnotes.save_changes') ?></button>
                                </form>

                                <div class="boardnotes__secondary-actions">
                                    <form method="post"
                                          action="<?= $this->url->href('BoardNotesController', 'boardNotesTransferNote', array('plugin' => 'BoardNotes', 'project_cus_id' => $noteProjectId)) ?>"
                                          data-boardnotes-form>
                                        <input type="hidden" name="csrf_token" value="<?= $this->text->e($csrfToken) ?>">
                                        <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
                                        <label for="boardnotes-transfer-<?= (int) $note['id'] ?>"><?= t('boardnotes.move_to_project') ?></label>
                                        <select id="boardnotes-transfer-<?= (int) $note['id'] ?>" name="target_project_id" required>
                                            <?php foreach ($projectsAccess as $targetProject): ?>
                                                <?php if ((int) $targetProject['project_id'] !== $noteProjectId): ?>
                                                    <option value="<?= (int) $targetProject['project_id'] ?>"><?= $this->text->e($targetProject['project_name']) ?></option>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        </select>
                                        <button type="submit" class="btn"><?= t('boardnotes.move') ?></button>
                                    </form>

                                    <?php if (!$project['is_custom'] && $this->user->hasProjectAccess('TaskCreationController', 'show', $noteProjectId)): ?>
                                        <form method="post"
                                              action="<?= $this->url->href('BoardNotesController', 'boardNotesToTask', array('plugin' => 'BoardNotes', 'project_cus_id' => $noteProjectId)) ?>"
                                              data-boardnotes-form>
                                            <input type="hidden" name="csrf_token" value="<?= $this->text->e($csrfToken) ?>">
                                            <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
                                            <label for="boardnotes-column-<?= (int) $note['id'] ?>"><?= t('boardnotes.create_task_in_column') ?></label>
                                            <select id="boardnotes-column-<?= (int) $note['id'] ?>" name="column_id" required>
                                                <?php foreach ($columns as $column): ?>
                                                    <option value="<?= (int) $column['id'] ?>"><?= $this->text->e($column['title']) ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <select name="swimlane_id" aria-label="<?= t('boardnotes.swimlane') ?>">
                                                <option value="0"><?= t('boardnotes.default_swimlane') ?></option>
                                                <?php foreach ($swimlanes as $swimlane): ?>
                                                    <option value="<?= (int) $swimlane['id'] ?>"><?= $this->text->e($swimlane['name']) ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <select name="category_id" aria-label="<?= t('boardnotes.category') ?>">
                                                <option value="0"><?= t('boardnotes.no_category') ?></option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= (int) $category['id'] ?>"<?= $category['name'] === $note['category'] ? ' selected' : '' ?>><?= $this->text->e($category['name']) ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label class="boardnotes__choice">
                                                <input type="checkbox" name="remove_note" value="1">
                                                <?= t('boardnotes.remove_after_task') ?>
                                                <span class="boardnotes__help" title="<?= t('boardnotes.remove_after_task_help') ?>" aria-label="<?= t('boardnotes.remove_after_task_help') ?>">
                                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                </span>
                                            </label>
                                            <button type="submit" class="btn"><?= t('boardnotes.create_task') ?></button>
                                        </form>
                                    <?php endif ?>

                                    <form method="post"
                                          action="<?= $this->url->href('BoardNotesController', 'boardNotesDeleteNote', array('plugin' => 'BoardNotes', 'project_cus_id' => $noteProjectId)) ?>"
                                          data-boardnotes-form
                                          data-confirm="<?= t('boardnotes.delete_note_confirm') ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $this->text->e($csrfToken) ?>">
                                        <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
                                        <button type="submit" class="btn btn-red"><?= t('boardnotes.delete_note') ?></button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <p class="boardnotes__description"><?= nl2br($this->text->e($note['description'])) ?></p>
                            <?php endif ?>
                        </details>
                    </div>
                    <?php if (!$readonlyNotes): ?>
                        <div class="boardnotes__order" aria-label="<?= t('boardnotes.change_position') ?>">
                            <button type="button" class="btn" data-boardnotes-up aria-label="<?= t('boardnotes.move_up') ?>"><i class="fa fa-arrow-up" aria-hidden="true"></i></button>
                            <button type="button" class="btn" data-boardnotes-down aria-label="<?= t('boardnotes.move_down') ?>"><i class="fa fa-arrow-down" aria-hidden="true"></i></button>
                        </div>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ol>
    <?php endif ?>
</section>
