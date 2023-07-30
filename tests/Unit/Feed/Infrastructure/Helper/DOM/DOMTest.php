<?php

namespace Unit\Feed\Infrastructure\Helper\DOM;

use App\Feed\Infrastructure\Helper\DOM\DOM;
use LogicException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DOMTest extends TestCase
{
    private \DOMElement $DOMElement;

    protected function setUp(): void
    {
        $DOMDocument = new \DOMDocument();
        $DOMDocument->loadXML(<<<XML
<?xml version="1.0"?>
<entry>
    <test-link href="https://example.com"/>
    <test-date-time>2023-07-05T19:00:15+00:00</test-date-time>
    <test-string>test content</test-string>
</entry>
XML);

        $DOMNodeList = $DOMDocument->getElementsByTagName('entry');

        self::assertInstanceOf(\DOMElement::class, $DOMNodeList[0]);

        $this->DOMElement = $DOMNodeList[0];
    }

    /**
     * @test
     */
    public function it_should_return_a_string(): void
    {
        // Act
        $string = DOM::getString($this->DOMElement, 'test-string');

        // Assert
        self::assertSame('test content', $string);
    }

    /**
     * @test
     */
    public function it_should_return_a_link(): void
    {
        // Act
        $link = DOM::getLink($this->DOMElement, 'test-link');

        // Assert
        self::assertEquals('https://example.com', $link);
    }

    /**
     * @test
     */
    public function it_should_throw_if_link_cant_be_retrieved_from_element(): void
    {
        // Assert
        self::expectException(OutOfBoundsException::class);

        // Act
        $link = DOM::getLink($this->DOMElement, 'test-string');
    }

    /**
     * @test
     */
    public function it_should_return_a_date_time_object(): void
    {
        // Act
        $dateTime = DOM::getDateTime($this->DOMElement, 'test-date-time');

        // Assert
        self::assertInstanceOf(\DateTime::class, $dateTime);
    }

    /**
     * @test
     */
    public function it_should_throw_if_item_cant_be_casted_to_datetime(): void
    {
        // Assert
        self::expectException(LogicException::class);

        // Act
        $dateTime = DOM::getDateTime($this->DOMElement, 'test-string');
    }

    /**
     * @test
     */
    public function it_should_throw_if_key_does_not_exist_while_retrieving_a_string(): void
    {
        // Assert
        self::expectException(OutOfBoundsException::class);

        // Act
        DOM::getString($this->DOMElement, 'non-existing');
    }

    /**
     * @test
     */
    public function it_should_throw_if_key_does_not_exist_while_retrieving_a_link(): void
    {
        // Assert
        self::expectException(OutOfBoundsException::class);

        // Act
        DOM::getLink($this->DOMElement, 'non-existing');
    }

    /**
     * @test
     */
    public function it_should_throw_if_key_does_not_exist_while_retrieving_a_datetime_object(): void
    {
        // Assert
        self::expectException(OutOfBoundsException::class);

        // Act
        DOM::getDateTime($this->DOMElement, 'non-existing');
    }
}
