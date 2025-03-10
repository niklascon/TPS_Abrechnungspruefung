<?php
namespace src\Models\database\core;
use PDO;
use PDOException;

require_once __DIR__ . '/../../../../config/paths.php';

/**
 * This class handles the database connection. It is quite abstract and can be called from many different classes
 */
class Database {
    private static $conn = null;

    private array $whereClause = [];

    private array $joinClause = [];

    /**
     * connect to database
     */
    public function __construct() {
        if (self::$conn === null) {
            try {
                $config = require BASE_DIRECTORY. 'config/database/database.php';
                self::$conn = new PDO(
                    "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
                    $config['username'],
                    $config['password']
                );
                // Set PDO error mode to exception
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        // echo "successfully";
    }

    /**
     * create new entry in database table
     *
     * @param $table string Name of the table
     * @param $data array Associative array of column names and values
     * @return int id of created entry
     */
    private function createNewEntry(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?'); // Creates placeholders for `?`

        $sql = "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $stmt = self::$conn->prepare($sql);

        $stmt->execute(array_values($data));

        // Return the ID of the newly created entry
        return (int) self::$conn->lastInsertId();
    }

    /**
     * update existing row of table
     *
     * @param $table string Name of the table
     * @param $data array Associative array of column names and values
     * @param $id int ID of the row to update
     * @return void
     */
    private function updateExistingEntry(string $table, array $data, int $id): void
    {
        unset($data['id']); // Remove `id` from the update data

        $setClauses = array_map(function ($column) {
            return "$column = ?";
        }, array_keys($data));

        $sql = "UPDATE `$table` SET " . implode(", ", $setClauses) . " WHERE id = ?";
        $stmt = self::$conn->prepare($sql);

        $stmt->execute(array_merge(array_values($data), [$id]));
    }

    /**
     * Save a new or existing entry in the database
     *
     * @param $table string Name of the table
     * @param $data array Associative array of column names and values
     * @return int id of data
     */
    public function save(string $table, array $data): int
    {
        if (!isset($data['id']) || $data['id'] == null) {
            return $this->createNewEntry($table, $data);
        } else {
            $this->updateExistingEntry($table, $data, $data['id']);
            return $data['id'];
        }
    }

    /**
     * Load a row from the database
     *
     * @param $table string Name of the table
     * @param $id int|null ID of the row to load
     * @return array|null Associative array of the row or null if not found
     */
    public function load(string $table, int|null $id): ?array
    {
        if (is_null($id)) {
            error_log("Database Error: Tried to load data with id = null");
            return null;
        }

        $sql = "SELECT * FROM `$table` WHERE id = ?";
        $stmt = self::$conn->prepare($sql);

        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            error_log("Database Error: Entry with ID {$id} does not exist in table {$table}");
            return null;
        }

        return $row; // Return the fetched row
    }

    /**
     * adds a where clause to the sql statement
     *
     * @param $clause string that gets added
     * @return void
     */
    public function addWhereClause(string $clause): void
    {
        $this->whereClause[] = $clause;
    }

    /**
     * adds join clauses to the sql statement. All type of joins are possible
     *
     * @param $clause string that contains the join clause
     * @return void
     */
    public function addJoinClause(string $clause): void
    {
        $clause = strtolower($clause); // lower case clause to compare if it contains a join string
        if (!str_contains($clause, 'join')) {
            error_log("SQL Join Clause doesn't contain the word 'JOIN'");
        } else {
            $this->joinClause[] = $clause;
        }
    }

    /**
     * to reset where clauses
     *
     * @return void
     */
    private function resetWhereClause(): void
    {
        $this->whereClause = [];
    }

    /**
     * Load all records from the specified table
     *
     * @param string $table The name of the table from which to fetch the data
     * @return array Array of rows from the table
     */
    public function loadAll(string $table): array
    {
        $sql = "SELECT" . " $table." ."*" . " FROM `$table`";

        // add join clauses to sql query
        $joinClauseAdded = False;
        if (!empty($this->joinClause)) {
            $sql .= ' ' . implode(' ', $this->joinClause);
            $joinClauseAdded = True;
        }

        // check if where clauses exist
        if (!empty($this->whereClause)) {
            // go through each where clause and check if id is linked to a table name
            if ($joinClauseAdded) {
                foreach ($this->whereClause as $whereClause) {
                    if (str_contains($whereClause, "id")) {
                        error_log("Warning: Unspecified id found in sql query");
                    }
                }
            }
            // add where Clauses to sql query
            $sql .= " WHERE " . implode(" AND ", $this->whereClause);
        }

        try {
            $stmt = self::$conn->query($sql);
        } catch (PDOException $e) {
            throw new PDOException("Database Error!");
        }


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a row from the database
     *
     * @param string $table Name of the table
     * @param int $id ID of the row to delete
     * @return bool True if the deletion was successful, false otherwise
     */
    public function delete(string $table, int $id): bool
    {
        $sql = "DELETE FROM `$table` WHERE id = ?";
        $stmt = self::$conn->prepare($sql);

        return $stmt->execute([$id]);
    }

    /**
     * close database connection
     */
    public function __destruct() {
        // DON'T ADD THIS TO THE CODE. OTHERWISE IT WON'T WORK ANYMORE
        /*if (self::$conn !== null) {
            self::$conn = null; // Reset connection for PDO
        }*/
    }
}
