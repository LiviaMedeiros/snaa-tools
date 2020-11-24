#!/bin/bash
. ./SETME/env.sh


echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> implementing assets: main!" $(tput sgr0)
php ./parts/implement_asset.php asset_main

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> implementing assets: fullvoice!" $(tput sgr0)
php ./parts/implement_asset.php asset_fullvoice

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> implementing assets: voice!" $(tput sgr0)
php ./parts/implement_asset.php asset_voice

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> implementing assets: movie_high!" $(tput sgr0)
php ./parts/implement_asset.php asset_movie_high

#echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> implementing assets: movie_low!" $(tput sgr0)
#php ./parts/implement_asset.php asset_movie_low

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> all done!" $(tput sgr0)

exit 0
