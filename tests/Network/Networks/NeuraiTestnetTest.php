<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Tests\Network\Networks;

use BitWasp\Bitcoin\Network\Networks\NeuraiTestnet;
use BitWasp\Bitcoin\Tests\AbstractTestCase;

class NeuraiTestnetTest extends AbstractTestCase
{
    public function testNeuraiTestnetNetwork()
    {
        $network = new NeuraiTestnet();
        $this->assertEquals('7f', $network->getAddressByte());
        $this->assertEquals('c4', $network->getP2shByte());
        $this->assertEquals('ef', $network->getPrivByte());
        $this->assertEquals('04358394', $network->getHDPrivByte());
        $this->assertEquals('043587cf', $network->getHDPubByte());
        $this->assertEquals('4e455552', $network->getNetMagicBytes());
        $this->assertEquals("Neurai Signed Message", $network->getSignedMessageMagic());
    }
}
