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

    print '<strong>[project] : </strong>' . $project_name;
    print '<br>';
if ($task_id > 0) {
    print '<strong>[creator] : </strong>' . $user_name;
    print '<br>';
    print '<strong>[owner] : </strong>' . $user_name;
    print '<br>';
} else {
    print '<strong>[user] : </strong>' . $user_name;
    print '<br>';
}
    print '<strong>[title] : </strong>' . $task_title;
    print '<br>';
    print '<strong>[description] : </strong>' . $task_description;
    print '<br>';
    print '<strong>[category] : </strong>' . $category;
    print '<br>';
    print '<strong>[column] : </strong>' . $column;
    print '<br>';
    print '<strong>[swimlane] : </strong>' . $swimlane;
    print '<br>';

?>