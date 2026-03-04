<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Asset;

/**
 * Represents CReissueAsset from the Neurai source (assettypes.h).
 *
 * Serialization layout:
 *   compact_size + name bytes
 *   int64_t  nAmount      (8 bytes LE)
 *   int8_t   nUnits       (1 byte, -1 means no change)
 *   int8_t   nReissuable  (1 byte)
 *   [34 bytes ipfsHash if present]
 */
class ReissueAsset
{
    /** @var string */
    public $name;

    /** @var int  additional satoshis to issue */
    public $amount;

    /** @var int  new decimal places, -1 = no change */
    public $units;

    /** @var bool */
    public $reissuable;

    /** @var string|null  34-byte raw IPFS hash, or null */
    public $ipfsHash;

    public function __construct(
        string $name,
        int $amount,
        int $units = -1,
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
     * Serialize to binary (same layout as CReissueAsset::SerializationOp).
     */
    public function serialize(): string
    {
        $data  = AssetScript::encodeString($this->name);
        $data .= pack('P', $this->amount);
        $data .= pack('c', $this->units);
        $data .= pack('c', $this->reissuable ? 1 : 0);
        if ($this->ipfsHash !== null) {
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
        $units      = unpack('c', $bin[$offset++])[1];
        $reissuable = ord($bin[$offset++]) === 1;
        $ipfsHash   = null;
        if (strlen($bin) - $offset >= 33) {
            $ipfsHash = AssetScript::decodeIpfsHash($bin, $offset);
        }
        return new self($name, $amount, $units, $reissuable, $ipfsHash);
    }
}
