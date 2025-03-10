<?php

namespace Unit\Controllers;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\BookingType;
use src\Models\database\implementation\LineItem;
use src\Models\pdf_parsing\PDFParser;

class PDFParserTest extends TestCase
{
    private PDFParser $pdfParser;
    private ReflectionClass $reflectionClass;

    protected function setUp(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        require_once __DIR__ . "/../../../config/paths.php";

        $this->pdfParser = new PDFParser();
        $this->reflectionClass = new ReflectionClass(PDFParser::class);
    }

    public function testReadBillDate(): void
    {
        // set up function
        $method = $this->reflectionClass->getMethod('readBillDate');
        $method->setAccessible(true);

        // check when input is correct
        $columns = ["Abrechnung", "2020"];
        $bill = new Bill();
        $loadedBool = $method->invoke($this->pdfParser, $bill, $columns);
        $this->assertTrue($loadedBool);
        $this->assertEquals("2020", $bill->getYear());

        // check when input is wrong
        $columns = ["nothing in here", "2020"];
        $loadedBool = $method->invoke($this->pdfParser, new Bill(), $columns);
        $this->assertFalse($loadedBool);

        // check when $columns is to small
        $columns = ["noth enough array inputs"];
        $loadedBool = $method->invoke($this->pdfParser, new Bill(), $columns);
        $this->assertFalse($loadedBool);
    }

    public function testReadLastMoneyValue(): void
    {
        // set up function
        $method = $this->reflectionClass->getMethod('readLastMoneyValue');
        $method->setAccessible(true);

        // check when input is correct
        $columns = ["Betriebskosten", "umlagefähig", "948,71", "€"];
        $returnPrice = $method->invoke($this->pdfParser, $columns);
        $this->assertEquals("948.71", $returnPrice);

        // check when money is splitted
        $columns = ["Betriebskosten", "umlagefähig", "948,7", "1", "€"];
        $returnPrice = $method->invoke($this->pdfParser, $columns);
        $this->assertEquals("948.71", $returnPrice);

        // check when no money is given
        $columns = ["Betriebskosten", "umlagefähig", "€"];
        $returnPrice = $method->invoke($this->pdfParser, $columns);
        $this->assertEquals("0.00", $returnPrice);
    }

    public function testConvertToFloat(): void
    {
        // set up function
        $method = $this->reflectionClass->getMethod('convertToFloat');
        $method->setAccessible(true);

        // Test for standard input with a comma as decimal separator
        $result = $method->invoke($this->pdfParser, '123,45');
        $this->assertEquals(123.45, $result);

        // Test for input with dot as decimal separator
        $result = $method->invoke($this->pdfParser, '123.45');
        $this->assertEquals(12345, $result);

        // Test for input with thousands separator (dot)
        $result = $method->invoke($this->pdfParser, '1.234,56');
        $this->assertEquals(1234.56, $result);

        // Test for input with multiple thousands separators (invalid format)
        $result = $method->invoke($this->pdfParser, '1.2.3.4,56');
        $this->assertEquals(1234.56, $result);

        // Test for empty input
        $result = $method->invoke($this->pdfParser, '');
        $this->assertEquals(0.0, $result);

        // Test for non-numeric input
        $result = $method->invoke($this->pdfParser, 'abc');
        $this->assertEquals(0.0, $result);
    }

    public function testReadLineItem(): void
    {
        // set up function
        $method = $this->reflectionClass->getMethod('readLineItem');
        $method->setAccessible(true);

        // Mock BookingType with a short name
        $bookingType = $this->createMock(BookingType::class);
        $bookingType->method('getShortName')->willReturn('BK001');

        // Test when the line contains the bookingType
        $columns = ['BK001', 'Description', '948,71', '€'];
        $lineItem = $method->invoke($this->pdfParser, $columns, $bookingType);

        $this->assertNotNull($lineItem);
        $this->assertInstanceOf(LineItem::class, $lineItem);
        $this->assertEquals('BK001', $lineItem->getDescription());
        $this->assertEquals(948.71, $lineItem->getPrice());
        $this->assertEquals($bookingType, $lineItem->getBookingType());

        // Test when the line does not start with the bookingType short name
        $columns = ['WRONG', 'Description', '948,71', '€'];
        $lineItem = $method->invoke($this->pdfParser, $columns, $bookingType);
        $this->assertNull($lineItem);

        // Test when the line has fewer than 3 columns
        $columns = ['BK001', 'Description'];
        $lineItem = $method->invoke($this->pdfParser, $columns, $bookingType);
        $this->assertNull($lineItem);

        // Test with empty columns
        $columns = [];
        $lineItem = $method->invoke($this->pdfParser, $columns, $bookingType);
        $this->assertNull($lineItem);

        // Test with invalid price format
        $columns = ['BK001', 'Description', 'invalid_price', '€'];
        $lineItem = $method->invoke($this->pdfParser, $columns, $bookingType);
        $this->assertNotNull($lineItem);
        $this->assertEquals(0.0, $lineItem->getPrice());

        // Test with split price values
        $columns = ['BK001', 'Description', '948,7', '1', '€'];
        $lineItem = $method->invoke($this->pdfParser, $columns, $bookingType);
        $this->assertNotNull($lineItem);
        $this->assertEquals(948.71, $lineItem->getPrice());
    }

