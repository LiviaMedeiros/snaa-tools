#!/bin/bash
. ./SETME/env.sh


mkdir -p ${LISTDIR}
mkdir -p ${SNAALOG}

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> generating filelists!" $(tput sgr0)
bash ./parts/generate_filelists.sh

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> optimizing scenario-json!" $(tput sgr0)
php ./parts/optimize_scenario-json.php

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> optimizing web png!" $(tput sgr0)
bash ./parts/optimize_png.sh image_web-png ${IMWBDIR}

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> optimizing resource png!" $(tput sgr0)
bash ./parts/optimize_png.sh image_native-png ${BASEDIR}

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> optimizing plist!" $(tput sgr0)
php ./parts/optimize_xml.php all-plist

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> all done!" $(tput sgr0)

exit 0
