<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\User;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\UserRealestate;

class UserTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testGetName(): void
    {
        $this->user->setUsername("Mark Weber");
        $this->assertEquals("Mark Weber", $this->user->getUsername());
    }

    public function testSaveNewUser(): void
    {
        // create user
        $this->user->setUsername("MarkWeber");
        $id = $this->user->save();

        // load user from database to compare it
        $db = new Database();
        $data = $db->load("user", $id);

        $this->assertEquals("MarkWeber", $data['username']);

        // delete entry from database again
        $db->delete("user", $id);
    }

    public function testGetRealEstatesOfUser(): void
    {
        $realEstate1 = new RealEstate();
        $realEstate1->setName("Real Estate 1");
        $realEstate1->save();
        $realEstate2 = new RealEstate();
        $realEstate2->setName("Real Estate 2");
        $realEstate2->save();
        $realEstate3 = new RealEstate();
        $realEstate3->setName("Real Estate 3");
        $realEstate3->save();

        $user1 = new User();
        $user1->setUsername("MarkWeber");
        $user1->save();
        $user2 = new User();
        $user2->setUsername("MarkWeber2");
        $user2->save();

        $userRealestate1 = new UserRealEstate();
        $userRealestate1->setRealEstate($realEstate1);
        $userRealestate1->setUser($user1);
        $userRealestate1->save();
        $userRealestate2 = new UserRealEstate();
        $userRealestate2->setRealEstate($realEstate2);
        $userRealestate2->setUser($user1);
        $userRealestate2->save();
        $userRealestate3 = new UserRealEstate();
        $userRealestate3->setRealEstate($realEstate3);
        $userRealestate3->setUser($user2);
        $userRealestate3->save();

        $this->assertCount(2, $user1->getRealEstatesOfUser());
        $this->assertCount(1, $user2->getRealEstatesOfUser());


        $db = new Database();
        $db->delete("real_estate", $realEstate1->getId());
        $db->delete("real_estate", $realEstate2->getId());
        $db->delete("real_estate", $realEstate3->getId());
        $db->delete("user", $user1->getId());
        $db->delete("user", $user2->getId());
        $db->delete("user_real_estate", $userRealestate1->getId());
        $db->delete("user_real_estate", $userRealestate2->getId());
        $db->delete("user_real_estate", $userRealestate3->getId());
    }

    public function testDelete(): void{
        // add new User and delete it again, then assert it is not in the database
        $username = "TestUsername";
        $this->user->setUsername($username);
        $id = $this->user->save();
        $this->user->delete();
        $db = new Database();
        $userList = $db->loadAll("bill");
        $this->assertNull($userList[$id], "User should be deleted, still exists");

        // there should not be an exception if one tries to delete a not existing user
        $this->user->delete();
    }
}
