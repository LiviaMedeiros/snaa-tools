#!/bin/bash
. ./SETME/env.sh


echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> checking assets: extra!" $(tput sgr0)
php ./parts/check_assets.php extra

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> checking assets: main!" $(tput sgr0)
php ./parts/check_assets.php main

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> checking assets: sound!" $(tput sgr0)
php ./parts/check_assets.php sound

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> checking assets: movie_high!" $(tput sgr0)
php ./parts/check_assets.php movie_high

#echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> checking assets: movie_low!" $(tput sgr0)
#php ./parts/check_assets.php movie_low

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> all done!" $(tput sgr0)

exit 0
