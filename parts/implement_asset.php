<?php
require_once('lib/libsnaa.php');
$snaa = new Snaa();

/* required env:
     ASSETDIR
     MADODIR
*/


$snaa->implement_asset(ASSETDIR.$argv[1].'.json');
