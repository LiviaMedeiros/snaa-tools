<?php
require_once('lib/libsnaa.php');

/* required env:
     BASEDIR
     LISTDIR
*/


optimize_json($argv[1].'.snaa');
