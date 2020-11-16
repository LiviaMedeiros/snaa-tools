<?php
$wednesday = getenv(); // $_ENV may be disabled
foreach (['BASEDIR', 'ORIGDIR', 'LISTDIR', 'ASSETDIR', 'SNAASIZE', 'SNAAMULT'] as $wedkey)
	if (isset($wednesday[$wedkey]))
		define($wedkey, $wednesday[$wedkey]);

// must be set!
/*
define('BASEDIR',  '/SNAA/magica/resource/download/asset/master/resource/');
define('ORIGDIR',  '/SNAA/magica/resource/download/asset/master/vanilla/');
define('LISTDIR',  '/tmp/snaa-tools/filelist/');
define('ASSETDIR', '/tmp/snaa-tools/asset/');
define('SNAASIZE', 4194304);
define('SNAAMULT', 2);
*/


function copy_asset($origasset, $fileasset) {
	$asset = json_decode(file_get_contents(ORIGDIR.$origasset), true);
	echo "copying: ".$origasset." -> ".$fileasset."\n" ;
	return file_put_contents(ASSETDIR.$fileasset, json_encode($asset, JSON_UNESCAPED_SLASHES));
}

function reverse_asset($origasset, $filelist) {
	$asset = json_decode(file_get_contents(ORIGDIR.$origasset), true);
	$cnt = count($asset);
	echo "reverse START: ".$origasset." -> ".$filelist."\n";

	$files = '';
	$i = 0;
	$s = 0;
	foreach ($asset as $file) {
		echo ($i++)."/".$cnt."\r";
		if (preg_match('/\.a[a-z][a-z]$/', $file['path']))
			$s++;
		else
			$files .= $file['path']."\n";
	}

	echo "reverse DONE: ".$s." skips in ".$cnt." files\n";
	return file_put_contents(LISTDIR.$filelist, $files);
}

function generate_asset($filelist, $fileasset) {
	$files = explode("\n", file_get_contents(LISTDIR.$filelist));
	$cnt = count($files) - 1;
	echo "generate START: ".$filelist." -> ".$fileasset."\n";

	$objs = [];
	$i = 0;
	$totes = 0;
	foreach ($files as $file) {
		if ($file == '') // empty last line
			continue;
		echo ($i++)."/".$cnt."\r";
		$filepath = BASEDIR.$file;
		$filesize = (int)filesize($filepath);
		$md5_file = md5_file($filepath);
		$userfile = str_replace(['movie/char/high/', 'movie/char/low/'], ['movie/char/', 'movie/char/'], $file);
		$totes += $filesize;

		$file_list = [];
		if (defined('SNAASIZE') && defined('SNAAMULT') && $filesize > SNAASIZE * SNAAMULT) {
			for ($chunk_b = 0; $filesize > 0; $filesize -= SNAASIZE) {
				$chunk_s = min($filesize, SNAASIZE);
				$chunk_e = $chunk_b + $chunk_s;
				$file_list[] = [
					'size' => $chunk_s,
					'url' => 'CHUNK='.$file.','.$chunk_b.'-'.($chunk_e-1).'#'
				];
				$chunk_b = $chunk_e;
			}
		} else {
			$file_list[] = [
				'size' => $filesize,
				'url' => $file
			];
		}

		$objs[] = [
			'file_list' => $file_list,
			'md5' => $md5_file,
			'path' => $userfile
		];
	}

	echo "generate DONE: ".$totes." bytes in ".$cnt." files\n";
	return file_put_contents(ASSETDIR.$fileasset, json_encode($objs, JSON_UNESCAPED_SLASHES));
}

copy_asset('asset_config.json.gz',    'asset_config.json');
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
