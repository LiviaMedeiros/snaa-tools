#!/bin/bash
[ -f ./SETME/env.sh ] && . ./SETME/env.sh || { echo 'snaa'; exit 1; }



snaa_print "checking assets: extra!"
php ./parts/check_assets.php extra

snaa_print "checking assets: main!"
php ./parts/check_assets.php main

snaa_print "checking assets: sound!"
php ./parts/check_assets.php sound

snaa_print "checking assets: movie_high!"
php ./parts/check_assets.php movie_high

snaa_print "checking assets: movie_low!"
#php ./parts/check_assets.php movie_low

snaa_print "all done!"

exit 0
