<?php

/**
 * Verify the SQLite fresh-install and upgrade-safe BoardNotes schema.
 *
 * Related file: Schema/Sqlite.php.
 */

require_once dirname(__DIR__) . '/Schema/Sqlite.php';

use function Kanboard\Plugin\BoardNotes\Schema\version_1;
use function Kanboard\Plugin\BoardNotes\Schema\version_2;

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY)');
$pdo->exec('CREATE TABLE projects (id INTEGER PRIMARY KEY)');

version_1($pdo);
version_2($pdo);

$customProjects = $pdo->query('SELECT project_name FROM boardnotes_cus ORDER BY project_id')->fetchAll(PDO::FETCH_COLUMN);
if ($customProjects !== array('Todo', 'General')) {
    throw new RuntimeException('Default custom projects were not created portably.');
}

$pdo->exec('INSERT INTO users (id) VALUES (1), (2)');
$pdo->exec('INSERT INTO projects (id) VALUES (10), (20)');
$pdo->exec("INSERT INTO boardnotes (project_id, user_id, position, is_active, title) VALUES (10, 1, 1, 1, 'User removal'), (20, 2, 1, 1, 'Project removal')");
$pdo->exec('DELETE FROM users WHERE id = 1');
$pdo->exec('DELETE FROM projects WHERE id = 20');

if ((int) $pdo->query('SELECT COUNT(*) FROM boardnotes')->fetchColumn() !== 0) {
    throw new RuntimeException('User or project lifecycle cleanup left orphaned notes.');
}

$indexes = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'index'")->fetchAll(PDO::FETCH_COLUMN);
foreach (array('boardnotes_cus_project_id_idx', 'boardnotes_owner_project_position_idx') as $index) {
    if (! in_array($index, $indexes, true)) {
        throw new RuntimeException('Missing schema index: ' . $index);
    }
}

echo 'BoardNotes SQLite migration contract passed.' . PHP_EOL;
