jQuery(function($) {
    if( $( ".adverts-button.adverts-show-contact").length > 0 ) {
        $( ".adverts-button.adverts-show-contact").click();
        $( ".adverts-button.adverts-show-contact").hide();
    }
    if( $( ".adverts-button.adverts-show-contact-form").length > 0 ) {
        $( ".adverts-contact-box").css( "display", "block" );
        $( ".adverts-button.adverts-show-contact-form").hide();
    }
});