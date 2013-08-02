<?php

function url_read($url) {
	$request = curl_init();
	$timeout = 5;
	curl_setopt($request, CURLOPT_URL, $url);
	curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($request, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($request);
	curl_close($request);
	return $data;
}
 
function remote_copy($source_url, $dest_url){
	$data = url_read($source_url);
	$res = file_put_contents($dest_url, $data, LOCK_EX);
	return $res;
}
 
function extract_zip($archive, $dest){
    $zip = new ZipArchive;
    $zip->open($archive);
    $zip->extractTo($dest);
    $zip->close();
}
 
function extract_targz($archive, $dest){    
    $p = new PharData($archive);
    
    // check if a decompressed version exists
    $path = substr($archive, 0, strpos($archive, ".tar")).".tar";
    unlink($path);
    
    $p2 = $p->decompress();
    
    unset($p);
    
    $p2->extractTo($dest, null, true);
    unset($p2);
}