<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;

// require_once '../../src/Models/Database.php';

class DatabaseTest extends TestCase
{
    protected Database $db;

    protected function setUp(): void {
        $this->db = new Database();
    }

    /**
     * check if it's possible to connect to the database
     *
     * @return void
     */
    public function testConnection(): void {
        $this->assertNotNull($this->db);
    }

    /**
     * save new entry in database or update it. delete it afterwards
     *
     * @return void
     */
    public function testSaveNewRecord(): void {
        $data = [
            'username' => 'MarkWeber'
        ];

        // Save new record
        $id = $this->db->save('user', $data);

        // Check if the record exists
        $this->assertEntryExists($data, $id);

        // Delete the record
        $this->deleteEntry($id);

        // Verify the record no longer exists
        error_log("Deliberate Database Error for testing follows:");
        $this->noLongerExists($id);
    }

    private function assertEntryExists($data, $id): void {
        // Check if the record exists
        $savedData = $this->db->load('user', $id);
        $this->assertNotNull($savedData, 'Record should exist after saving.');
        $this->assertEquals($data['name'], $savedData['name']);
    }

    private function deleteEntry($id): void {
        $deleteResult = $this->db->delete('user', $id);
        $this->assertTrue($deleteResult, 'Record should be deleted successfully.');
    }

    private function noLongerExists($id): void {
        $deletedData = $this->db->load('user', $id);
        $this->assertNull($deletedData, 'Record should not exist after deletion');
    }

    /**
     * Test addWhereClause method by applying clauses and verifying the result
     *
     * @return void
     */
    public function testAddWhereClause(): void {
        // Add where clauses
        $this->db->addWhereClause("price > 150");

        // Load all data with clauses
        $data = $this->db->loadAll('line_item');

        // Assertions (example: assuming `line_item` table exists and query is valid)
        $this->assertIsArray($data, 'Result should be an array of rows.');
        foreach ($data as $row) {
            $this->assertGreaterThan(150, $row['price'], 'Each line_item should have a price > 150.');
        }
    }

    /**
     * Test addJoinClause method by applying a join and verifying the result
     *
     * @return void
     */
    public function testAddJoinClause(): void {
        // Add join clause
        $this->db->addJoinClause("INNER JOIN line_item ON line_item.fk_bill = bill.id");

        // Load all data with join
        $data = $this->db->loadAll('bill');

        // Assertions (example: assuming `bill` table exists and query is valid)
        $this->assertIsArray($data, 'Result should be an array of rows.');

        foreach ($data as $row) {
            $this->assertArrayHasKey('id', $row, 'Joined data should exist.');
        }
    }

    /**
     * Test addJoinClause method by feeding it with invalid entries
     *
     * @return void
     */
    public function testAddJoinClauseWithInvalidString(): void
    {
        $this->db->addJoinClause("invalid_clause");

        // Load data to see if it works without adding the invalid join clause
        $data = $this->db->loadAll("bill");

        $this->assertIsArray($data, 'Database should function even when an invalid join clause is added.');

        // if we try to add joins to a non existing class or where there is no connection the database should throw a generic exception
        $this->expectExceptionMessage("Database Error!");
        $this->db->addJoinClause("JOIN notindg on quatsch = mist");
        $data = $this->db->loadAll("bill");
    }

    /**
     * Test combined use of addWhereClause and addJoinClause
     *
     * @return void
     */
    public function testAddWhereAndJoinClauses(): void {
        // Add join and where clauses
        $this->db->addJoinClause("INNER JOIN line_item ON line_item.fk_bill = bill.id");
        $this->db->addWhereClause("sum > 903");

        // Load data with combined clauses
        $data = $this->db->loadAll('bill');

        // Assertions (example: assuming `orders` table exists and query is valid)
        $this->assertIsArray($data, 'Result should be an array of rows.');
        foreach ($data as $row) {
            $this->assertGreaterThan(903, $row['sum'], 'Sum should be greater than 903.');
        }
    }
}