    public function testParseWithValidFile(): void
    {
        // Mock Page
        $pageMock = $this->createMock(Page::class);
        $pageMock->method('getText')->willReturn(
            "Abrechnung 2020\nHausreinigung Umlage 948,71 €\nGesamtkosten Heizanlage\n"
        );

        // Mock Document
        $pdfMock = $this->createMock(Document::class);
        $pdfMock->method('getPages')->willReturn([$pageMock]);

        // Mock Parser
        $parserMock = $this->createMock(Parser::class);
        $parserMock->method('parseFile')->willReturn($pdfMock);

        $pdfParser = new PDFParser($parserMock);

        // Execute parse method
        $result = $pdfParser->parse('dummy.pdf');

        $bill = $result['bill'];
        $this->assertInstanceOf(Bill::class, $bill);
        $this->assertEquals('2020', $bill->getYear());

        $lineItems = $result['lineItems'];
        $this->assertCount(1, $lineItems);
        $this->assertInstanceOf(LineItem::class, $lineItems[0]);
        $this->assertEquals('Hausreinigung', $lineItems[0]->getDescription());
        $this->assertEquals(948.71, $lineItems[0]->getPrice());
    }

    public function testParseWithEmptyFile(): void
    {
        // Mock Parser
        $parserMock = $this->createMock(Parser::class);

        // Mock empty pdf without pages
        $pdfMock = $this->createMock(Document::class);
        $pdfMock->method('getPages')->willReturn([]);

        $parserMock->method('parseFile')->willReturn($pdfMock);

        $pdfParser = new PDFParser($parserMock);

        // run test
        $result = $pdfParser->parse('empty.pdf');

        // check that nothing has found
        $this->assertEmpty($result['lineItems']);
    }

    public function testParseWithNoMatchingBookingTypes(): void
    {
        // Mock Page without matching booking type text
        $pageMock = $this->createMock(Page::class);
        $pageMock->method('getText')->willReturn("Abrechnung 2020\nNo matching booking type here\n");

        // Mock Document
        $pdfMock = $this->createMock(Document::class);
        $pdfMock->method('getPages')->willReturn([$pageMock]);

        // Mock Parser
        $parserMock = $this->createMock(Parser::class);
        $parserMock->method('parseFile')->willReturn($pdfMock);

        $pdfParser = new PDFParser($parserMock);

        // run test
        $result = $pdfParser->parse('dummy.pdf');

        // check that nothing has been found
        $this->assertEmpty($result['lineItems']);
    }

    public function testParseWithMissingBillDate(): void
    {
        // Mock Page without "Abrechnung" line
        $pageMock = $this->createMock(Page::class);
        $pageMock->method('getText')->willReturn("No billing year here\nHausreinigung Umlage 948,71 €\n");

        // Mock Document
        $pdfMock = $this->createMock(Document::class);
        $pdfMock->method('getPages')->willReturn([$pageMock]);

        // Mock Parser
        $parserMock = $this->createMock(Parser::class);
        $parserMock->method('parseFile')->willReturn($pdfMock);

        $pdfParser = new PDFParser($parserMock);

        // run test
        $result = $pdfParser->parse('dummy.pdf');

        // check that bill date has not been set
        $bill = $result['bill'];
        $this->assertEquals(0, $bill->getYear());

        // there still shouldn't be any line items
        $lineItems = $result['lineItems'];
        $this->assertCount(1, $lineItems);
    }

    public function testParseWithMultipleLineItems(): void
    {
        // Mock Page with multiple booking types
        $pageMock = $this->createMock(Page::class);
        $pageMock->method('getText')->willReturn(
            "Abrechnung 2020\nHausreinigung Umlage 948,71 €\nGartenpflege 399,50 €\nGesamtkosten Heizanlage"
        );

        // Mock Document
        $pdfMock = $this->createMock(Document::class);
        $pdfMock->method('getPages')->willReturn([$pageMock]);

        // Mock Parser
        $parserMock = $this->createMock(Parser::class);
        $parserMock->method('parseFile')->willReturn($pdfMock);

        $pdfParser = new PDFParser($parserMock);

        // run test
        $result = $pdfParser->parse('dummy.pdf');

        // check that two line items have been found
        $lineItems = $result['lineItems'];
        $this->assertCount(2, $lineItems);

        // check description and price of booking types
        $this->assertEquals('Hausreinigung', $lineItems[0]->getDescription());
        $this->assertEquals(948.71, $lineItems[0]->getPrice());

        $this->assertEquals('Gartenpflege', $lineItems[1]->getDescription());
        $this->assertEquals(399.50, $lineItems[1]->getPrice());
    }
}