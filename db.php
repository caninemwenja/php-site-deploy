<?php

// Example script that updates database schema and settings

// example 
$db_file = $target."Path of SQL schema file relative to the uploaded code folder";

echo "<hr/>";
echo "Updating Schema";
echo "<br/>";

echo "DB File: {$db_file}";
echo "<br/>";

// connect to the database
$conn = new mysqli("database host", "database name", "database username", "database password");

echo "Connected to DB";
echo "<br/>";

// read the SQL statements
$sql = @file_get_contents($db_file);

echo "SQL: {$sql}";
echo "<br/>";

// execute the SQL statements
$res = $conn->multi_query($sql);

if($res){
	echo "Schema updated";
	echo "<br/>";
}
else{
	echo "Schema update failed";
	echo "<br/>";
}

$conn->close();

echo "Connection close";
echo "<br/>"; 

// example of updating the code's database settings on the host server

echo "Updating db settings";
echo "<br/>";

// get the path to the database settings file in the codebase
$db_config_file = $target."database settings file";

echo "Config file: ${db_config_file}";
echo "<br/>";

// example database settings to replace the ones in the code
$new_db_settings = "<?php\n\ndefine('DB_HOST','database host');\ndefine('DB_NAME','database name');\ndefine('DB_USER','database user');\ndefine('DB_PASS','database password');\n";

echo "New DB Settings: {$new_db_settings}";
echo "<br/>";

echo "Writing settings to file";
echo "<br/>";

// replace the codebase settings with the host settings
$settings_file = fopen($db_config_file,"w");
$res = fwrite($settings_file, $new_db_settings);
if($res){
	echo "File written";
	echo "<br/>";
	
	echo "Settings updated";
	echo "<br/>";
	
}
else{
	echo "Writing failed";
	echo "<br/>";
	
	echo "Settings were not updated";
	echo "<br/>";
}

fclose($settings_file);
echo "File closed";
echo "<hr/>";