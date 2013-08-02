<?php

include('choose_settings.inc.php');
 
// choose which branch to view
 
if(!isset($_GET['branch'])){
    throw new Exception("No branch provided");
}
 
$branch = $_GET['branch'];

echo "Changing to branch: {$branch}";
echo "<br/>";
 
$filename = sprintf($_BRANCH_FILE, $branch);
 
if(!file_exists($filename)){
    throw new Exception("Branch hasn't been deployed");
}

echo "Branch file found";
echo "<br/>";
 
$head = file_get_contents($filename);

echo "Working on commit no: {$head}";
echo "<br/>";
 
$target = sprintf($_LINK_TARGET, $branch, $head, $head);

// run extra scripts just in case

foreach($_SCRIPTS as $script){
	require($script);
}

$link = $_LINK;
 
@unlink($link);

echo "Removed previous link";
echo "<br/>";
 
echo "Creating Link: {$link} to Target: {$target}";
echo "<br/>";

symlink($target, $link);
 
echo "Created Link";
 
echo "<br/>";
