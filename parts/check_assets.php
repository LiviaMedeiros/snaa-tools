<?php
require_once('lib/libsnaa.php');

/* required env:
     ASSETDIR
     MADODIR
*/


switch ($argv[1]) {
	case 'extra':
		check_asset(ASSETDIR.'asset_char_list.json');
		check_asset(ASSETDIR.'asset_prologue_voice.json');
		check_asset(ASSETDIR.'asset_prologue_main.json');
		break;
	case 'main':
		check_asset(ASSETDIR.'asset_main.json');
		break;
	case 'sound':
		check_asset(ASSETDIR.'asset_fullvoice.json');
		check_asset(ASSETDIR.'asset_voice.json');
		break;
	case 'movie_high':
		check_asset(ASSETDIR.'asset_movie_high.json');
		break;
	case 'movie_low':
		check_asset(ASSETDIR.'asset_movie_low.json');
	default:
}
