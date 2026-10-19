<?php

/**
 * Adapt PDO to the minimal database API required by the database contract.
 *
 * Related file: Model/BoardNotesModel.php.
 * Related test: tests/DatabaseMigrationTest.php.
 */

namespace Kanboard\Plugin\BoardNotes\Tests\Support;

use PDO;

class TestDatabase
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function execute($sql, array $values = array())
    {
        $statement = $this->pdo->prepare($sql);

        return $statement->execute($values) ? $statement : false;
    }
}
