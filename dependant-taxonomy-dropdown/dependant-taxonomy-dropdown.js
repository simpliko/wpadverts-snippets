jQuery(function($) {
    jQuery(".dependant-taxonomy-dropdown-ui").each(function(index, item) {
        new DependantTaxonomyDropdown( jQuery( item ) ); 
    });
});

function DependantTaxonomyDropdown( e ) {
    this.wrap = e;
    this.select = e.find( ".dependant-taxonomy-dropdown" );
    this.hidden = e.find( ".dependant-taxonomy-dropdown-value" );
    this.loader = e.find( ".dependant-taxonomy-loader .animate-spin");
    
    this.OnChange();
};

DependantTaxonomyDropdown.prototype.OnChange = function( e ) {
    var id = null;
    var prev = null;
    
    if( typeof e !== 'undefined') {
        id = jQuery( e.target ).val();
    } else {
        id = this.hidden.val();
    }
    
    if( id === "" && typeof e !== 'undefined' && jQuery( e.target ).prev().length > 0 ) {
        prev = jQuery( e.target ).prev();
    }
    
    if( id === "" &&  prev !== null && prev.prop("tagName").toLowerCase() === "select" ) {
        id = prev.val();
    }
    
    this.loader.css("display", "inline-block");
    this.wrap.css( "opacity", "0.5" );
    
    jQuery.ajax({
        url: adverts_frontend_lang.ajaxurl,
        type: "post",
        dataType: "json",
        data: {
            action: "dependant_taxonomy_dropdown",
            id: id,
            taxonomy: this.wrap.data("taxonomy")
        },
        success: jQuery.proxy( this.OnChangeSuccess, this )
    });
};

DependantTaxonomyDropdown.prototype.OnChangeSuccess = function( response ) {
    this.select.html( response.html );
    this.select.find("select").on( "change", jQuery.proxy( this.OnChange, this ) );
    this.hidden.val( response.selected );
    this.wrap.css( "opacity", "1" );
    this.loader.css("display", "none");
};