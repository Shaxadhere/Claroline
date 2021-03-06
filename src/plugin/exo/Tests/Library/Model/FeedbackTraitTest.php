<?php

namespace UJM\ExoBundle\Tests\Library\Model;

use PHPUnit\Framework\TestCase;
use UJM\ExoBundle\Library\Model\FeedbackTrait;

class FeedbackTraitTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mock;

    protected function setUp(): void
    {
        parent::setUp();

        // Creates a mock using the trait
        $this->mock = $this->getMockForTrait(FeedbackTrait::class);
    }

    /**
     * The trait MUST adds a `feedback` with its getter and setter in the class using it.
     */
    public function testInjectFeedback()
    {
        // Test getter
        $this->assertTrue(method_exists($this->mock, 'getFeedback'));
        // Test setter
        $this->assertTrue(method_exists($this->mock, 'setFeedback'));
    }
}
