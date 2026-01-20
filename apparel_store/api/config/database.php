<?php
$conn = mysqli_init();

mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL); 

$success = mysqli_real_connect(
    $conn, 
    getenv('TIDB_HOST'), 
    getenv('TIDB_USER'), 
    getenv('TIDB_PASSWORD'), 
    getenv('TIDB_DATABASE'), 
    4000
);

if (!$success) {
    die("Database Connection Error: " . mysqli_connect_error());
}
?>