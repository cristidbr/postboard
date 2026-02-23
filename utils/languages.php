<?php 

if ( ! defined( 'ABSPATH' ) ) 
{
    exit;
}

include_once( ABSPATH . 'schema/language.php' );


$statement = $pdo->prepare( "SELECT * FROM languages" );
$statement->execute();

$languages = $statement->fetchAll( PDO::FETCH_CLASS, 'PB_Language' );

$language_slug = isset( $_GET[ 'lang' ] ) ? $_GET[ 'lang' ] : null;

if( $language_slug == null )
{
    $language_slug = isset( $_SESSION[ 'lang' ] ) ? $_SESSION[ 'lang' ] : DEFAULT_LANGUAGE_SLUG;
}

$language_slug = preg_replace( '/\s+/u', '', $language_slug );


function language_search( $languages, $slug = null )
{
    foreach( $languages as $entry ) 
    {
        if( $slug != null ) 
        {
            if( $entry->slug == null )
            {
                continue;
            }

            if( strtolower( $entry->slug ) == strtolower( $slug ) )
            {
                return $entry;
            }
        }
    }

    return null;
}

$language = language_search( $languages, $slug = $language_slug );

if( $language == null )
{
    $language = language_search( $languages, DEFAULT_LANGUAGE_SLUG );
}

if( $language == null )
{
    error_log( 'Unable to find default language definition' );
    exit( 'Unable to find default language definition' );
}

// build language ID map
$language_ids = [];
foreach( $languages as $entry )
{
    $language_ids[ $entry->id ] = $entry;
}

// get languages list
$language_names = [];
foreach( $languages as $entry )
{
    // in current language
    if( $entry->lang != $language->lang )
    {
        continue;
    }

    if( $entry->translation == NULL )
    {
        $language_names[ $entry->lang ] = $entry->name;
        continue;
    }
    
    $ref = $language_ids[ $entry->translation ];
    $language_names[ $ref->lang ] = $entry->name;
}

// get language slugs
$language_slugs = [];
foreach( $languages as $entry )
{
    if( $entry->slug == NULL )
    {
        continue;
    }

    $language_slugs[ $entry->lang ] = $entry->slug;
}

// save to session
$_SESSION[ 'lang' ] = $language->slug;

?>
