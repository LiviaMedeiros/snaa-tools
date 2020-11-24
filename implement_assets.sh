#!/bin/bash
[ -f ./SETME/env.sh ] && . ./SETME/env.sh || { echo 'snaa'; exit 1; }


snaa_print "implementing assets: main!"
php ./parts/implement_asset.php asset_main

snaa_print "implementing assets: fullvoice!"
php ./parts/implement_asset.php asset_fullvoice

snaa_print "implementing assets: voice!"
php ./parts/implement_asset.php asset_voice

snaa_print "implementing assets: movie_high!"
php ./parts/implement_asset.php asset_movie_high

snaa_print "implementing assets: movie_low!"
#php ./parts/implement_asset.php asset_movie_low

snaa_print "all done!"

exit 0
