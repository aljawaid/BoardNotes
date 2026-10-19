<?php

/**
 * Provide the Kanboard project-user table name required by the database contract.
 *
 * Related file: Model/BoardNotesModel.php.
 * Related test: tests/DatabaseMigrationTest.php.
 */

namespace Kanboard\Model;

class ProjectUserRoleModel
{
    public const TABLE = 'project_has_users';
}
