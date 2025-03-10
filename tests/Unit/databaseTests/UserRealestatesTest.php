<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\User;
use src\Models\database\implementation\UserRealestate;
use src\Models\database\implementation\UserRealestates;

class UserRealestatesTest extends TestCase
{
    private $user1;
    private $user2;
    private $realEstate1;
    private $realEstate2;
    private $userRealestate1;
    private $userRealestate2;

    protected function setUp(): void
    {
        $this->user1 = new User();
        $this->user1->setUsername("Markl");
        $this->user1->save();

        $this->user2 = new User();
        $this->user2->setUsername("Nobody");
        $this->user2->save();

        $this->realEstate1 = new RealEstate();
        $this->realEstate1->setName("Real Estate");
        $this->realEstate1->save();

        $this->realEstate2 = new RealEstate();
        $this->realEstate2->setName("Real Estate");
        $this->realEstate2->save();

        $this->userRealestate1 = new UserRealestate();
        $this->userRealestate1->setUser($this->user1);
        $this->userRealestate1->setRealEstate($this->realEstate1);
        $this->userRealestate1->save();

        $this->userRealestate2 = new UserRealestate();
        $this->userRealestate2->setUser($this->user2);
        $this->userRealestate2->setRealEstate($this->realEstate2);
        $this->userRealestate2->save();
    }

    public function testAddWhereUser(): void
    {
        $userRealestates = new UserRealestates();
        $userRealestates->addWhereUser($this->user1);

        $userExists = null;
        $userNotExists = null;
        foreach ($userRealestates->loadAll() as $userRealestate) {
            if ($userRealestate->getUser()->getId() == $this->user1->getId()) {
                $userExists = $userRealestate->getUser();
            }
            if ($userRealestate->getUser()->getId() == $this->user2->getId()) {
                $userNotExists = $userRealestate->getUser();
            }
        }
        $this->assertEquals($this->user1->getId(), $userExists->getId());
        $this->assertNull($userNotExists);
    }

    public function testAddWhereRealestate(): void
    {
        $userRealestates = new UserRealestates();
        $userRealestates->addWhereRealestate($this->realEstate1);

        $realEstateExists = null;
        $realEstateNotExists = null;
        foreach ($userRealestates->loadAll() as $userRealestate) {
            if ($userRealestate->getRealestate()->getId() == $this->realEstate1->getId()) {
                $realEstateExists = $userRealestate->getRealestate();
            }
            if ($userRealestate->getRealestate()->getId() == $this->realEstate2->getId()) {
                $realEstateNotExists = $userRealestate->getRealestate();
            }
        }
        $this->assertEquals($this->realEstate1->getId(), $realEstateExists->getId());
        $this->assertNull($realEstateNotExists);
    }

    public function testLoadAll(): void
    {
        $userRealestates = new UserRealestates();
        $allUserRealestates = $userRealestates->loadAll();

        $this->assertIsArray($allUserRealestates);
        $this->assertGreaterThan(1, count($allUserRealestates));

        // check if list is an object of UserRealestates
        foreach ($allUserRealestates as $userRealestate) {
            $this->assertInstanceOf(UserRealestate::class, $userRealestate);
        }
    }


    protected function tearDown(): void
    {
        $db = new Database();
        $db->delete("user", $this->user1->getId());
        $db->delete("user", $this->user2->getId());
        $db->delete("real_estate", $this->realEstate1->getId());
        $db->delete("real_estate", $this->realEstate2->getId());
        $db->delete("user_real_estate", $this->userRealestate1->getId());
        $db->delete("user_real_estate", $this->userRealestate2->getId());
    }
}
