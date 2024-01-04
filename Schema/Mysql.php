<?php

namespace Kanboard\Plugin\BoardNotes\Schema;

use PDO;

const VERSION = 1;


function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes_cus (
        `id` INT NOT NULL AUTO_INCREMENT,
        `project_id` INTEGER NOT NULL,
        `project_name` TEXT,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS boardnotes (
        `id` INT NOT NULL AUTO_INCREMENT,
        `project_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `position` INT,
        `is_active` INT,
        `title` TEXT,
        `category` TEXT,
        `description` TEXT,
        `date_created` INT,
        `date_modified` INT,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');

    $pdo->exec('INSERT INTO `boardnotes_cus`(`project_id`, `project_name`) VALUES (9998, "General")');

    $pdo->exec('INSERT INTO `boardnotes_cus`(`project_id`, `project_name`) VALUES (9997, "Todo")');
}
