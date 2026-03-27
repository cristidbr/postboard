<?php 

if ( ! defined( 'ABSPATH' ) ) 
{
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once( ABSPATH . 'config.php' );
require_once( ABSPATH . 'utils/languages.php' );
include_once( ABSPATH . 'request/headers.php' );
include_once( ABSPATH . 'schema/entry.php' );
include_once( ABSPATH . 'templates/entry.php' );

if ( ! isset( $_GET[ 'skey' ] ) )
{
    echo 'No SKEY present';
    return ;
}

// get entry
$skey = $_GET[ 'skey' ];

$statement = $pdo->prepare( 'SELECT * FROM entries WHERE skey = :skey AND cleanup = 0' );
$statement->bindParam( ':skey', $skey );
$statement->execute();

$entries = $statement->fetchAll( PDO::FETCH_CLASS, 'PB_Entry' );

if( count( $entries ) == 0 )
{
    echo 'Not found';
    return ;
}

$main_entry = $entries[ 0 ];

$linked_translations = [];

// get linked translation
if( $main_entry->translation != NULL )
{
    $statement = $pdo->prepare( "SELECT * FROM entries WHERE id = :id AND cleanup = 0" );
    $statement->bindParam( ':id', $main_entry->translation );
    $statement->execute();

    $linked_translations = array_merge( $linked_translations, $statement->fetchAll( PDO::FETCH_CLASS, 'PB_Entry' ) );

    // get other translations
    $statement = $pdo->prepare( "SELECT * FROM entries WHERE translation = :translation AND id != :id AND cleanup = 0" );
    $statement->bindParam( ':translation', $main_entry->translation );
    $statement->bindParam( ':id', $main_entry->id );
    $statement->execute();

    $linked_translations = array_merge( $linked_translations, $statement->fetchAll( PDO::FETCH_CLASS, 'PB_Entry' ) );
}

if( $main_entry->translation == NULL ) 
{
    // current skey is main, get its translations
    $statement = $pdo->prepare( "SELECT * FROM entries WHERE translation = :translation AND cleanup = 0" );
    $statement->bindParam( ':translation', $main_entry->id );
    $statement->execute();

    $linked_translations = array_merge( $linked_translations, $statement->fetchAll( PDO::FETCH_CLASS, 'PB_Entry' ) );
}

// filter main
$main_translations = [];
foreach( $linked_translations as $entry )
{
    if( $entry->id == $main_entry->id )
    {
        continue;
    }

    $main_translations[] = $entry;
}

?>

<html>

<?php

require_once( 'templates/head.php' );

?>

    <title>Entry <?php echo $skey; ?></title>

</head>
<body>

    <main class="container">
        <div id="root">
            <div>
                <nav class="navtop">
                    <h1 class="pt-serif-bold">
                        <a href="/">PostBoard</a>
                    </h1>

                    <h3 class="pt-serif-regular">
                        <a class="navitem" href="create.php">Create</a>
                    </h3>
                </nav>
                
                <h2 class="pt-serif-regular">Entry <span style="font-weight: normal;"><?php echo $skey; ?></span></h2>
                
                <?php render_entry( $main_entry, $main_translations ); ?>
            </div>
        </div>
    </main>

    <?php require_once( 'templates/footer.php' ); ?>

    <script type="text/javascript" src="/content/js/main.js"></script>
</body>
</html>
