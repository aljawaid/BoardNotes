<?php

namespace Kanboard\Plugin\BoardNotes\Schema;

//use Kanboard\Core\Security\Token;
//use Kanboard\Core\Security\Role;
use PDO;

const VERSION = 1;


function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_cus (
        id INTEGER AUTO_INCREMENT,
        project_id INTEGER NOT NULL,
        project_name TEXT,
        PRIMARY KEY(id)
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes (
        id INTEGER AUTO_INCREMENT,
        project_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        position INTEGER,
        is_active INTEGER,
        title TEXT,
        category TEXT,
        description TEXT,
        date_created INTEGER,
        date_modified INTEGER,
        PRIMARY KEY(id)
    )');

    $pdo->exec('INSERT INTO boardnotes_cus (project_id, project_name) VALUES (9998, "General")');

    $pdo->exec('INSERT INTO boardnotes_cus (project_id, project_name) VALUES (9997, "Todo")');
}
