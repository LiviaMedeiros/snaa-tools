#!/bin/bash
. ./SETME/env.sh


snaa_print "preparing madomagi!"
php ./parts/generate_madomagi.php

snaa_print "making madomagi!"
bash ./parts/generate_madomagi.sh

snaa_print "all done!"


exit 0
