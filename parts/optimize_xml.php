<?php
require_once('lib/libsnaa.php');
$snaa = new Snaa();

/* required env:
     BASEDIR
     LISTDIR
*/

$snaa->optimize_xml($argv[1].'.snaa');
