<?php
$url = !empty($_GET['url']) ? $_GET['url'] : $_SERVER['HTTP_HOST'];
$path = !empty($_GET['path']) ? $_GET['path'] : '';
if(!empty($url) && !empty($path)){
	$rootPath = $_SERVER['DOCUMENT_ROOT'];

	$splitPath = explode('/w3-cache/',$path);
	
	$cachePath = $rootPath.$splitPath[0].'/w3-cache/';
	preg_match('/(.*)\/w3-cache\/(css|js)\/(\d*)(.*)[mob]*\.(css|js)/', $path, $match);
	$key = w3ScanDir($cachePath.$match[2]);
	$newPath = $cachePath.$match[2].'/'.$key.$match[4].'.'.$match[5];
	if(file_exists($newPath)){
		header("HTTP/1.1 200 OK");
		// @codingStandardsIgnoreLine
		echo file_get_contents($newPath);
		exit;
	}elseif(file_exists($rootPath.$match[4].'.'.$match[5])){
		header("HTTP/1.1 200 OK");
		// @codingStandardsIgnoreLine
		echo file_get_contents($rootPath.$match[4].'.'.$match[5]);
		exit;
	}
	exit;
}

function w3ScanDir($dir){
	$max = 0;
	if (is_dir($dir)) {
		$objects = @scandir($dir);
		if (is_array($objects) && count($objects) > 1) {
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir . "/" . $object) == "dir") {
						$max = filemtime($dir . "/" . $object) > $max ? filemtime($dir . "/" . $object) : $max;
					} else {
						
					}
				}
			}
		}
		return $object;
	}
	return false;
}