<?php
$wednesday = getenv(); // $_ENV may be disabled
foreach (['BASEDIR', 'ORIGDIR', 'LISTDIR', 'ASSETDIR', 'MADODIR', 'CHARLIST', 'SNAASIZE', 'SNAAMULT'] as $wedkey)
	if (isset($wednesday[$wedkey]))
		define($wedkey, $wednesday[$wedkey]);

/*
define( 'BASEDIR', '/SNAA/magica/resource/download/asset/master/resource/');
define( 'ORIGDIR', '/SNAA/magica/resource/download/asset/master/vanilla/');
define( 'IMWBDIR', '/SNAA/magica/resource/image_web/');
define( 'SNAALOG', '/tmp/snaa-tools/logs/');
define( 'LISTDIR', '/tmp/snaa-tools/filelist/');
define('ASSETDIR', '/tmp/snaa-tools/asset/');
define( 'MADODIR', '/tmp/snaa-tools/madomagi/');
define('CHARLIST', 'image_native/scene/download/char_list.json');
define('SNAASIZE', 4194304);
define('SNAAMULT', 2);
*/

function read_json($filepath) {
	return json_decode(file_get_contents($filepath), true);
}

function write_json($filepath, $data, $json_format = 'loose') {
	switch ($json_format) {
		case 'loose':
			$json_flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
			break;
		case 'pretty':
			$json_flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
			break;
		case 'ascii':
			$json_flags = JSON_UNESCAPED_SLASHES;
			break;
		case 'canonical':
			$json_flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
			break;
		default:
			$json_flags = 0;
	}
	$filebody = json_encode($data, $json_flags);
	file_put_contents($filepath, $filebody);
	return strlen($filebody);
}

function rebuild_json($filepath, $json_format = 'loose') {
	$filesize = filesize($filepath);
	$data = read_json($filepath);
	$newsize = write_json($filepath, $data, $json_format);
	return $newsize - $filesize;
}

function read_ugly_file($filepath) {
	return simplexml_load_string(file_get_contents($filepath));
}

function read_ugly_file_in_ugly_way($filepath) {
	return json_decode(json_encode((array)read_ugly_file($filepath)), true);
}

function write_ugly_file($filepath, $data, $xml_format = 'loose') {
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->loadXML($data->asXML());
	$dom->formatOutput = ($xml_format == 'pretty');
	$filebody = $dom->saveXML();
	file_put_contents($filepath, $filebody);
	return strlen($filebody);
}

function rebuild_xml($filepath, $xml_format = 'loose') {
	$filesize = filesize($filepath);
	$data = read_ugly_file($filepath);
	$newsize = write_ugly_file($filepath, $data, $xml_format);
	return $newsize - $filesize;
}

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
		$asset = read_json(ASSETDIR.$assetfile);

		$values = [];
		foreach ($asset as $file)
			$values[] = "('resource/".$file['path']."','".$file['md5']."')";

		$db->query("INSERT INTO download_asset VALUES ".implode(",",$values));
	}
	return true;
}

function copy_asset($origasset, $fileasset) {
	$asset = read_json(ORIGDIR.$origasset);
	echo "copying: ".$origasset." -> ".$fileasset."\n" ;
	return write_json(ASSETDIR.$fileasset, $asset);
}

function reverse_asset($origasset, $filelist) {
	$asset = read_json(ORIGDIR.$origasset);
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
	return write_json(ASSETDIR.$fileasset, $objs);
}

function generate_charlist($filecharlist, $fileasset) {
	$chars = read_json(BASEDIR.$filecharlist);
	echo "charlist START: ".$filecharlist." -> ".$fileasset."\n";

	$objs = [
		file2asset($filecharlist)
	];
	foreach ($chars as $char) {
		$objs[] = file2asset('image_native/card/image/card_'.$char['id'].'_m.png');
		$objs[] = file2asset('image_native/card/image/card_'.$char['id'].'_l.png');
	}

	echo "charlist DONE: ".count($objs)." files from ".count($chars)." characters\n";
	return write_json(ASSETDIR.$fileasset, $objs, 'pretty');
}

