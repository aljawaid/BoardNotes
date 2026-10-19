<?php

/**
 * Verify retry-safe SQLite migrations and portable BoardNotes orphan cleanup.
 *
 * Related files: Schema/Sqlite.php, Schema/Mysql.php, Schema/Postgres.php,
 * Model/BoardNotesModel.php and Plugin.php.
 * Related support: tests/Support.
 */

use Kanboard\Plugin\BoardNotes\Model\BoardNotesModel;
use Kanboard\Plugin\BoardNotes\Tests\Support\TestDatabase;

use function Kanboard\Plugin\BoardNotes\Schema\version_1;
use function Kanboard\Plugin\BoardNotes\Schema\version_2;
use function Kanboard\Plugin\BoardNotes\Schema\version_3;

require_once __DIR__ . '/Support/Kanboard/Core/Base.php';
require_once __DIR__ . '/Support/Kanboard/Model/ProjectModel.php';
require_once __DIR__ . '/Support/Kanboard/Model/TaskModel.php';
require_once __DIR__ . '/Support/Kanboard/Model/ColumnModel.php';
require_once __DIR__ . '/Support/Kanboard/Model/SwimlaneModel.php';
require_once __DIR__ . '/Support/Kanboard/Model/ProjectUserRoleModel.php';
require_once __DIR__ . '/Support/Kanboard/Model/CategoryModel.php';
require_once __DIR__ . '/Support/TestDatabase.php';
require_once dirname(__DIR__) . '/Schema/Sqlite.php';
require_once dirname(__DIR__) . '/Model/BoardNotesModel.php';

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY)');
$pdo->exec('CREATE TABLE projects (id INTEGER PRIMARY KEY)');

version_1($pdo);
version_1($pdo);
version_2($pdo);
version_2($pdo);
version_3($pdo);

$customProjects = $pdo
    ->query('SELECT project_name FROM boardnotes_cus ORDER BY project_id')
    ->fetchAll(PDO::FETCH_COLUMN);
if ($customProjects !== array('Todo', 'General')) {
    throw new RuntimeException('Default custom projects were not created portably.');
}

$pdo->exec('INSERT INTO users (id) VALUES (1), (2)');
$pdo->exec('INSERT INTO projects (id) VALUES (10), (20)');
$pdo->exec("INSERT INTO boardnotes (project_id, user_id, position, is_active, title) VALUES
    (10, 1, 1, 1, 'User removal'),
    (20, 2, 1, 1, 'Project removal'),
    (9998, 2, 1, 1, 'Custom project')");
$pdo->exec('DELETE FROM users WHERE id = 1');
$pdo->exec('DELETE FROM projects WHERE id = 20');

$model = new BoardNotesModel();
$model->db = new TestDatabase($pdo);
if (!$model->boardNotesCleanupOrphans()) {
    throw new RuntimeException('Portable orphan cleanup reported a failure.');
}

$remainingNotes = $pdo
    ->query('SELECT title FROM boardnotes ORDER BY id')
    ->fetchAll(PDO::FETCH_COLUMN);
if ($remainingNotes !== array('Custom project')) {
    throw new RuntimeException('Orphan cleanup removed a custom-project note or retained an orphan.');
}

$indexes = $pdo
    ->query("SELECT name FROM sqlite_master WHERE type = 'index'")
    ->fetchAll(PDO::FETCH_COLUMN);
$expectedIndexes = array(
    'boardnotes_cus_project_id_idx',
    'boardnotes_owner_project_position_idx',
    'boardnotes_project_idx',
);
foreach ($expectedIndexes as $index) {
    if (!in_array($index, $indexes, true)) {
        throw new RuntimeException('Missing schema index: ' . $index);
    }
}

$mysqlSchema = file_get_contents(dirname(__DIR__) . '/Schema/Mysql.php');
$pluginSource = file_get_contents(dirname(__DIR__) . '/Plugin.php');
if (
    strpos($mysqlSchema, 'CREATE TRIGGER') !== false
    || strpos($mysqlSchema, 'mysqlIndexExists') === false
) {
    throw new RuntimeException('MySQL migration still requires privileged triggers or lacks retry-safe indexes.');
}
if (strpos($pluginSource, 'boardNotesCleanupOrphans') === false) {
    throw new RuntimeException('Application startup does not invoke portable orphan cleanup.');
}

echo 'BoardNotes SQLite migration contract passed.' . PHP_EOL;
