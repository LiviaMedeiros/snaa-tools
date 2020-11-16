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


copy_asset('asset_config.json.gz',    'asset_config.json');

if (defined('CHARLIST'))
	generate_charlist(CHARLIST, 'asset_char_list.json');
else
	copy_asset('asset_char_list.json.gz', 'asset_char_list.json');

reverse_asset('asset_prologue_voice.json.gz', 'prologue_voice.txt');
reverse_asset('asset_prologue_main.json.gz',  'prologue_main.txt');

generate_asset('fullvoice.txt',      'asset_fullvoice.json');
generate_asset('movie_high.txt',     'asset_movie_high.json');
generate_asset('movie_low.txt',      'asset_movie_low.json');
generate_asset('voice.txt',          'asset_voice.json');
generate_asset('main.txt',           'asset_main.json');
generate_asset('prologue_voice.txt', 'asset_prologue_voice.json');
generate_asset('prologue_main.txt',  'asset_prologue_main.json');
