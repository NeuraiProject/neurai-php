<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Asset;

class AssetType
{
    const ROOT           = 0;
    const SUB            = 1;
    const UNIQUE         = 2;
    const MSGCHANNEL     = 3;
    const QUALIFIER      = 4;
    const SUB_QUALIFIER  = 5;
    const RESTRICTED     = 6;
    const VOTE           = 7;
    const REISSUE        = 8;
    const OWNER          = 9;
    const INVALID        = 11;

    // Script tag bytes: ASCII codes of 'r','v','n' + type discriminant
    // (matching XNA_R=114, XNA_V=118, XNA_N=110 in assets.h)
    const TAG_NEW      = "rvnq"; // new asset (ROOT/SUB/UNIQUE/…)
    const TAG_OWNER    = "rvno"; // owner token
    const TAG_TRANSFER = "rvnt"; // transfer
    const TAG_REISSUE  = "rvnr"; // reissue
}
