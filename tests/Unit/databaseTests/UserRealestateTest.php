<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\implementation\Realestate;
use src\Models\database\implementation\User;
use src\Models\database\implementation\UserRealestate;


class UserRealestateTest extends TestCase
{
    private $userRealestate;

    protected function setUp(): void
    {
        $this->userRealestate = new UserRealestate();
    }

    public function testGetUser(): void
    {
        $this->userRealestate->setUser(new User(1));
        $this->assertEquals(1, $this->userRealestate->getUser()->getId());
    }

    public function testGetRealestate(): void
    {
        $this->userRealestate->setRealestate(new Realestate(1));
        $this->assertEquals(1, $this->userRealestate->getRealestate()->getId());
    }
}
