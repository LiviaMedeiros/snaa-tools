#!/bin/bash
. ./SETME/env.sh


echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> generating filelists!" $(tput sgr0)
bash ./parts/generate_filelists.sh

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> generating assets!" $(tput sgr0)
php ./parts/generate_assets.php

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> compressing assets!" $(tput sgr0)
bash ./parts/compress_assets.sh

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> all done!" $(tput sgr0)

exit 0
