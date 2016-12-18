<?php

namespace Test;

/**
 * Class UnitTest
 */
class Ipv4UnitTest extends \UnitTestCase
{
    public function testTestCase()
    {
        $model = \Ipv4::findByIp('128.179.255.205');

        $this->assertNotNull($model);

        $this->assertEquals(
            $model->city_name,
            'Fribourg'
        );
    }
}