#!/bin/bash
[ -f ./SETME/env.sh ] && . ./SETME/env.sh || { echo 'snaa'; exit 1; }


snaa_print "generating filelists!"
bash ./parts/generate_filelists.sh

snaa_print "optimizing scenario-json!"
php ./parts/optimize_scenario-json.php

snaa_print "optimizing web png!"
bash ./parts/optimize_png.sh image_web-png ${IMWBDIR}

snaa_print "optimizing resource png!"
bash ./parts/optimize_png.sh image_native-png ${BASEDIR}

snaa_print "optimizing plist!" $(tput sgr0)
php ./parts/optimize_xml.php all-plist

snaa_print "all done!"

exit 0
