<?php
require_once('lib/libsnaa.php');

/* required env:
     ASSETDIR
     MADODIR
*/


$dir = MADODIR."resource/";

switch ($argv[1]) {
	case 'extra':
		check_asset(ASSETDIR.'asset_char_list.json', $dir);
		check_asset(ASSETDIR.'asset_prologue_voice.json', $dir);
		check_asset(ASSETDIR.'asset_prologue_main.json', $dir);
		break;
	case 'main':
		check_asset(ASSETDIR.'asset_main.json', $dir);
		break;
	case 'sound':
		check_asset(ASSETDIR.'asset_fullvoice.json', $dir);
		check_asset(ASSETDIR.'asset_voice.json', $dir);
		break;
	case 'movie_high':
		check_asset(ASSETDIR.'asset_movie_high.json', $dir);
		break;
	case 'movie_low':
		check_asset(ASSETDIR.'asset_movie_low.json', $dir);
	default:
}
