<?php
declare(strict_types = 1);
$wednesday = getenv(); // $_ENV may be disabled
foreach (['DOFILES', 'BASEDIR', 'ORIGDIR', 'LISTDIR', 'ASSETDIR', 'MADODIR', 'CHARLIST', 'SNAASIZE', 'SNAAMULT'] as $wedkey)
	if (isset($wednesday[$wedkey]))
		define($wedkey, $wednesday[$wedkey]);

/*
define( 'DOFILES', false);
define( 'BASEDIR', '/SNAA/magica/resource/download/asset/master/resource/');
define( 'ORIGDIR', '/SNAA/magica/resource/download/asset/master/vanilla/');
define( 'IMWBDIR', '/SNAA/magica/resource/image_web/');
define( 'SNAALOG', '/tmp/snaa-tools/logs/');
define( 'LISTDIR', '/tmp/snaa-tools/filelist/');
define('ASSETDIR', '/tmp/snaa-tools/asset/');
define( 'MADODIR', '/tmp/snaa-tools/madomagi/');
define('CHARLIST', 'image_native/scene/download/char_list.json');
define('IROEPOCH', '2017-03-17T10:39:13Z');
define('SNAASIZE', 4194304);
define('SNAAMULT', 2);
*/

class Snaa {
	private function touch_iroepoch($filepath) {
		return is_file($filepath) ? touch($filepath, strtotime(IROEPOCH)) : false;
	}

	private function read_list($filepath) {
		return is_file($filepath) ? file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : false;
	}

	private function write_file($filepath, $filebody, $flags = 0) {
		return (defined('DOFILES') && DOFILES) ? file_put_contents($filepath, $filebody, $flags) : false;
	}

	private function read_json($filepath) {
		return is_file($filepath) ? json_decode(file_get_contents($filepath), true) : false;
	}

	private function write_json($filepath, $data, $json_format = 'loose') {
		$json_flags = match ($json_format) {
			'loose'     => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
			'pretty'    => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT,
			'ascii'     => JSON_UNESCAPED_SLASHES,
			'canonical' => JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
			default     => 0
		};
		$filebody = json_encode($data, $json_flags);
		$this->write_file($filepath, $filebody);
		return strlen($filebody);
	}

	private function rebuild_json($filepath, $json_format = 'loose') {
		$filesize = filesize($filepath);
		$data = $this->read_json($filepath);
		$newsize = $this->write_json($filepath, $data, $json_format);
		return $newsize - $filesize;
	}

	private function read_ugly_file($filepath) {
		return is_file($filepath) ? simplexml_load_string(file_get_contents($filepath)) : false;
	}

	private function read_ugly_file_in_ugly_way($filepath) {
		return json_decode(json_encode((array)($this->read_ugly_file($filepath))), true);
	}

	private function write_ugly_file($filepath, $data, $xml_format = 'loose') {
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($data->asXML());
		$dom->formatOutput = ($xml_format == 'pretty');
		$filebody = $dom->saveXML();
		$this->write_file($filepath, $filebody);
		return strlen($filebody);
	}

	private function rebuild_xml($filepath, $xml_format = 'loose') {
		$filesize = filesize($filepath);
		$data = $this->read_ugly_file($filepath);
		$newsize = $this->write_ugly_file($filepath, $data, $xml_format);
		return $newsize - $filesize;
	}

	private function chunk_encode($f, $b, $e, $s = null, $n = null) {
		return 'CHUNK'.($n === null ? '' : '.'.$this->split_prefix($n)).'='.$f.','.$b.'-'.($e - 1).'#';
	}

	private function chunk_read($str) {
		return preg_match('/^CHUNK[^=]*=([^,]*),([0-9]*)-([0-9]*)#$/', $str, $m)
			 ? file_get_contents(filename: BASEDIR.$m[1], offset: $m[2], maxlen: $m[3] - $m[2] + 1)
			 : file_get_contents(BASEDIR.$str);
	}

