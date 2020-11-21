<?php
require_once('lib/libsnaa.php');

/* required env:
     BASEDIR
     LISTDIR
*/

optimize_xml($argv[1].'.snaa');