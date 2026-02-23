<?php 

if ( ! defined( 'ABSPATH' ) ) 
{
    exit;
}

?>
<footer class="pt-serif-regular">
    <div class="languages">
    <?php

    foreach( array_keys( $language_slugs ) as $key )
    {
        if( !isset( $language_names[ $key ] ) || !isset( $language_slugs[ $key ] ) )
        {
            continue;
        }

        $item_name = $language_names[ $key ];
        $item_slug = $language_slugs[ $key ];

        ?>
        <a href="/?lang=<?php echo $item_slug; ?>"><?php echo $item_name; ?></a>
        <?php
    }

    ?>
    </div>
<?php

?>
</footer>
