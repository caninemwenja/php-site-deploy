<?php

include('update_settings.inc.php');
 
// upload code when pushed
 
$log = fopen($_LOG_FILE,"a+");
 
function get_data($url) {
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
  $data = get_data($source_url);
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
 
// check if there is data in POST body
 
$payload = @file_get_contents($_PAYLOAD_SOURCE);
 
// remove payload meta
 
$eq_pos = strpos($payload, '=');
 
if($eq_pos === false){
	throw new Exception('Invalid payload');
}
 
// implement logging
 
$actual_payload = urldecode(substr($payload, $eq_pos+1));
 
fwrite($log,"JSON downloaded: {$actual_payload}");
 
// get the json
 
$json = json_decode($actual_payload);
 
$branches = array();
 
// for each branch affected
 
foreach($json->commits as $commit){
	if(in_array($commit->branch, $branches) === false){
	    	// get branch
		$branch = $commit->branch;
		$branches[] = $branch;
		
		// get commit no
		$commit_no = $commit->node;
		
		// download branch code
		$branch_url = sprintf($_BRANCH_URL, $branch);
		$local_file_name = sprintf($_LOCAL_FILE, $branch);
		
		if(remote_copy($branch_url, $local_file_name) === false){
			fwrite($log, "Failed to write, check permissions");
			break;
	        }
	        
        	fwrite($log, "Done copying to local");
	        
	        // extract code
	        $local_folder = sprintf($_LOCAL_FOLDER, $branch, $commit_no);
	        extract_zip($local_file_name, $local_folder);
	        
	        fwrite($log, "Extracted code");
	        
	        // write latest commit no to branch file
	        $branch_file = fopen(sprintf($_BRANCH_FILE, $branch),"w");
	        fwrite($branch_file, $commit_no);
	        fclose($branch_file);     
	}
}
 
fclose($log);
 
?>
