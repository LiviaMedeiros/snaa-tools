<?php
require_once('lib/libsnaa.php');

/* required env:
     ASSETDIR
     MADODIR
*/


switch ($argv[1]) {
	case 'extra':
		Snaa::check_asset(ASSETDIR.'asset_char_list.json');
		Snaa::check_asset(ASSETDIR.'asset_prologue_voice.json');
		Snaa::check_asset(ASSETDIR.'asset_prologue_main.json');
		break;
	case 'main':
		Snaa::check_asset(ASSETDIR.'asset_main.json');
		break;
	case 'sound':
		Snaa::check_asset(ASSETDIR.'asset_fullvoice.json');
		Snaa::check_asset(ASSETDIR.'asset_voice.json');
		break;
	case 'movie_high':
		Snaa::check_asset(ASSETDIR.'asset_movie_high.json');
		break;
	case 'movie_low':
		Snaa::check_asset(ASSETDIR.'asset_movie_low.json');
	default:
}
