<?php
require_once('lib/libsnaa.php');

/* required env:
     ASSETDIR
     MADODIR
*/


implement_asset(ASSETDIR.$argv[1].'.json');
