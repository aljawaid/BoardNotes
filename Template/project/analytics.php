<?php

/**
 * Render dependency-free and empty-safe note analytics.
 *
 * Related test: tests/OutputSecurityContractTest.php.
 */

$counts = array(0 => 0, 1 => 0, 2 => 0);
foreach ($analyticsData as $note) {
    $status = (int) $note['is_active'];
    if (isset($counts[$status])) {
        $counts[$status]++;
    }
}
$total = array_sum($counts);
$statuses = array(
    1 => t('boardnotes.open'),
    2 => t('boardnotes.in_progress'),
    0 => t('boardnotes.completed'),
);
?>

<?= $this->asset->css('plugins/BoardNotes/Assets/css/style.css') ?>

<div class="page-header">
    <h2><?= t('boardnotes.analytics_for', $this->text->e($project['name'])) ?></h2>
</div>

<?php if ($total === 0): ?>
    <p class="boardnotes__empty"><?= t('boardnotes.empty_analytics') ?></p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th><?= t('boardnotes.status') ?></th>
                <th><?= t('boardnotes.notes') ?></th>
                <th><?= t('boardnotes.percentage') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($statuses as $status => $label): ?>
                <tr>
                    <td><?= $this->text->e($label) ?></td>
                    <td><?= (int) $counts[$status] ?></td>
                    <td><?= number_format(($counts[$status] / $total) * 100, 1) ?>%</td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>

<p>
    <a class="btn" href="<?= $project['is_custom']
        ? $this->url->href('BoardNotesController', 'boardNotesShowAll', array('plugin' => 'BoardNotes'))
        : $this->url->href('BoardNotesController', 'boardNotesShowProject', array('plugin' => 'BoardNotes', 'project_id' => $project['id'])) ?>">
        <?= t('boardnotes.back_to_notes') ?>
    </a>
</p>
