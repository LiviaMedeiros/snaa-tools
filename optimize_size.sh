#!/bin/bash
[ -f ./SETME/env.sh ] && . ./SETME/env.sh || { echo 'snaa'; exit 1; }


snaa_print "generating filelists!"
bash ./parts/generate_filelists.sh

snaa_print "optimizing scenario-json!"
php ./parts/optimize_json.php scenario-json

snaa_print "optimizing live2d-json"
php ./parts/optimize_json.php live2d-json

snaa_print "optimizing image_native-exportjson"
php ./parts/optimize_json.php image_native-exportjson

snaa_print "optimizing web png!"
bash ./parts/optimize_png.sh image_web-png ${IMWBDIR} 8

snaa_print "optimizing resource png!"
bash ./parts/optimize_png.sh image_native-png ${BASEDIR} 8

snaa_print "optimizing plist!" $(tput sgr0)
php ./parts/optimize_xml.php all-plist

snaa_print "all done!"

exit 0
