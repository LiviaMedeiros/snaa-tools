<?php
$wednesday = getenv(); // $_ENV may be disabled
foreach (['BASEDIR', 'ORIGDIR', 'LISTDIR', 'ASSETDIR', 'MADODIR', 'CHARLIST', 'SNAASIZE', 'SNAAMULT'] as $wedkey)
	if (isset($wednesday[$wedkey]))
		define($wedkey, $wednesday[$wedkey]);

/*
define('BASEDIR',  '/SNAA/magica/resource/download/asset/master/resource/');
define('ORIGDIR',  '/SNAA/magica/resource/download/asset/master/vanilla/');
define('LISTDIR',  '/tmp/snaa-tools/filelist/');
define('ASSETDIR', '/tmp/snaa-tools/asset/');
define('MADODIR',  '/tmp/snaa-tools/madomagi/');
define('CHARLIST', 'image_native/scene/download/char_list.json');
define('SNAASIZE', 4194304);
define('SNAAMULT', 2);
*/

function chunk_encode($f, $b, $e, $s = null) {
	return 'CHUNK='.$f.','.$b.'-'.($e - 1).'#';
}

function chunk_read($str) {
	return preg_match('/^CHUNK=([^,]*),([0-9]*)-([0-9]*)#$/', $str, $m)
	     ? file_get_contents(BASEDIR.$m[1], false, null, $m[2], $m[3] - $m[2] + 1)
	     : file_get_contents(BASEDIR.$str);
}

function calc_etag($filepath, $method = 's3') {
	switch ($method) {
		case 's3':
			$fullpath = ASSETDIR.$filepath.'.gz';
			return md5_file($fullpath);
		case 'nginx':
			$fullpath = ASSETDIR.$filepath; // change to final path to ensure mtime
			return sprintf('%x-%x', filemtime($fullpath), filesize($fullpath));
		default:
			return 'snaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
	}
}

function madomagi_C5XyOsaM() {
	$C5XyOsaM = [
		'C5XyOsaM' => '8c88d9d9d8888f8990dcdedfd89089d9dc8a90858c85df908e8a8cdcd98b888f8588df8f'
	];
	return file_put_contents(MADODIR.'C5XyOsaM.json', json_encode($C5XyOsaM));
}

function madomagi_db($dbfile, $quality = 'high', $voices = true) {
	$metaassetfiles = [
		'asset_config.json',
		'asset_char_list.json',
	];
	$assetfiles = [
		'asset_main.json'
	];
	if ($voices)
		array_push($assetfiles, 'asset_fullvoice.json', 'asset_voice.json');
	if ($quality == 'high')
		array_push($assetfiles, 'asset_movie_high.json');
	else if ($quality == 'low')
		array_push($assetfiles, 'asset_movie_low.json');

	if (is_file(MADODIR.$dbfile))
		unlink(MADODIR.$dbfile);
	$db = new SQLite3(MADODIR.$dbfile);
	$db->query("CREATE TABLE download_asset(path char(128) primary key,md5 char(128))");
	$db->query("CREATE TABLE asset_json(file char(128) primary key,etag char(128))");

	foreach (array_merge($metaassetfiles, $assetfiles) as $assetfile) {
		echo "making ".$dbfile."/".$assetfile."\n";
		copy(ASSETDIR.$assetfile, MADODIR.$assetfile);
		$db->query("INSERT INTO asset_json VALUES('".$assetfile."','\"".calc_etag($assetfile)."\"')");
		if (in_array($assetfile, $metaassetfiles))
			continue;
		$asset = json_decode(file_get_contents(ASSETDIR.$assetfile), true);

		$values = [];
		foreach ($asset as $file)
			$values[] = "('resource/".$file['path']."','".$file['md5']."')";

		$db->query("INSERT INTO download_asset VALUES ".implode(",",$values));
	}
	return true;
}

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

function file2asset($file) {
	$filepath = BASEDIR.$file;
	$filesize = filesize($filepath);
	$md5_file = md5_file($filepath);
	$userfile = str_replace(['movie/char/high/', 'movie/char/low/'], ['movie/char/', 'movie/char/'], $file);

	$file_list = [];
	if (defined('SNAASIZE') && defined('SNAAMULT') && $filesize > SNAASIZE * SNAAMULT) {
		for ($chunk_b = 0; $filesize > 0; $filesize -= SNAASIZE) {
			$chunk_s = min($filesize, (int)SNAASIZE);
			$chunk_e = $chunk_b + $chunk_s;
			$file_list[] = [
				'size' => $chunk_s,
				'url' => chunk_encode($file, $chunk_b, $chunk_e)
			];
			$chunk_b = $chunk_e;
		}
	} else {
		$file_list[] = [
			'size' => $filesize,
			'url' => $file
		];
	}

	return [
		'file_list' => $file_list,
		'md5' => $md5_file,
		'path' => $userfile
	];
}

function generate_asset($filelist, $fileasset) {
	$files = explode("\n", file_get_contents(LISTDIR.$filelist));
	$cnt = count($files) - 1;
	echo "generate START: ".$filelist." -> ".$fileasset."\n";

	$objs = [];
	$i = 0;
	foreach ($files as $file) {
		if ($file == '') // empty last line
			continue;
		echo ($i++)."/".$cnt."\r";
		$objs[] = file2asset($file);
	}

	echo "generate DONE: ".$cnt." files\n";
	return file_put_contents(ASSETDIR.$fileasset, json_encode($objs, JSON_UNESCAPED_SLASHES));
}

function generate_charlist($filecharlist, $fileasset) {
	$chars = json_decode(file_get_contents(BASEDIR.$filecharlist), true);
	echo "charlist START: ".$filecharlist." -> ".$fileasset."\n";

	$objs = [
		file2asset($filecharlist)
	];
	foreach ($chars as $char) {
		$objs[] = file2asset('image_native/card/image/card_'.$char['id'].'_m.png');
		$objs[] = file2asset('image_native/card/image/card_'.$char['id'].'_l.png');
	}

	echo "charlist DONE: ".count($objs)." files from ".count($chars)." characters\n";
	return file_put_contents(ASSETDIR.$fileasset, json_encode($objs, JSON_UNESCAPED_SLASHES));
}

function implement_asset($assetfile) {
	$assets = json_decode(file_get_contents($assetfile), true);
	echo "implement START: ".$assetfile." -> ".MADODIR."resource/\n";

	$f = 0;
	$c = 0;
	$cnt = count($assets);
	foreach ($assets as $asset) {
		$filepath = MADODIR.'resource/'.$asset['path'];
		$dirname = dirname($filepath);
		if (!file_exists($dirname))
			mkdir($dirname, 0755, true);
		if (file_exists($filepath)) {
			echo "file already exists: ".$filepath."\n";
			continue;
		}
		echo (++$f)."/".$cnt."\r";
		foreach ($asset['file_list'] as $file) {
			$chunk = chunk_read($file['url']);
			$filesize = strlen($chunk);
			if ($filesize != $file['size'])
				echo "size mismatch: ".$file['url']." is ".$filesize.", expected ".$file['size']."\n";
			file_put_contents($filepath, $chunk, FILE_APPEND);
			$c++;
		}
		$md5_file = md5_file($filepath);
		if ($md5_file != $asset['md5'])
			echo "md5 mismatch: ".$asset['path']." is ".$md5_file.", expected ".$asset['md5']."\n";
	}

	echo "implement DONE: ".$c." chunks in ".$f." files\n";
	return true;
}
