#!/bin/bash
. ./SETME/env.sh


mkdir -p ${LISTDIR}

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> generating filelists!" $(tput sgr0)
bash ./parts/generate_filelists.sh

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> rebuilding scenario!" $(tput sgr0)
php ./parts/rebuild_scenario.php

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> all done!" $(tput sgr0)

exit 0
