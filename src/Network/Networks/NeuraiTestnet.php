<?php

namespace BitWasp\Bitcoin\Network\Networks;

use BitWasp\Bitcoin\Network\Network;
use BitWasp\Bitcoin\Script\ScriptType;

class NeuraiTestnet extends Network
{
    /**
     * {@inheritdoc}
     * @see Network::$base58PrefixMap
     */
    protected $base58PrefixMap = [
        self::BASE58_ADDRESS_P2PKH => "7f", // decimal 127 → addresses start with 't'
        self::BASE58_ADDRESS_P2SH  => "c4", // decimal 196
        self::BASE58_WIF           => "ef", // decimal 239
    ];

    /**
     * {@inheritdoc}
     * @see Network::$bip32PrefixMap
     */
    protected $bip32PrefixMap = [
        self::BIP32_PREFIX_XPUB => "043587cf",
        self::BIP32_PREFIX_XPRV => "04358394",
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
     * pchMessageStart = {0x52, 0x55, 0x45, 0x4e} (RUEN), stored reversed
     */
    protected $p2pMagic = "4e455552";
}
