const copyShareLink = async ( evt ) => 
{
    var button = evt.target;
    
    if( button.tagName !== 'BUTTON' )
    {
        button = button.parentNode;
    }

    var href = location.href;

    if( button.dataset.hasOwnProperty( 'sharehref' ) )
    {
        href = ( new URL( button.dataset.sharehref, window.location.origin ) ).href;
    }

    try {
        await navigator.clipboard.writeText(href);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
}

window.copyShareLink = copyShareLink;

const handleStatusCopy = async (evt) => {
    const pre_node = evt.target?.parentNode?.querySelector('pre');

    if (pre_node) 
    {
        if( pre_node.classList.contains( 'content-pre' ) )
        {
            return ;
        }

        try {
            await navigator.clipboard.writeText(pre_node.innerText);
        } catch (err) {
            console.error('Failed to copy: ', err);
        }
    }
}

window.handleStatusCopy = handleStatusCopy;

window.toggleTranslationForm = function ( evt ) 
{
    var form = document.querySelector( 'form[name=translate1]' );
    if( form !== null )
    {
        var slist = document.querySelector( '.status_listing' );
        slist.classList.toggle( 'status_listing_translation_open' );

        form.querySelector( 'pre' ).focus();
    }
}

window.addEventListener("load", () => {
    Array.from(
        document.querySelectorAll('h3.entry_text')
    )
        .map(item => item.addEventListener('click', window.handleStatusCopy));

    Array.from(
        document.querySelectorAll('button.button_link')
    )
        .map(item => item.addEventListener('click', window.copyShareLink));

    Array.from(
        document.querySelectorAll('button.button_translations')
    )
        .map(item => item.addEventListener( 'click', window.toggleTranslationForm ) );
});

window.handleContentEditableInputChange = function ( target ) 
{
    var textarea = target.parentNode.querySelector( 'textarea.content' );
    textarea.value = target.innerText;
}

window.addEventListener( 'load', () => 
{
    Array.from( document.querySelectorAll( '.status_date' ) ).map( ( element ) => 
    {
        if( ! element.innerText.trim() )
        {
            return ;
        }

        element.innerText = ( new Date( element.innerText ) ).toLocaleString();
    } );
} )

