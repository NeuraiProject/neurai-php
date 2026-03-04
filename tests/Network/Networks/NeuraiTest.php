<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Tests\Network\Networks;

use BitWasp\Bitcoin\Network\Networks\Neurai;
use BitWasp\Bitcoin\Tests\AbstractTestCase;

class NeuraiTest extends AbstractTestCase
{
    public function testNeuraiNetwork()
    {
        $network = new Neurai();
        $this->assertEquals('35', $network->getAddressByte());
        $this->assertEquals('75', $network->getP2shByte());
        $this->assertEquals('80', $network->getPrivByte());
        $this->assertEquals('0488ade4', $network->getHDPrivByte());
        $this->assertEquals('0488b21e', $network->getHDPubByte());
        $this->assertEquals('5255454e', $network->getNetMagicBytes());
        $this->assertEquals("Neurai Signed Message", $network->getSignedMessageMagic());
    }
}