function check_asset($assetfile, $dir) {
	$assets = read_json($assetfile);
	echo "check asset START: ".$assetfile." <=> ".$dir."\n";
	if (!is_dir($dir)) {
		echo "CHECK FAILED: ".$dir." is not a directory\n";
		return false;
	}

	$f = 0;
	$allgood = true;
	$totes = ['expected' => 0, 'real' => 0];
	$cnt = count($assets);
	foreach ($assets as $asset) {
		$filepath = $dir.$asset['path'];
		echo (++$f)."/".$cnt."\r";
		$filesize = 0;
		foreach ($asset['file_list'] as $file)
			$filesize += $file['size'];
		$totes['expected'] += $filesize;
		if (!file_exists($filepath)) {
			$allgood = false;
			echo "FILE IS MISSING: ".$filepath."\n";
			continue;
		}
		$real_filesize = filesize($filepath);
		if ($filesize != $real_filesize) {
			$allgood = false;
			echo "LENGHT MISMATCH: ".$file['url']." is ".$filesize.", expected ".$real_filesize."\n";
		}
		$totes['real'] += $real_filesize;
		$md5_file = md5_file($filepath);
		if ($md5_file != $asset['md5']) {
			$allgood = false;
			echo "MD5SUM MISMATCH: ".$asset['path']." is ".$md5_file.", expected ".$asset['md5']."\n";
		}
	}

	echo "check asset DONE: ".$totes['real']."/".$totes['expected']." bytes in ".$f." files\n";
	return $allgood;
}

function implement_asset($assetfile) {
	$assets = read_json($assetfile);
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

function plist_writeframe($filepath, $frame, $file_png) {
	$img = new Imagick($file_png);
	$img->cropImage($frame['width'], $frame['height'], $frame['x'], $frame['y']);
	$img->setImagePage(0, 0, 0, 0);
	$img->writeImage($filepath);
	$img->clear();
	return true;
}

function plist_frame($data) {
	$frame = [];
	foreach (['width', 'height', 'originalWidth', 'originalHeight', 'x', 'y'] as $key) {
		$n = array_search($key, $data['key']);
		if ($n === false)
			return false;
		$frame[$key] = $data['integer'][$n];
	}
	return $frame;
}

function plist_extract($file_png, $file_plist, $file_dir) {
	$plist = read_ugly_file_in_ugly_way($file_plist);
	echo "plist extract START: ".$file_png."\n";

	$fkey = array_search('frames', $plist['dict']['key']);
	if ($fkey === false)
		return false;
	$plist = $plist['dict']['dict'][$fkey];

	foreach ($plist['key'] as $n => $framename) {
		$frame = plist_frame($plist['dict'][$n]);
		if ($frame === false) {
			echo " skipping frame: ".$framename."\n";
			continue;
		}
		echo " extracting frame: ".$framename."\n";
		plist_writeframe($file_dir.$framename, $frame, $file_png);
	}

	echo "plist extract DONE: ".$file_png."\n";
	return true;
}

function optimize_scenario($filelist) {
	$files = explode("\n", file_get_contents(LISTDIR.$filelist));
	$cnt = count($files) - 1;
	echo "optimize scenario START: ".$filelist."\n";

	$i = 0;
	$totes = [-1 => 0, 0 => 0, 1 => 0, 2 => 0];
	foreach ($files as $file) {
		if ($file == '') // empty last line
			continue;
		echo ($i++)."/".$cnt."\r";
		$sizediff = rebuild_json(BASEDIR.$file, 'loose');
		$totes[($sizediff >0)-(0> $sizediff)]++;
		$totes[2] += $sizediff;
	}
	echo "optimize scenario DONE: ".$totes[-1]." decrease, ".$totes[0]." keep, ".$totes[1]." increase, total: ".sprintf("%+d",$totes[2])." bytes\n";
	return true;
}

function optimize_xml($filelist) {
	$files = explode("\n", file_get_contents(LISTDIR.$filelist));
	$cnt = count($files) - 1;
	echo "optimize xml START: ".$filelist."\n";

	$i = 0;
	$totes = [-1 => 0, 0 => 0, 1 => 0, 2 => 0];
	foreach ($files as $file) {
		if ($file == '') // empty last line
			continue;
		echo ($i++)."/".$cnt."\r";
		$sizediff = rebuild_xml(BASEDIR.$file, 'loose');
		$totes[($sizediff >0)-(0> $sizediff)]++;
		$totes[2] += $sizediff;
	}
	echo "optimize xml DONE: ".$totes[-1]." decrease, ".$totes[0]." keep, ".$totes[1]." increase, total: ".sprintf("%+d",$totes[2])." bytes\n";
	return true;
}
