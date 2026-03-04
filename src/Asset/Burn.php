<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Asset;

/**
 * Burn addresses and amounts for Neurai asset operations.
 * Values sourced from chainparams.cpp (CMainParams / CTestNetParams).
 *
 * Amounts are in satoshis (1 XNA = 100_000_000).
 */
class Burn
{
    // -------------------------------------------------------------------------
    // Mainnet
    // -------------------------------------------------------------------------
    const MAINNET_ISSUE_ADDRESS              = 'NbURNXXXXXXXXXXXXXXXXXXXXXXXT65Gdr';
    const MAINNET_REISSUE_ADDRESS            = 'NXReissueAssetXXXXXXXXXXXXXXWLe4Ao';
    const MAINNET_ISSUE_SUB_ADDRESS          = 'NXissueSubAssetXXXXXXXXXXXXXX6B2JF';
    const MAINNET_ISSUE_UNIQUE_ADDRESS       = 'NXissueUniqueAssetXXXXXXXXXXUBzP4Z';
    const MAINNET_ISSUE_MSGCHANNEL_ADDRESS   = 'NXissueMsgChanneLAssetXXXXXXTUzrtJ';
    const MAINNET_ISSUE_QUALIFIER_ADDRESS    = 'NXissueQuaLifierXXXXXXXXXXXXWurNcU';
    const MAINNET_ISSUE_SUBQUALIFIER_ADDRESS = 'NXissueSubQuaLifierXXXXXXXXXV71vM3';
    const MAINNET_ISSUE_RESTRICTED_ADDRESS   = 'NXissueRestrictedXXXXXXXXXXXWpXx4H';
    const MAINNET_ADD_TAG_ADDRESS            = 'NXaddTagBurnXXXXXXXXXXXXXXXXWucUTr';

    // -------------------------------------------------------------------------
    // Testnet
    // -------------------------------------------------------------------------
    const TESTNET_ISSUE_ADDRESS              = 'tBURNXXXXXXXXXXXXXXXXXXXXXXXVZLroy';
    const TESTNET_REISSUE_ADDRESS            = 'tAssetXXXXXXXXXXXXXXXXXXXXXXas6pz8';
    const TESTNET_ISSUE_SUB_ADDRESS          = 'tSubAssetXXXXXXXXXXXXXXXXXXXXGTvF4';
    const TESTNET_ISSUE_UNIQUE_ADDRESS       = 'tUniqueAssetXXXXXXXXXXXXXXXXVCgpLs';
    const TESTNET_ISSUE_MSGCHANNEL_ADDRESS   = 'tMsgChanneLAssetXXXXXXXXXXXXVsJoya';
    const TESTNET_ISSUE_QUALIFIER_ADDRESS    = 'tQuaLifierXXXXXXXXXXXXXXXXXXT5czoV';
    const TESTNET_ISSUE_SUBQUALIFIER_ADDRESS = 'tSubQuaLifierXXXXXXXXXXXXXXXW5MmGk';
    const TESTNET_ISSUE_RESTRICTED_ADDRESS   = 'tRestrictedXXXXXXXXXXXXXXXXXVyPBEK';
    const TESTNET_ADD_TAG_ADDRESS            = 'tTagBurnXXXXXXXXXXXXXXXXXXXXYm6pxA';

    // -------------------------------------------------------------------------
    // Amounts (satoshis) — same for mainnet and testnet
    // -------------------------------------------------------------------------
    const AMOUNT_ISSUE           = 100000000000; // 1000 XNA
    const AMOUNT_REISSUE         =  20000000000; //  200 XNA
    const AMOUNT_ISSUE_SUB       =  20000000000; //  200 XNA
    const AMOUNT_ISSUE_UNIQUE    =   1000000000; //   10 XNA
    const AMOUNT_ISSUE_MSGCHANNEL=  20000000000; //  200 XNA
    const AMOUNT_ISSUE_QUALIFIER = 200000000000; // 2000 XNA
    const AMOUNT_ISSUE_SUBQUALIF =  20000000000; //  200 XNA
    const AMOUNT_ISSUE_RESTRICTED= 300000000000; // 3000 XNA
    const AMOUNT_ADD_TAG         =    20000000;  //  0.2 XNA

    /**
     * Return the mainnet burn address for a given AssetType constant.
     */
    public static function mainnetAddress(int $assetType): string
    {
        switch ($assetType) {
            case AssetType::ROOT:
                return self::MAINNET_ISSUE_ADDRESS;
            case AssetType::SUB:
                return self::MAINNET_ISSUE_SUB_ADDRESS;
            case AssetType::UNIQUE:
                return self::MAINNET_ISSUE_UNIQUE_ADDRESS;
            case AssetType::MSGCHANNEL:
                return self::MAINNET_ISSUE_MSGCHANNEL_ADDRESS;
            case AssetType::QUALIFIER:
                return self::MAINNET_ISSUE_QUALIFIER_ADDRESS;
            case AssetType::SUB_QUALIFIER:
                return self::MAINNET_ISSUE_SUBQUALIFIER_ADDRESS;
            case AssetType::RESTRICTED:
                return self::MAINNET_ISSUE_RESTRICTED_ADDRESS;
            case AssetType::REISSUE:
                return self::MAINNET_REISSUE_ADDRESS;
            default:
                throw new \InvalidArgumentException("No burn address for asset type $assetType");
        }
    }

    /**
     * Return the burn amount (satoshis) for a given AssetType constant.
     */
    public static function amount(int $assetType): int
    {
        switch ($assetType) {
            case AssetType::ROOT:
                return self::AMOUNT_ISSUE;
            case AssetType::SUB:
                return self::AMOUNT_ISSUE_SUB;
            case AssetType::UNIQUE:
                return self::AMOUNT_ISSUE_UNIQUE;
            case AssetType::MSGCHANNEL:
                return self::AMOUNT_ISSUE_MSGCHANNEL;
            case AssetType::QUALIFIER:
                return self::AMOUNT_ISSUE_QUALIFIER;
            case AssetType::SUB_QUALIFIER:
                return self::AMOUNT_ISSUE_SUBQUALIF;
            case AssetType::RESTRICTED:
                return self::AMOUNT_ISSUE_RESTRICTED;
            case AssetType::REISSUE:
                return self::AMOUNT_REISSUE;
            default:
                throw new \InvalidArgumentException("No burn amount for asset type $assetType");
        }
    }
}
