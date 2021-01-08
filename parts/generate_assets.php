<?php
require_once('lib/libsnaa.php');
$snaa = new Snaa();

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


$snaa->copy_asset('asset_config.json.gz', 'asset_config.json');

if (defined('CHARLIST'))
	$snaa->generate_charlist(CHARLIST, 'asset_char_list.json');
else
	$snaa->copy_asset('asset_char_list.json.gz', 'asset_char_list.json');

$snaa->reverse_asset('asset_prologue_voice.json.gz', 'prologue_voice.snaa');
$snaa->reverse_asset('asset_prologue_main.json.gz',  'prologue_main.snaa');

$snaa->generate_asset('fullvoice.snaa',      'asset_fullvoice.json');
$snaa->generate_asset('movie_high.snaa',     'asset_movie_high.json');
$snaa->generate_asset('movie_low.snaa',      'asset_movie_low.json');
$snaa->generate_asset('voice.snaa',          'asset_voice.json');
$snaa->generate_asset('main.snaa',           'asset_main.json');
$snaa->generate_asset('prologue_voice.snaa', 'asset_prologue_voice.json');
$snaa->generate_asset('prologue_main.snaa',  'asset_prologue_main.json');
