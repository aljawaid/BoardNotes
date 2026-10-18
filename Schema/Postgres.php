<?php

namespace Kanboard\Plugin\BoardNotes\Schema;

use PDO;

const VERSION = 3;


function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_cus (
        id SERIAL PRIMARY KEY,
        project_id INTEGER NOT NULL,
        project_name TEXT
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes (
        id SERIAL PRIMARY KEY,
        project_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        position INTEGER,
        is_active INTEGER,
        title TEXT,
        category TEXT,
        description TEXT,
        date_created INTEGER,
        date_modified INTEGER
    )');

    // Seed each custom project once so an interrupted installation can safely retry.
    $pdo->exec("INSERT INTO boardnotes_cus (project_id, project_name)
        SELECT 9998, 'General' WHERE NOT EXISTS (
            SELECT 1 FROM boardnotes_cus WHERE project_id = 9998
        )");
    $pdo->exec("INSERT INTO boardnotes_cus (project_id, project_name)
        SELECT 9997, 'Todo' WHERE NOT EXISTS (
            SELECT 1 FROM boardnotes_cus WHERE project_id = 9997
        )");
}

function version_2(PDO $pdo)
{
    // Repair duplicate defaults left by interrupted legacy migrations before enforcing uniqueness.
    $pdo->exec('DELETE FROM boardnotes_cus
        WHERE id NOT IN (SELECT MIN(id) FROM boardnotes_cus GROUP BY project_id)');

    // Prevent duplicate custom projects and optimise owner/project cleanup and note order.
    $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS boardnotes_cus_project_id_idx ON boardnotes_cus(project_id)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS boardnotes_owner_project_position_idx ON boardnotes(user_id, project_id, position)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS boardnotes_project_idx ON boardnotes(project_id)');
}

function version_3(PDO $pdo)
{
    // Retire legacy trigger functions; the model performs portable orphan cleanup during application startup.
    $pdo->exec('DROP TRIGGER IF EXISTS boardnotes_delete_user ON users');
    $pdo->exec('DROP TRIGGER IF EXISTS boardnotes_delete_project ON projects');
    $pdo->exec('DROP FUNCTION IF EXISTS boardnotes_delete_user_notes()');
    $pdo->exec('DROP FUNCTION IF EXISTS boardnotes_delete_project_notes()');
}
