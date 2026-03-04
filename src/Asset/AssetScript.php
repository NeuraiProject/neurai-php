<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Asset;

use BitWasp\Bitcoin\Script\Opcodes;
use BitWasp\Bitcoin\Script\ScriptFactory;
use BitWasp\Bitcoin\Script\ScriptInterface;
use BitWasp\Buffertools\Buffer;

/**
 * Builds and parses Neurai asset scripts.
 *
 * Asset scripts append a suffix to a standard P2PKH output:
 *
 *   <P2PKH> OP_XNA_ASSET <pushdata("rvnq"|"rvnt"|"rvnr" + serialized_data)> OP_DROP
 *
 * The 4-byte tag identifies the operation:
 *   "rvnq" — new asset (CNewAsset)
 *   "rvno" — owner token
 *   "rvnt" — transfer (CAssetTransfer)
 *   "rvnr" — reissue  (CReissueAsset)
 */
class AssetScript
{
    // IPFS SHA2-256 multi-hash prefix bytes (from assettypes.h)
    const IPFS_SHA2_256     = 0x12;
    const IPFS_SHA2_256_LEN = 0x20;
    const TXID_NOTIFIER     = 0x54;

    // -------------------------------------------------------------------------
    // Script builders
    // -------------------------------------------------------------------------

    /**
     * Append the OP_XNA_ASSET suffix for a new asset to an existing script.
     *
     * The caller is responsible for providing the base P2PKH scriptPubKey.
     * This returns only the asset suffix; concatenate with the base script binary.
     */
    public static function newAssetSuffix(NewAsset $asset): string
    {
        $payload = AssetType::TAG_NEW . $asset->serialize();
        return self::buildSuffix($payload);
    }

    /**
     * Asset suffix for an owner token output.
     * Owner token name is the asset name + "!".
     */
    public static function ownerSuffix(string $assetName): string
    {
        $ownerName = $assetName . "!";
        $payload = AssetType::TAG_OWNER . self::encodeString($ownerName);
        return self::buildSuffix($payload);
    }

    /**
     * Asset suffix for a transfer output.
     */
    public static function transferSuffix(AssetTransfer $transfer): string
    {
        $payload = AssetType::TAG_TRANSFER . $transfer->serialize();
        return self::buildSuffix($payload);
    }

    /**
     * Asset suffix for a reissue output.
     */
    public static function reissueSuffix(ReissueAsset $reissue): string
    {
        $payload = AssetType::TAG_REISSUE . $reissue->serialize();
        return self::buildSuffix($payload);
    }

    /**
     * Build the raw binary suffix:  OP_XNA_ASSET <pushdata> OP_DROP
     */
    private static function buildSuffix(string $payload): string
    {
        return chr(Opcodes::OP_XNA_ASSET)
            . self::pushData($payload)
            . chr(Opcodes::OP_DROP);
    }

    // -------------------------------------------------------------------------
    // Script parser
    // -------------------------------------------------------------------------

    /**
     * Detect if a script contains an asset suffix (has OP_XNA_ASSET).
     */
    public static function hasAsset(ScriptInterface $script): bool
    {
        return strpos($script->getBinary(), chr(Opcodes::OP_XNA_ASSET)) !== false;
    }

    /**
     * Parse the asset payload from a script.
     * Returns one of: NewAsset, AssetTransfer, ReissueAsset, or null on failure.
     *
     * @return NewAsset|AssetTransfer|ReissueAsset|null
     */
    public static function parseAsset(ScriptInterface $script)
    {
        $bin = $script->getBinary();
        $pos = strpos($bin, chr(Opcodes::OP_XNA_ASSET));
        if ($pos === false) {
            return null;
        }

        // After OP_XNA_ASSET comes a pushdata instruction + payload
        $pos++; // skip OP_XNA_ASSET
        $payload = self::readPushData($bin, $pos);
        if ($payload === null || strlen($payload) < 4) {
            return null;
        }

        $tag  = substr($payload, 0, 4);
        $data = substr($payload, 4);
        $off  = 0;

        switch ($tag) {
            case AssetType::TAG_NEW:
                return NewAsset::deserialize($data, $off);
            case AssetType::TAG_TRANSFER:
                return AssetTransfer::deserialize($data, $off);
            case AssetType::TAG_REISSUE:
                return ReissueAsset::deserialize($data, $off);
            case AssetType::TAG_OWNER:
                // Owner tag: payload is just the owner token name string
                $name = self::decodeString($data, $off);
                return new NewAsset($name, 100000000, 0, false); // 1 COIN, no decimals
            default:
                return null;
        }
    }

