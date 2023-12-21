<?php
$client = new JsonRPC\Client('http://URL/kanboard/jsonrpc.php');
$client->authentication('jsonrpc', 'API_FROM_SETTINGS_IN_KANBOARD');
$client->execute('createTask', [
        'project_id' => $project_id,
        'user_id' => $user_id,
        'title' => $task_title,
        'description' => $task_description,
        'category_id' => $category_id,
        'column_id' => $column_id,
        'swimlane_id' => $swimlane_id
    ]);
?>

