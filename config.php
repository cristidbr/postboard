<?php 

if ( ! defined( 'ABSPATH' ) ) 
{
    exit;
}

session_start();

ini_set( 'display_errors', '1' );
ini_set( 'display_startup_errors', '1' );
error_reporting( E_ALL );

define( 'DEFAULT_LANGUAGE_SLUG', 'en' );

// TODO: Complete with production details
define( 'DB_HOST', 'mysql' );
define( 'DB_NAME', '' );
define( 'DB_USERNAME', '' );
define( 'DB_PASSWORD', '' );


$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME .';charset=utf8mb4';

$options = [
    PDO::ATTR_EMULATE_PREPARES   => false, // Disable emulation mode for "real" prepared statements
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Disable errors in the form of exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Make the default fetch be an associative array
];

try {
    $pdo = new PDO( $dsn, DB_USERNAME, DB_PASSWORD, $options );
} 
catch ( Exception $e ) 
{
    error_log( $e->getMessage() );
    exit( 'Unable to connect' . $e->getMessage() ); 
}

?>
