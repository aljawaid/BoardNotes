<?php

namespace Kanboard\Plugin\Boardnotes\Schema;

use Kanboard\Core\Security\Token;
use Kanboard\Core\Security\Role;
use PDO;

const VERSION = 7;

function version_7(PDO $pdo)
{
    $pdo->exec("ALTER TABLE boardnotes ADD COLUMN category TEXT");
}


function version_6($pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes (
        "id" INTEGER PRIMARY KEY,
        "project_id" INTEGER NOT NULL,
        "user_id" INTEGER NOT NULL,
        "position" INTEGER,
        "is_active" INTEGER,
        "title" TEXT,
        "description" TEXT,
        "date_created" INTEGER,
        "date_modified" INTEGER,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
    )');
}

