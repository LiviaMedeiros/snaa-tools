#!/bin/bash

# CHANGE ME
export  MADODIR="/tmp/snaa-tools/madomagi/"
export  LISTDIR="/tmp/snaa-tools/filelist/"
export ASSETDIR="/tmp/snaa-tools/asset/"
export  BASEDIR="/SNAA/magica/resource/download/asset/master/resource/"
export  MADOTAR="${HOME}/madomagi.tar.lzo"


mkdir -p ${MADODIR}
mkdir -p ${LISTDIR}
mkdir -p ${ASSETDIR}

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> preparing madomagi!" $(tput sgr0)
php ./parts/generate_madomagi.php

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> making madomagi!" $(tput sgr0)
bash ./parts/generate_madomagi.sh

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> all done!" $(tput sgr0)


exit 0