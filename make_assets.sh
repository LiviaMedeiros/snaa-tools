#!/bin/bash

# CHANGE ME
export  BASEDIR="/SNAA/magica/resource/download/asset/master/resource/"
export  ORIGDIR="/SNAA/magica/resource/download/asset/vanilla/"
export  LISTDIR="/tmp/snaa-tools/filelist/"
export ASSETDIR="/tmp/snaa-tools/asset/"
export CHARLIST="image_native/scene/download/char_list.json"

# default
export SNAASIZE="4194304"
export SNAAMULT="2"

# snaa
# unset!

# slice_1MB
#export SNAASIZE="1048576"
#export SNAAMULT="1"


mkdir -p ${LISTDIR}
mkdir -p ${ASSETDIR}

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> generating filelists!" $(tput sgr0)
bash ./parts/generate_filelists.sh

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> generating assets!" $(tput sgr0)
php ./parts/generate_assets.php

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> compressing assets!" $(tput sgr0)
bash ./parts/compress_assets.sh

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> all done!" $(tput sgr0)

exit 0