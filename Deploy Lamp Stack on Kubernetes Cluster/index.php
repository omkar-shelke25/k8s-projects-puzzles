<?php
// Get database credentials from environment variables
$dbname = getenv('MYSQL_DATABASE');  // Database name
$dbuser = getenv('MYSQL_USER');      // Database user
$dbpass = getenv('MYSQL_PASSWORD');  // Database password
$dbhost = getenv('MYSQL_HOST');      // Database host

// Attempt to connect to the database
$connect = mysqli_connect($dbhost, $dbuser, $dbpass) or die("Unable to Connect to '$dbhost'");

// Check if the connection was successful
if ($connect->connect_error) {
   die("Connection failed: " . $connect->connect_error);
}
echo "Connected successfully\n";

// Test query to check if the database exists and list tables
$test_query = "SHOW TABLES FROM $dbname";
$result = mysqli_query($connect, $test_query);

if (!$result) {
   die("Error executing query: " . mysqli_error($connect));
}

// Output the tables in the database
echo "Tables in database '$dbname':\n";
while ($row = mysqli_fetch_array($result)) {
   echo $row[0] . "\n";
}

// Close the connection
mysqli_close($connect);
?>
