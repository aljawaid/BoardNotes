<?php
$client = new JsonRPC\Client('http://URL/kanboard/jsonrpc.php');
$client->authentication('jsonrpc', 'API_FROM_SETTINGS_IN_KANBOARD');
$client->execute('createTask', ['title' => $task_title, 'description' => $task_description, 'project_id' => $project_id, 'column_id' => $column_id, 'category_id' => $category_id, 'swimlane_id' => $swimlane_id]);
?>

