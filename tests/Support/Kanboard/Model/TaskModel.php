<?php

/**
 * Provide the Kanboard task table name required by the database contract.
 *
 * Related file: Model/BoardNotesModel.php.
 * Related test: tests/DatabaseMigrationTest.php.
 */

namespace Kanboard\Model;

class TaskModel
{
    public const TABLE = 'tasks';
}
