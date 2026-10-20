<?php

namespace Kanboard\Plugin\BoardNotes\Schema;

use PDO;

const VERSION = 3;


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

    // Seed each custom project once so an interrupted installation can safely retry.
    $pdo->exec("INSERT INTO `boardnotes_cus` (`project_id`, `project_name`)
        SELECT 9998, 'General' WHERE NOT EXISTS (
            SELECT 1 FROM `boardnotes_cus` WHERE `project_id` = 9998
        )");
    $pdo->exec("INSERT INTO `boardnotes_cus` (`project_id`, `project_name`)
        SELECT 9997, 'Todo' WHERE NOT EXISTS (
            SELECT 1 FROM `boardnotes_cus` WHERE `project_id` = 9997
        )");
}

function version_2(PDO $pdo)
{
    // Repair duplicate defaults left by interrupted legacy migrations before enforcing uniqueness.
    $pdo->exec('DELETE duplicate_row FROM `boardnotes_cus` AS duplicate_row
        INNER JOIN `boardnotes_cus` AS keeper
            ON duplicate_row.`project_id` = keeper.`project_id`
            AND duplicate_row.`id` > keeper.`id`');

    // Create each index only once because MySQL DDL cannot be rolled back after a later failure.
    if (! mysqlIndexExists($pdo, 'boardnotes_cus', 'boardnotes_cus_project_id_idx')) {
        $pdo->exec('ALTER TABLE `boardnotes_cus`
            ADD UNIQUE INDEX `boardnotes_cus_project_id_idx` (`project_id`)');
    }
    if (! mysqlIndexExists($pdo, 'boardnotes', 'boardnotes_owner_project_position_idx')) {
        $pdo->exec('ALTER TABLE `boardnotes`
            ADD INDEX `boardnotes_owner_project_position_idx` (`user_id`, `project_id`, `position`)');
    }
    if (! mysqlIndexExists($pdo, 'boardnotes', 'boardnotes_project_idx')) {
        $pdo->exec('ALTER TABLE `boardnotes` ADD INDEX `boardnotes_project_idx` (`project_id`)');
    }
}

function version_3(PDO $pdo)
{
    // Retire privileged triggers; the model performs portable orphan cleanup during application startup.
    $pdo->exec('DROP TRIGGER IF EXISTS `boardnotes_delete_user`');
    $pdo->exec('DROP TRIGGER IF EXISTS `boardnotes_delete_project`');
}

function mysqlIndexExists(PDO $pdo, $table, $index)
{
    $statement = $pdo->prepare('SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1');
    $statement->execute(array($table, $index));

    return $statement->fetchColumn() !== false;
}
