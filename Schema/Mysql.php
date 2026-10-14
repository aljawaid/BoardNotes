<?php

namespace Kanboard\Plugin\BoardNotes\Schema;

use PDO;

const VERSION = 2;


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

    $pdo->exec("INSERT INTO `boardnotes_cus` (`project_id`, `project_name`) VALUES (9998, 'General')");

    $pdo->exec("INSERT INTO `boardnotes_cus` (`project_id`, `project_name`) VALUES (9997, 'Todo')");
}

function version_2(PDO $pdo)
{
    // Prevent duplicate custom projects and optimise the owner/project note order.
    $pdo->exec('ALTER TABLE `boardnotes_cus` ADD UNIQUE INDEX `boardnotes_cus_project_id_idx` (`project_id`)');
    $pdo->exec('ALTER TABLE `boardnotes` ADD INDEX `boardnotes_owner_project_position_idx` (`user_id`, `project_id`, `position`)');

    // Remove private notes when their Kanboard user or real project is deleted.
    $pdo->exec('DROP TRIGGER IF EXISTS `boardnotes_delete_user`');
    $pdo->exec('CREATE TRIGGER `boardnotes_delete_user` AFTER DELETE ON `users`
        FOR EACH ROW DELETE FROM `boardnotes` WHERE `user_id` = OLD.`id`');
    $pdo->exec('DROP TRIGGER IF EXISTS `boardnotes_delete_project`');
    $pdo->exec('CREATE TRIGGER `boardnotes_delete_project` AFTER DELETE ON `projects`
        FOR EACH ROW DELETE FROM `boardnotes` WHERE `project_id` = OLD.`id`');
}
