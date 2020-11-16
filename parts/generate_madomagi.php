<?php
$wednesday = getenv(); // $_ENV may be disabled
foreach (['MADODIR', 'ASSETDIR'] as $wedkey)
	if (isset($wednesday[$wedkey]))
		define($wedkey, $wednesday[$wedkey]);

// must be set!
/*
define('MADODIR',  '/tmp/snaa-tools/madomagi/');
define('ASSETDIR', '/tmp/snaa-tools/asset/');
*/


function calc_etag($filepath) {
	$fullpath = ASSETDIR.$filepath;
	// insert your math here
	return 'snaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
}

function make_C5XyOsaM() {
	$C5XyOsaM = [
		'C5XyOsaM' => '8c88d9d9d8888f8990dcdedfd89089d9dc8a90858c85df908e8a8cdcd98b888f8588df8f'
	];
	return file_put_contents(MADODIR.'C5XyOsaM.json', json_encode($C5XyOsaM));
}

function make_db($dbfile) {
	$assetfiles = [ // playing with voices and high quality video
		'asset_config.json',
		'asset_char_list.json',
		'asset_main.json',
		'asset_fullvoice.json',
		'asset_voice.json',
		'asset_movie_high.json'
	];
	$realassetfiles = [
		'asset_main.json',
		'asset_fullvoice.json',
		'asset_voice.json',
		'asset_movie_high.json',
		'asset_movie_low.json'
	];

	if (is_file(MADODIR.$dbfile))
		unlink(MADODIR.$dbfile);
	$db = new SQLite3(MADODIR.$dbfile);
	$db->query("CREATE TABLE download_asset(path char(128) primary key,md5 char(128))");
	$db->query("CREATE TABLE asset_json(file char(128) primary key,etag char(128))");

	foreach ($assetfiles as $assetfile) {
		echo "making ".$dbfile."/".$assetfile."\n";
		copy(ASSETDIR.$assetfile, MADODIR.$assetfile);
		$db->query("INSERT INTO asset_json VALUES('".$assetfile."','\"".calc_etag($assetfile)."\"')");
		if (!in_array($assetfile, $realassetfiles))
			continue;
		$asset = json_decode(file_get_contents(ASSETDIR.$assetfile), true);

		$values = [];
		foreach ($asset as $file)
			$values[] = "('resource/".$file['path']."','".$file['md5']."')";

		$db->query("INSERT INTO download_asset VALUES ".implode(",",$values));
	}
	return true;
}

make_C5XyOsaM();
make_db('madomagi.db');
