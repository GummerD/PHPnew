<?

use PHPUnit\Framework\TestCase;

/**
 * Summary of HelloTest
 */
class HelloTest extends TestCase
{
    public function testItWorks(): void
    {
        $this->assertTrue(true); // утвержадть истину. Assert - утвержать.
    }

    public function testEquals(): void
    {
        $this->assertEquals(4, 2+2, 'Равеноство неверное');// утверждать равенство
    }
}