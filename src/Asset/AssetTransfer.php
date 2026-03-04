<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Asset;

/**
 * Represents CAssetTransfer from the Neurai source (assettypes.h).
 *
 * Serialization layout:
 *   compact_size + name bytes
 *   int64_t  nAmount    (8 bytes LE)
 *   [34 bytes message/ipfsHash if present]
 *   [int64_t nExpireTime if message present and expireTime != 0]
 */
class AssetTransfer
{
    /** @var string */
    public $name;

    /** @var int  satoshi-denominated */
    public $amount;

    /** @var string|null  34-byte raw IPFS/message hash, or null */
    public $message;

    /** @var int  Unix timestamp, 0 means no expiry */
    public $expireTime;

    public function __construct(
        string $name,
        int $amount,
        ?string $message = null,
        int $expireTime = 0
    ) {
        $this->name       = $name;
        $this->amount     = $amount;
        $this->message    = $message;
        $this->expireTime = $expireTime;
    }

    /**
     * Serialize to binary (same layout as CAssetTransfer::SerializationOp).
     */
    public function serialize(): string
    {
        $data  = AssetScript::encodeString($this->name);
        $data .= pack('P', $this->amount); // int64_t LE
        if ($this->message !== null) {
            $data .= AssetScript::encodeIpfsHash($this->message);
            if ($this->expireTime !== 0) {
                $data .= pack('P', $this->expireTime);
            }
        }
        return $data;
    }

    /**
     * Deserialize from binary stream, advancing $offset.
     */
    public static function deserialize(string $bin, int &$offset): self
    {
        $name    = AssetScript::decodeString($bin, $offset);
        $amount  = AssetScript::decodeInt64($bin, $offset);
        $message = null;
        $expireTime = 0;
        $remaining = strlen($bin) - $offset;
        if ($remaining >= 33) {
            $message = AssetScript::decodeIpfsHash($bin, $offset);
            $remaining = strlen($bin) - $offset;
            if ($remaining >= 8) {
                $expireTime = AssetScript::decodeInt64($bin, $offset);
            }
        }
        return new self($name, $amount, $message, $expireTime);
    }
}
