<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Asset;

/**
 * Represents CNewAsset from the Neurai source (assettypes.h).
 *
 * Serialization layout (Bitcoin compact-size encoding):
 *   compact_size + name bytes
 *   int64_t  nAmount      (8 bytes LE)
 *   int8_t   units        (1 byte)
 *   int8_t   nReissuable  (1 byte)
 *   int8_t   nHasIPFS     (1 byte)
 *   [34 bytes ipfsHash if nHasIPFS == 1]
 */
class NewAsset
{
    const MAX_NAME_LENGTH = 32; // 31 chars + null from C++ (MAX_NAME_LENGTH=32 in assets.h)
    const MAX_UNIT = 8;
    const MIN_UNIT = 0;

    /** @var string */
    public $name;

    /** @var int  satoshi-denominated (1 XNA = 1_0000_0000) */
    public $amount;

    /** @var int  0–8 decimal places */
    public $units;

    /** @var bool */
    public $reissuable;

    /** @var string|null  34-byte raw IPFS hash, or null */
    public $ipfsHash;

    public function __construct(
        string $name,
        int $amount,
        int $units = 8,
        bool $reissuable = true,
        ?string $ipfsHash = null
    ) {
        $this->name       = $name;
        $this->amount     = $amount;
        $this->units      = $units;
        $this->reissuable = $reissuable;
        $this->ipfsHash   = $ipfsHash;
    }

    /**
     * Serialize to binary (same layout as CNewAsset::SerializationOp).
     */
    public function serialize(): string
    {
        $data  = AssetScript::encodeString($this->name);
        $data .= pack('P', $this->amount);            // int64_t LE
        $data .= pack('c', $this->units);             // int8_t
        $data .= pack('c', $this->reissuable ? 1 : 0);
        $hasIPFS = ($this->ipfsHash !== null) ? 1 : 0;
        $data .= pack('c', $hasIPFS);
        if ($hasIPFS) {
            $data .= AssetScript::encodeIpfsHash($this->ipfsHash);
        }
        return $data;
    }

    /**
     * Deserialize from binary stream, advancing $offset.
     */
    public static function deserialize(string $bin, int &$offset): self
    {
        $name       = AssetScript::decodeString($bin, $offset);
        $amount     = AssetScript::decodeInt64($bin, $offset);
        $units      = ord($bin[$offset++]);
        $reissuable = ord($bin[$offset++]) === 1;
        $hasIPFS    = ord($bin[$offset++]) === 1;
        $ipfsHash   = null;
        if ($hasIPFS) {
            $ipfsHash = AssetScript::decodeIpfsHash($bin, $offset);
        }
        return new self($name, $amount, $units, $reissuable, $ipfsHash);
    }
}