	private function calc_etag($filepath, $method = 's3') {
		switch ($method) {
			case 's3':
				$fullpath = ASSETDIR.$filepath.'.xz';
				return md5_file($fullpath);
			case 'nginx':
				$fullpath = ASSETDIR.$filepath; // change to final path to ensure mtime
				return sprintf('%x-%x', filemtime($fullpath), filesize($fullpath));
			default:
				return 'snaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
		}
	}

	private function asset_localpath($file) {
		return str_replace(['movie/char/high/', 'movie/char/low/'], ['movie/char/', 'movie/char/'], $file);
	}

	private function is_ugly_chunk($file) { // split --bytes=1048576 --suffix-length=3
		return preg_match('/\.a[a-z][a-z]$/', $file);
	}

	private function split_prefix($num, $len = 3) {
		$z = count($a = range('a', 'z'));
		for ($res = ''; $len --> 0; $res .= $a[intdiv($num, pow($z, $len)) % $z]);
		return $res;
	}


	public function split_file($filepath, $outpath, $chunksize = 1048576, $delete = false) {
		$filesize = filesize($filepath);
		if ($filesize <= $chunksize)
			return false;

		$md5_file = md5_file($filepath);
		$res = [
			'file' => $filepath,
			'size' => $filesize,
			'md5' => $md5_file,
			'chunks' => []
		];

		for ($offset = 0; $offset < $filesize; $offset += $chunksize) {
			//$chunk = file_get_contents(filename: $filepath, offset: $offset, maxlen: $chunksize); // unknown maxlen in 8.0.1 WTF
			$chunk = file_get_contents($filepath, false, null, $offset, $chunksize); // $use_include_path is bool now
			$md5chunk = md5($chunk);
			$chunkpath = $outpath.'.'.$this->split_prefix(intdiv($offset, $chunksize));
			$realchunksize = strlen($chunk);
			$this->write_file($chunkpath, $chunk);
			$res['chunks'][] = [
				'file' => $chunkpath,
				'size' => $realchunksize,
				'md5' => $md5chunk
			];
		}
		if ($delete)
			unlink($filepath);

		return $res;
	}

	public function madomagi_C5XyOsaM() {
		$C5XyOsaM = [
			'C5XyOsaM' => '8c88d9d9d8888f8990dcdedfd89089d9dc8a90858c85df908e8a8cdcd98b888f8588df8f'
		];
		return $this->write_json(MADODIR.'C5XyOsaM.json', $C5XyOsaM);
	}

	public function madomagi_db($dbfile, $quality = 'high', $voices = true) {
		$dbpath = MADODIR.$dbfile;
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

		if (is_file($dbpath))
			unlink($dbpath);
		$db = new SQLite3($dbpath);
		$db->query("CREATE TABLE download_asset(path char(128) primary key,md5 char(128))");
		$db->query("CREATE TABLE asset_json(file char(128) primary key,etag char(128))");

		foreach (array_merge($metaassetfiles, $assetfiles) as $assetfile) {
			echo "making ".$dbfile."/".$assetfile."\n";
			copy(ASSETDIR.$assetfile, MADODIR.$assetfile);
			$db->query("INSERT INTO asset_json VALUES('".$assetfile."','\"".$this->calc_etag($assetfile)."\"')");
			if (in_array($assetfile, $metaassetfiles))
				continue;
			$db->query("INSERT INTO download_asset VALUES ".implode(",", array_map(function($file) { return "('resource/".$file['path']."','".$file['md5']."')"; }, $this->read_json(ASSETDIR.$assetfile))));
		}
		return filesize($dbpath);
	}

	public function copy_asset($origasset, $fileasset) {
		$asset = $this->read_json(ORIGDIR.$origasset);
		echo "copying: ".$origasset." -> ".$fileasset."\n" ;
		return $this->write_json(ASSETDIR.$fileasset, $asset);
	}

	public function reverse_asset($origasset, $filelist) {
		$asset = $this->read_json(ORIGDIR.$origasset);
		$cnt = count($asset);
		echo "reverse START: ".$origasset." -> ".$filelist."\n";

		$files = '';
		$i = $s = 0;
		foreach ($asset as $file) {
			echo ($i++)."/".$cnt."\r";
			if ($this->is_ugly_chunk($file['path']))
				$s++;
			else
				$files .= $file['path']."\n";
		}

		echo "reverse DONE: ".$s." skips in ".$cnt." files\n";
		return $this->write_file(LISTDIR.$filelist, $files);
	}

