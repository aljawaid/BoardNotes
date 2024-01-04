<style>
table {
    width: initial !important;
}
</style>

<?php

if ($task_id > 0) {
    print '<strong>Success!</strong>';
    print '<br>';
    print 'Created task <a target="_blank" title="Opens in a new window ⇗" href="task/' . $task_id . '">';
    print '<strong> #' . $task_id . ' </strong><i class="fa fa-external-link" aria-hidden="true"></i></a> as:';
} else {
    print '<strong>Ooops, something went wrong ;/</strong>';
    print '<br>';
    print 'The task could not be created for:';
}

    print '<br>';
    print '<br>';
    print '<table>';

    print '<tr><td align="right"><strong>[project] : </strong></td><td>' . $project_name . '</td></tr>';
if ($task_id > 0) {
    print '<tr><td align="right"><strong>[creator] : </strong></td><td>' . $user_name . '</td></tr>';
    print '<tr><td align="right"><strong>[owner] : </strong></td><td>' . $user_name . '</td></tr>';
} else {
    print '<tr><td align="right"><strong>[user] : </strong></td><td>' . $user_name . '</td></tr>';
}
    print '<tr><td align="right"><strong>[title] : </strong></td><td>' . $task_title . '</td></tr>';
    print '<tr><td align="right"><strong>[description] : </strong></td><td>' . $task_description . '</td></tr>';
    print '<tr><td align="right"><strong>[category] : </strong></td><td>' . $category . '</td></tr>';
    print '<tr><td align="right"><strong>[column] : </strong></td><td>' . $column . '</td></tr>';
    print '<tr><td align="right"><strong>[swimlane] : </strong></td><td>' . $swimlane . '</td></tr>';

    print '</table>';
?>