<?php

namespace Unit\Controllers;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use src\Controllers\BillOverview;
use src\Models\database\implementation\Bill;

class BillOverviewTest extends TestCase
{

    private $billOverview;

    protected function setUp(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        require_once __DIR__ . "/../../../config/paths.php";

        $this->billOverview = new BillOverview();
    }

    public function testPostFormDeletesRows(): void
    {
        // Mock for the first element in the bill list
        $bill1 = $this->createMock(Bill::class);
        $bill1->expects($this->never())
            ->method('delete'); // Expects that delete() is never called

        // Mock for the second element in the bill list
        $bill2 = $this->createMock(Bill::class);
        $bill2->expects($this->once())
            ->method('delete'); // Expects that delete() is called exactly once

        // Mock for the third element in the bill list
        $bill3 = $this->createMock(Bill::class);
        $bill3->expects($this->once())
            ->method('delete'); // Expects that delete() is called exactly once

        // Initial bill list
        $billList = [$bill1, $bill2, $bill3];

        // Simulate POST data: Only `row-0` exists
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'row-0' => 'on', // Row 0 exists
        ];

        // Test the function
        $reflectionClass = new ReflectionClass(BillOverview::class);
        $method = $reflectionClass->getMethod('postForm');
        $method->setAccessible(true);
        $updatedBillList = $method->invoke($this->billOverview, $billList);

        // Assertions
        $this->assertCount(1, $updatedBillList, 'Only one element should remain.');
        $this->assertSame($bill1, $updatedBillList[0], 'The remaining element should be the first one in the list.');
    }
}