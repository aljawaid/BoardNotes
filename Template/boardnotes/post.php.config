<?php
/* Rename file to post.php */
$client = new JsonRPC\Client('http://URL/kanboard/jsonrpc.php');
$client->authentication('jsonrpc', 'API_FROM_SETTINGS_IN_KANBOARD');
$client->execute('createTask', ['title' => $title, 'description' => $description, 'project_id' => $project_id, 'column_id' => $column_id, 'category_id' => $category_id, 'svimlane_id' => $svimlane_id]);
?>