	public function file2asset($file) {
		$filepath = BASEDIR.$file;
		$filesize = filesize($filepath);
		$md5_file = md5_file($filepath);
		$userfile = $this->asset_localpath($file);

		$file_list = [];
		if (defined('SNAASIZE') && defined('SNAAMULT') && $filesize > SNAASIZE * SNAAMULT) {
			for ($chunk_b = 0; $filesize > 0; $filesize -= SNAASIZE) {
				$chunk_s = min($filesize, (int)SNAASIZE);
				$chunk_e = $chunk_b + $chunk_s;
				$file_list[] = [
					'size' => $chunk_s,
					'url' => $this->chunk_encode($file, $chunk_b, $chunk_e, $chunk_s, intdiv($chunk_b, (int)SNAASIZE))
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

	public function generate_asset($filelist, $fileasset) {
		$files = $this->read_list(LISTDIR.$filelist);
		$cnt = count($files);
		echo "generate START: ".$filelist." -> ".$fileasset."\n";

		$objs = [];
		$i = 0;
		foreach ($files as $file) {
			echo ($i++)."/".$cnt."\r";
			$objs[] = $this->file2asset($file);
		}

		echo "generate DONE: ".$cnt." files\n";
		return $this->write_json(ASSETDIR.$fileasset, $objs);
	}

	public function generate_charlist($filecharlist, $fileasset) {
		$chars = $this->read_json(BASEDIR.$filecharlist);
		echo "charlist START: ".$filecharlist." -> ".$fileasset."\n";

		$objs = [$filecharlist];
		foreach ($chars as $char) {
			$objs[] = 'image_native/card/image/card_'.$char['id'].'_m.png';
			$objs[] = 'image_native/card/image/card_'.$char['id'].'_l.png';
		}
		$objs = array_map([$this, 'file2asset'], $objs);

		echo "charlist DONE: ".count($objs)." files from ".count($chars)." characters\n";
		return $this->write_json(ASSETDIR.$fileasset, $objs, 'pretty');
	}

	public function check_asset($assetfile, $dir = null) {
		$assets = $this->read_json($assetfile);
		$dir = $dir ?? MADODIR.'resource/';
		echo "check asset START: ".$assetfile." <=> ".$dir."\n";
		if (!is_dir($dir)) {
			echo "CHECK FAILED: ".$dir." is not a directory\n";
			return false;
		}

		$allgood = true;
		$f = 0;
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
				echo "FILE IS MISSING: ".$filepath."\n";
				$allgood = false;
				continue;
			}
			;
			if (($real_filesize = filesize($filepath)) != $filesize) {
				echo "LENGHT MISMATCH: ".$file['url']." is ".$filesize.", expected ".$real_filesize."\n";
				$allgood = false;
			}
			$totes['real'] += $real_filesize;
			if (($md5_file = md5_file($filepath)) != $asset['md5']) {
				echo "MD5SUM MISMATCH: ".$asset['path']." is ".$md5_file.", expected ".$asset['md5']."\n";
				$allgood = false;
			}
		}

		echo "check asset DONE: ".$totes['real']."/".$totes['expected']." bytes in ".$f." files\n";
		return $allgood;
	}

	public function implement_asset($assetfile, $dir = null) {
		$assets = $this->read_json($assetfile);
		$dir = $dir ?? MADODIR.'resource/';
		echo "implement START: ".$assetfile." -> ".$dir."\n";

		$allgood = true;
		$f = $c = 0;
		$cnt = count($assets);
		foreach ($assets as $asset) {
			$filepath = $dir.$asset['path'];
			$dirname = dirname($filepath);
			if (!file_exists($dirname))
				mkdir($dirname, 0755, true);
			if (file_exists($filepath)) {
				echo "file already exists: ".$filepath."\n";
				$allgood = false;
				continue;
			}
			echo (++$f)."/".$cnt."\r";
			foreach ($asset['file_list'] as $file) {
				$chunk = $this->chunk_read($file['url']);
				$filesize = strlen($chunk);
				if ($filesize != $file['size']) {
					echo "size mismatch: ".$file['url']." is ".$filesize.", expected ".$file['size']."\n";
					$allgood = false;
				}
				$this->write_file($filepath, $chunk, FILE_APPEND);
				$c++;
			}
			if (($md5_file = md5_file($filepath)) != $asset['md5']) {
				echo "md5 mismatch: ".$asset['path']." is ".$md5_file.", expected ".$asset['md5']."\n";
				$allgood = false;
			}
		}

		echo "implement DONE: ".$c." chunks in ".$f." files\n";
		return $allgood;
	}

	public function plist_writeframe($filepath, $frame, $file_png) {
		if ($frame['x'] === false || $frame['y'] === false || $frame['width'] === false || $frame['height'] === false) {
			echo " bad frame: ".$filepath."\n";
			return false;
		}
		$img = new Imagick($file_png);
		$img->cropImage($frame['width'], $frame['height'], $frame['x'], $frame['y']);
		$img->setImagePage(0, 0, 0, 0);
		$img->writeImage($filepath);
		$img->clear();
		return true;
	}

	public function plist_frame($data) {
		if (($n = array_search('frame', $data['key'])) !== false) // offset, rotated, sourceSize
			return preg_match('/{{(?<x>[0-9]*),(?<y>[0-9]*)},{(?<width>[0-9]*),(?<height>[0-9]*)}}/', $data['string'][$n], $m) ? $m : false;
		$keys = ['x', 'y', 'width', 'height']; // originalWidth, originalHeight
		return array_combine($keys, array_map(function($key) use ($data) { return ($n = array_search($key, $data['key'])) === false ? false : $data['integer'][$n]; }, $keys));
	}

	function plist_extract($file_png, $file_plist, $file_dir) {
		$plist = $this->read_ugly_file_in_ugly_way($file_plist);
		echo "plist extract START: ".$file_png."\n";

		if (($fkey = array_search('frames', $plist['dict']['key'])) === false)
			return false;
		$plist = $plist['dict']['dict'][$fkey];

		foreach ($plist['key'] as $n => $framename) {
			if (($frame = $this->plist_frame($plist['dict'][$n])) === false) {
				echo " skipping frame: ".$framename."\n";
				continue;
			}
			echo " extracting frame: ".$framename."\n";
			$this->plist_writeframe($file_dir.$framename, $frame, $file_png);
		}

		echo "plist extract DONE: ".$file_png."\n";
		return true;
	}

	public function optimize_json($filelist) {
		$files = $this->read_list(LISTDIR.$filelist);
		$cnt = count($files);
		echo "optimize json START: ".$filelist."\n";

		$i = 0;
		$totes = [-1 => 0, 0 => 0, 1 => 0, 2 => 0];
		foreach ($files as $file) {
			echo ($i++)."/".$cnt."\r";
			$sizediff = $this->rebuild_json(BASEDIR.$file, 'loose');
			$totes[$sizediff <=> 0]++;
			$totes[2] += $sizediff;
		}
		echo "optimize json DONE: ".$totes[-1]." decrease, ".$totes[0]." keep, ".$totes[1]." increase, total: ".sprintf("%+d",$totes[2])." bytes\n";
		return true;
	}

	public function optimize_xml($filelist) {
		$files = $this->read_list(LISTDIR.$filelist);
		$cnt = count($files);
		echo "optimize xml START: ".$filelist."\n";

		$i = 0;
		$totes = [-1 => 0, 0 => 0, 1 => 0, 2 => 0];
		foreach ($files as $file) {
			echo ($i++)."/".$cnt."\r";
			$sizediff = $this->rebuild_xml(BASEDIR.$file, 'loose');
			$totes[$sizediff <=> 0]++;
			$totes[2] += $sizediff;
		}
		echo "optimize xml DONE: ".$totes[-1]." decrease, ".$totes[0]." keep, ".$totes[1]." increase, total: ".sprintf("%+d",$totes[2])." bytes\n";
		return true;
	}
}
