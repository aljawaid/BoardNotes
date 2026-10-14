<?php

namespace Kanboard\Plugin\BoardNotes\Schema;

use PDO;

const VERSION = 2;


function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_cus (
        id INTEGER PRIMARY KEY,
        project_id INTEGER NOT NULL,
        project_name TEXT
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes (
        id INTEGER PRIMARY KEY,
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

    $pdo->exec("INSERT INTO boardnotes_cus (project_id, project_name) VALUES (9998, 'General')");

    $pdo->exec("INSERT INTO boardnotes_cus (project_id, project_name) VALUES (9997, 'Todo')");
}

function version_2(PDO $pdo)
{
    // Prevent duplicate custom projects and optimise the owner/project note order.
    $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS boardnotes_cus_project_id_idx ON boardnotes_cus(project_id)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS boardnotes_owner_project_position_idx ON boardnotes(user_id, project_id, position)');

    // Remove private notes when their Kanboard user or real project is deleted.
    $pdo->exec('CREATE TRIGGER IF NOT EXISTS boardnotes_delete_user AFTER DELETE ON users
        BEGIN DELETE FROM boardnotes WHERE user_id = OLD.id; END');
    $pdo->exec('CREATE TRIGGER IF NOT EXISTS boardnotes_delete_project AFTER DELETE ON projects
        BEGIN DELETE FROM boardnotes WHERE project_id = OLD.id; END');
}
