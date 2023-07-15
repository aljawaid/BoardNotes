<p class="page-headerName">Boardnotes</p>
<?= $this->render('Boardnotes:boardnotes/dataSingle', array(
    'project' => $project,
        'data' => $data,
    'categories' => $categories,
    'columns' => $columns,
        'swimlanes' => $swimlanes,
)) ?>

