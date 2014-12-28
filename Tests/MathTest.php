<?php

namespace Innmind\ProvisionerBundle\Tests;

use Innmind\ProvisionerBundle\Math;

class MathTest extends \PHPUnit_Framework_TestCase
{
    public function testLinearRegression()
    {
        $this->assertEquals(
            ['slope' => 0.5, 'intercept' => 0],
            Math::linearRegression([0, 1, 0, 2])
        );
    }
}
