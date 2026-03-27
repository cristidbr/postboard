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

// fetch entries descending
$entries_start = 0;
$entries_count = 100;

$statement = $pdo->prepare( "SELECT * FROM entries WHERE lang = :lang AND draft = 0 AND cleanup = 0 ORDER BY id DESC LIMIT :entries_start, :entries_count " );
$statement->bindValue( ':lang', $language->lang, PDO::PARAM_STR ); 
$statement->bindValue( ':entries_start', (int) $entries_start, PDO::PARAM_INT ); 
$statement->bindValue( ':entries_count', (int) $entries_count, PDO::PARAM_INT ); 
$statement->execute();

$entries = $statement->fetchAll( PDO::FETCH_CLASS, 'PB_Entry' );


require_once( 'templates/head.php' );

?>

    <title>PostBoard</title>

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

                <?php
                
                    foreach( $entries as $entry ) 
                    {
                        ?>
                        <h2 class="pt-serif-regular hidden">Entry <span style="font-weight: normal;"><?php echo $entry->skey; ?></span></h2>
                        <?php

                        render_entry( $entry, [], true );
                    }
                ?>
            </div>
        </div>
    </main>

    <?php require_once( 'templates/footer.php' ); ?>

    <script type="text/javascript" src="/content/js/main.js" ></script>
</html>
