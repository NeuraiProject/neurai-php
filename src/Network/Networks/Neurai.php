<?php

namespace BitWasp\Bitcoin\Network\Networks;

use BitWasp\Bitcoin\Network\Network;
use BitWasp\Bitcoin\Script\ScriptType;

class Neurai extends Network
{
    /**
     * {@inheritdoc}
     * @see Network::$base58PrefixMap
     */
    protected $base58PrefixMap = [
        self::BASE58_ADDRESS_P2PKH => "35", // decimal 53 → addresses start with 'N'
        self::BASE58_ADDRESS_P2SH  => "75", // decimal 117
        self::BASE58_WIF           => "80", // decimal 128
    ];

    /**
     * {@inheritdoc}
     * @see Network::$bip32PrefixMap
     */
    protected $bip32PrefixMap = [
        self::BIP32_PREFIX_XPUB => "0488b21e",
        self::BIP32_PREFIX_XPRV => "0488ade4",
    ];

    /**
     * {@inheritdoc}
     * @see Network::$bip32ScriptTypeMap
     */
    protected $bip32ScriptTypeMap = [
        self::BIP32_PREFIX_XPUB => ScriptType::P2PKH,
        self::BIP32_PREFIX_XPRV => ScriptType::P2PKH,
    ];

    /**
     * {@inheritdoc}
     * @see Network::$signedMessagePrefix
     */
    protected $signedMessagePrefix = "Neurai Signed Message";

    /**
     * {@inheritdoc}
     * @see Network::$p2pMagic
     * pchMessageStart = {0x4e, 0x45, 0x55, 0x52} (NEUR), stored reversed
     */
    protected $p2pMagic = "5255454e";
}
