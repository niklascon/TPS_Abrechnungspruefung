<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\User;
use src\Models\database\implementation\Users;

// require_once '../../src/Models/User.php';
// require_once '../../src/Models/Users.php';

class UsersTest extends TestCase
{
    private $users;

    protected function setUp(): void
    {
        $this->users = new Users();
    }

    public function testLoadAllUsers(): void
    {
        $users = $this->users->loadAll();

        $this->assertIsArray($users);
        // $this->assertGreaterThan(0, count($users));

        // check if list is an object of User
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    public function testDeleteUserName(): void
    {
        $user = new User();
        $user->setUsername("user1");
        $id = $user->save();

        $db = new Database();

        $this->users->deleteUserName("user1");

        $this->assertNull($db->load("user", $id));
    }

    public function testLoadAllByName(): void
    {
        $this->users->deleteUserName("user1");

        $this->users->addWhereUserName('user1');
        $usersArray = $this->users->loadAll();
        $this->assertEmpty($usersArray);

        $user = new User();
        $user->setUsername("user1");
        $id = $user->save();

        $this->users->addWhereUserName('user1');
        $loadedUsers = $this->users->loadAll();
        $this->assertEquals(1, count($loadedUsers));
        $this->assertEquals($id, $loadedUsers[0]->getId());

        $this->users->deleteUserName("user1");
    }
}