    // -------------------------------------------------------------------------
    // Binary encoding helpers (Bitcoin compact-size / little-endian)
    // -------------------------------------------------------------------------

    /**
     * Encode a string with Bitcoin compact-size length prefix.
     */
    public static function encodeString(string $s): string
    {
        return self::compactSize(strlen($s)) . $s;
    }

    /**
     * Decode a compact-size prefixed string, advancing $offset.
     */
    public static function decodeString(string $bin, int &$offset): string
    {
        $len = self::readCompactSize($bin, $offset);
        $str = substr($bin, $offset, $len);
        $offset += $len;
        return $str;
    }

    /**
     * Read a little-endian int64, advancing $offset.
     */
    public static function decodeInt64(string $bin, int &$offset): int
    {
        $unpacked = unpack('P', substr($bin, $offset, 8));
        $offset += 8;
        return $unpacked[1];
    }

    /**
     * Encode an IPFS hash for the wire format.
     * Accepts either a 34-byte raw hash (0x12+0x20+32 bytes) or a 32-byte txid.
     */
    public static function encodeIpfsHash(string $hash): string
    {
        if (strlen($hash) === 34) {
            // Standard IPFS SHA2-256: write 0x12 + 32 content bytes (skip 0x20 length byte)
            return chr(self::IPFS_SHA2_256) . substr($hash, 2, 32);
        }
        // TXID notifier
        return chr(self::TXID_NOTIFIER) . substr($hash, 0, 32);
    }

    /**
     * Decode an IPFS hash from a binary stream, advancing $offset.
     * Returns the full 34-byte (or 33-byte) hash.
     */
    public static function decodeIpfsHash(string $bin, int &$offset): string
    {
        $type = ord($bin[$offset++]);
        $content = substr($bin, $offset, 32);
        $offset += 32;
        if ($type === self::IPFS_SHA2_256) {
            return chr(self::IPFS_SHA2_256) . chr(self::IPFS_SHA2_256_LEN) . $content;
        }
        return chr($type) . $content;
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Encode a Bitcoin compact-size integer.
     */
    private static function compactSize(int $n): string
    {
        if ($n < 0xfd) {
            return chr($n);
        }
        if ($n <= 0xffff) {
            return "\xfd" . pack('v', $n);
        }
        return "\xfe" . pack('V', $n);
    }

    /**
     * Read a Bitcoin compact-size integer, advancing $offset.
     */
    private static function readCompactSize(string $bin, int &$offset): int
    {
        $first = ord($bin[$offset++]);
        if ($first < 0xfd) {
            return $first;
        }
        if ($first === 0xfd) {
            $n = unpack('v', substr($bin, $offset, 2))[1];
            $offset += 2;
            return $n;
        }
        $n = unpack('V', substr($bin, $offset, 4))[1];
        $offset += 4;
        return $n;
    }

    /**
     * Build a Bitcoin-style pushdata instruction for arbitrary-length data.
     */
    private static function pushData(string $data): string
    {
        $len = strlen($data);
        if ($len <= 75) {
            return chr($len) . $data;
        }
        if ($len <= 0xff) {
            return chr(Opcodes::OP_PUSHDATA1) . chr($len) . $data;
        }
        if ($len <= 0xffff) {
            return chr(Opcodes::OP_PUSHDATA2) . pack('v', $len) . $data;
        }
        return chr(Opcodes::OP_PUSHDATA4) . pack('V', $len) . $data;
    }

    /**
     * Read a pushdata payload from a script binary, advancing $offset.
     */
    private static function readPushData(string $bin, int &$offset): ?string
    {
        if ($offset >= strlen($bin)) {
            return null;
        }
        $op = ord($bin[$offset++]);
        if ($op >= 1 && $op <= 75) {
            $data = substr($bin, $offset, $op);
            $offset += $op;
            return $data;
        }
        if ($op === Opcodes::OP_PUSHDATA1) {
            $len = ord($bin[$offset++]);
            $data = substr($bin, $offset, $len);
            $offset += $len;
            return $data;
        }
        if ($op === Opcodes::OP_PUSHDATA2) {
            $len = unpack('v', substr($bin, $offset, 2))[1];
            $offset += 2;
            $data = substr($bin, $offset, $len);
            $offset += $len;
            return $data;
        }
        if ($op === Opcodes::OP_PUSHDATA4) {
            $len = unpack('V', substr($bin, $offset, 4))[1];
            $offset += 4;
            $data = substr($bin, $offset, $len);
            $offset += $len;
            return $data;
        }
        return null;
    }
}
