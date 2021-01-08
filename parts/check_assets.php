<?php
require_once('lib/libsnaa.php');
$snaa = new Snaa();

/* required env:
     ASSETDIR
     MADODIR
*/


switch ($argv[1]) {
	case 'extra':
		$snaa->check_asset(ASSETDIR.'asset_char_list.json');
		$snaa->check_asset(ASSETDIR.'asset_prologue_voice.json');
		$snaa->check_asset(ASSETDIR.'asset_prologue_main.json');
		break;
	case 'main':
		$snaa->check_asset(ASSETDIR.'asset_main.json');
		break;
	case 'sound':
		$snaa->check_asset(ASSETDIR.'asset_fullvoice.json');
		$snaa->check_asset(ASSETDIR.'asset_voice.json');
		break;
	case 'movie_high':
		$snaa->check_asset(ASSETDIR.'asset_movie_high.json');
		break;
	case 'movie_low':
		$snaa->check_asset(ASSETDIR.'asset_movie_low.json');
	default:
}
