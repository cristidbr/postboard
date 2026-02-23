<?php 

if ( ! defined( 'ABSPATH' ) ) 
{
    exit;
}


function render_entry( $entry, $translations = [], $ref = false )
{
    global $language;
    global $language_names;

    $entry_date_modified = DateTime::createFromFormat( 'Y-m-d H:i:s', $entry->date_modified, new DateTimeZone( 'UTC' ) );
    $entry_date_modified = $entry_date_modified->format( DateTime::ATOM );

    $entry_content = htmlspecialchars( $entry->content, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' );
    $entry_sharehref = 'entry.php?skey=' . $entry->skey;

    $entry_credit = htmlspecialchars( $entry->credit, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' );
    $entry_credit_display = ( strlen( $entry_credit ) == 0 ) ? 'hidden' : '';

    $original_skey = $entry->skey;
    $original_skey_index = $entry->id;

    foreach( $translations as $translation )
    {
        if( $translation->id < $original_skey_index )
        {
            $original_skey_index = $translation->id;
            $original_skey =  $translation->skey;
        }
    }

    $translated_variant = [];
    foreach( array_keys( $language_names ) as $key )
    {
        $translated_variant[ $key ] = ( $key == $entry->lang );
    }

    foreach( $translations as $translation )
    {
        $translated_variant[ $translation->lang ] = true;
    }

    $remaining_translations = [];
    foreach( array_keys( $translated_variant ) as $key )
    {
        if( !$translated_variant[ $key ] )
        {
            $remaining_translations[] = $key;
        }
    }

    ?>
    <div class="status_entry pt-serif-regular">
        <div class="group_main">
            <div class="group_content">
                <div class="status_listing">
                    <div class="status_item <?php echo ( count( $translations ) == 0 ) ? 'status_item_br' : ''; ?>">
                        <div class="status_body  ">
                            <h6 class="hidden">English</h6>

                            <?php 
                                if( $ref ) {
                                    ?><a href="<?php echo 'entry.php?skey=' . $entry->skey; ?>" style="text-decoration: none;"><?php
                                } 
                            ?>
                                <h3 class="entry_text">
                                    <pre class="content" ><?php echo $entry_content; ?></pre>
                                </h3>
                            <?php 
                                if( $ref ) {
                                    ?></a><?php
                                }
                            ?>
                        </div>
                        <div itemprop="creator" itemscope="" itemtype="https://schema.org/Person">
                            <meta itemprop="name" content="...">
                        </div>

                        <meta itemprop="creditText" content="<?php echo $entry_credit; ?>">

                        <div itemProp="inLanguage" itemScope itemType="https://schema.org/Language">
                            <meta itemProp="name" content="<?php echo $language->lang; ?>" />
                            <meta itemProp="alternateName" content="<?php echo $language->name; ?>" />
                        </div>
                        <?php echo '<script type="application/ld+json">'; ?>
                        {
                            "@context": "https://schema.org/",
                            "@type": "Quotation",
                            "text": <?php echo json_encode( $entry_content ); ?>,
                            "inLanguage": {
                                "@type": "Language",
                                "name": <?php echo json_encode( $language->lang ); ?>,
                                "alternateName": <?php echo json_encode( $language->name ); ?>
                            },
                            "creditText": <?php echo json_encode( $entry_credit ); ?>
                        }
                        <?php echo '</script>' ?>
                    </div>

                    <?php 
                    for( $index = 0; $index < count( $translations ); $index ++ )
                    {
                        $translation = $translations[ $index ];

                        $translation_content = htmlspecialchars( $translation->content, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' );
                        $translation_credit = htmlspecialchars( $translation->credit, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' );
                        $translation_lang_name = NULL;

                        if( isset( $language_names[ $translation->lang ] ) )
                        {
                            $translation_lang_name = $language_names[ $translation->lang ];
                        }

                        ?>
                        <div class="status_item <?php echo ( count( $translations ) == $index + 1 ) ? 'status_item_br' : ''; ?>">
                            <div class="status_body">
                                <h6 class="status_lang <?php echo ( $translation_lang_name == NULL ) ? 'hidden' : ''; ?>"
                                ><?php echo $translation_lang_name; ?></h6>

                                <h3 class="entry_text">
                                    <pre class="content" ><?php echo $translation_content; ?></pre>
                                </h3>
                            </div>
                            <div itemprop="creator" itemscope="" itemtype="https://schema.org/Person">
                                <meta itemprop="name" content="...">
                            </div>

                            <meta itemprop="creditText" content="<?php echo $translation_credit; ?>">

                            <div itemProp="inLanguage" itemScope itemType="https://schema.org/Language">
                                <meta itemProp="name" content="<?php echo $translation->lang; ?>" />
                                <meta itemProp="alternateName" content="<?php echo $language->name; ?>" />
                            </div>

                            <?php echo '<script type="application/ld+json">'; ?>
                            {
                                "@context": "https://schema.org/",
                                "@type": "Quotation",
                                "text": <?php echo json_encode( $translation_content ); ?>,
                                "inLanguage": {
                                    "@type": "Language",
                                    "name": <?php echo json_encode( $translation->lang ); ?>,
                                    "alternateName": <?php echo json_encode( $language->name ); ?>
                                },
                                "creditText": <?php echo json_encode( $translation_credit ); ?>
                            }
                            <?php echo '</script>' ?>
                        </div>
                        <?php
                    }
                    ?>

                    <?php 
                    if( !$ref ) {
                    ?>
                    
                    <div class="status_item status_item_translate">
                        <form name="translate1" method="post" action="translate.php">
                            <input name="cs8" type="hidden" value="&#x2713;" />

                            <div class="status_body">
                                <div class="translate_selector">
                                    <div class="translate_selector_group">
                                        <input type="hidden" name="from" value="<?php echo $entry->skey; ?>" >
                                        <input type="hidden" name="original" value="<?php echo $original_skey; ?>" >

                                        <?php if( count( $remaining_translations ) > 1 ) { ?>
                                            <select class="translate_options" name="lang">
                                                <?php 
                                                foreach( $remaining_translations as $rtranslation ) 
                                                {
                                                    ?>
                                                    <option value="<?php echo $rtranslation; ?>">
                                                        <?php echo $language_names[ $rtranslation ]; ?>
                                                    </option>
                                                    <?php 
                                                } 
                                                ?>
                                            </select>
                                        <?php } else if( count( $remaining_translations ) == 1 ) { ?>
                                            <h6 class="status_lang"><?php echo $language_names[ $remaining_translations[ 0 ] ]; ?></h6>
                                            <input type="hidden" name="lang" value="<?php echo $remaining_translations[ 0 ]; ?>" >
                                        <?php } ?>
                                        
                                        <h3 class="entry_text ">
                                            <pre class="content content-pre" contenteditable oninput="handleContentEditableInputChange( this )"></pre>
                                            <textarea class="content" name="content" style="display: none"></textarea>
                                        </h3>

                                        <div class="entry_text_detail">
                                            <p>Credit</p> 
                                            <input class="status_create_input" type="text" name="credit" 
                                                value="<?php echo $entry_credit; ?>"
                                            >
                                        </div>
                                    </div>

                                    <button type="submit" name="translate" class="translate_options_next">
                                        <p>Send</p>
                                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M21.7267 2.95694L16.2734 22.0432C16.1225 22.5716 15.7979 22.5956 15.5563 22.1126L11 13L1.9229 9.36919C1.41322 9.16532 1.41953 8.86022 1.95695 8.68108L21.0432 2.31901C21.5716 2.14285 21.8747 2.43866 21.7267 2.95694ZM19.0353 5.09647L6.81221 9.17085L12.4488 11.4255L15.4895 17.5068L19.0353 5.09647Z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php 
                    }
                    ?>
                </div>
                <div class="status_info">
                    <div class="status_credit <?php echo $entry_credit_display; ?>"><?php echo $entry_credit; 
                    ?></div><div class="status_date"><?php echo $entry_date_modified; 
                    ?></div><div class="status_author hidden"><a>TODO</a></div>
                </div>
            </div>
        </div>
        <div class="button_group">
            <button class="button_link" data-sharehref="<?php echo $entry_sharehref; ?>">
                <svg stroke="currentColor" fill="currentColor"
                    stroke-width="0" viewBox="0 0 24 24" height="20" width="20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M18.3638 15.5355L16.9496 14.1213L18.3638 12.7071C20.3164 10.7545 20.3164 7.58866 18.3638 5.63604C16.4112 3.68341 13.2453 3.68341 11.2927 5.63604L9.87849 7.05025L8.46428 5.63604L9.87849 4.22182C12.6122 1.48815 17.0443 1.48815 19.778 4.22182C22.5117 6.95549 22.5117 11.3876 19.778 14.1213L18.3638 15.5355ZM15.5353 18.364L14.1211 19.7782C11.3875 22.5118 6.95531 22.5118 4.22164 19.7782C1.48797 17.0445 1.48797 12.6123 4.22164 9.87868L5.63585 8.46446L7.05007 9.87868L5.63585 11.2929C3.68323 13.2455 3.68323 16.4113 5.63585 18.364C7.58847 20.3166 10.7543 20.3166 12.7069 18.364L14.1211 16.9497L15.5353 18.364ZM14.8282 7.75736L16.2425 9.17157L9.17139 16.2426L7.75717 14.8284L14.8282 7.75736Z">
                    </path>
                </svg>
                <p>Copy Link</p>
            </button>

            <?php if( ! $ref ) { ?>  
            <button class="button_translations"
                <?php echo count( $remaining_translations ) ? '' : 'disabled'; ?>
            >
                <svg stroke="currentColor" fill="currentColor" 
                    stroke-width="0" viewBox="0 0 24 24" height="20" width="20" 
                    xmlns="http://www.w3.org/2000/svg">
                        <path 
                            d="M5 15V17C5 18.0544 5.81588 18.9182 6.85074 18.9945L7 19H10V21H7C4.79086 21 3 19.2091 3 17V15H5ZM18 10L22.4 21H20.245L19.044 18H14.954L13.755 21H11.601L16 10H18ZM17 12.8852L15.753 16H18.245L17 12.8852ZM8 2V4H12V11H8V14H6V11H2V4H6V2H8ZM17 3C19.2091 3 21 4.79086 21 7V9H19V7C19 5.89543 18.1046 5 17 5H14V3H17ZM6 6H4V9H6V6ZM10 6H8V9H10V6Z">
                        </path>
                </svg>
                <p>Translate</p>
            </button>
            <?php } ?>

        </div>
    </div>
    <?php 
}

?>
