jQuery(document).ready(function(){

    //Badges Search Filter
    jQuery("#mycerd-badges-search").on("keyup", function() {
        var value = jQuery(this).val().toLowerCase();
        jQuery(".mycred-badges-list .mycred-badges-list-item").filter(function() {
            jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    //Show Achieved Badges
    jQuery(document).on( 'click', '.mycred-achieved-badge-btn', function(e){
        e.preventDefault();
        jQuery('.not-earned').hide();
        jQuery('.earned').show();
    });

    //Show Not Achieved Badges
    jQuery(document).on( 'click', '.mycred-not-achieved-badge-btn', function(e){
        e.preventDefault();
        jQuery('.earned').hide();
        jQuery('.not-earned').show();
    });

    //Clear Filter Button
    jQuery(document).on( 'click', '.mycred-clear-filter-btn', function(e){
        e.preventDefault()
        jQuery('.earned').show();
        jQuery('.not-earned').show();
        jQuery('#mycerd-badges-search').val('');
    } )
});

