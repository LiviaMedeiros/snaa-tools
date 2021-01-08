<?php
require_once('lib/libsnaa.php');

/* required env:
     BASEDIR
     ORIGDIR
     LISTDIR
     ASSETDIR
   optional:
     CHARLIST
     SNAASIZE
     SNAAMULT
*/


Snaa::copy_asset('asset_config.json.gz', 'asset_config.json');

if (defined('CHARLIST'))
	Snaa::generate_charlist(CHARLIST, 'asset_char_list.json');
else
	Snaa::copy_asset('asset_char_list.json.gz', 'asset_char_list.json');

Snaa::reverse_asset('asset_prologue_voice.json.gz', 'prologue_voice.snaa');
Snaa::reverse_asset('asset_prologue_main.json.gz',  'prologue_main.snaa');

Snaa::generate_asset('fullvoice.snaa',      'asset_fullvoice.json');
Snaa::generate_asset('movie_high.snaa',     'asset_movie_high.json');
Snaa::generate_asset('movie_low.snaa',      'asset_movie_low.json');
Snaa::generate_asset('voice.snaa',          'asset_voice.json');
Snaa::generate_asset('main.snaa',           'asset_main.json');
Snaa::generate_asset('prologue_voice.snaa', 'asset_prologue_voice.json');
Snaa::generate_asset('prologue_main.snaa',  'asset_prologue_main.json');
