#!/bin/bash
[ -f ./SETME/env.sh ] && . ./SETME/env.sh || { echo 'snaa'; exit 1; }


snaa_print "generating filelists!"
bash ./parts/generate_filelists.sh

snaa_print "generating assets!"
php ./parts/generate_assets.php

snaa_print "compressing assets!"
bash ./parts/compress_assets.sh

snaa_print "all done!"

exit 0
