<?php
require_once('lib/libsnaa.php');

/* required env:
     BASEDIR
     LISTDIR
*/


Snaa::optimize_json($argv[1].'.snaa');
