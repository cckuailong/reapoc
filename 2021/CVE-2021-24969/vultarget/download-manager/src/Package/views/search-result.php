<div class='w3eden'>
    <form id="srcp" style="margin-bottom: 10px">
        <input type="text" class="form-control form-control-lg" style="border-radius: 3px;background-position: 16px 11px;padding-left: 50px !important;" name="src" placeholder="<?php _e('Search Package','wpdmap'); ?>" id="src">
    </form>
    <div style='clear: both;'>
        <div  class='wpdm-downloads' id='wpdm-downloads'></div>
    </div>
</div>
<script>
    function htmlEncode(value){
        return jQuery('<div/>').text(value).html();
    }
    jQuery('#srcp').submit(function(e){
        e.preventDefault();
        jQuery('.wpdm-cat-link').removeClass('active');
        jQuery('#inp').html('<?php _e('Search Result For','wpdmap'); ?> <b>'+htmlEncode(jQuery('#src').val())+'</b>');
        jQuery('#wpdm-downloads').prepend('<div class="wpdm-loading"><i class="fa fa-spin fa-spinner icon icon-spin icon-spinner"></i>  <?php _e('Loading','wpdmap'); ?>...</div>').load('<?php echo  home_url('/?wpdmtask=get_downloads&pg='.get_the_ID().'&search='); ?>'+encodeURIComponent(jQuery('#src').val() ));
    });
    jQuery('body').on('click', '.pagination a',function(e){
        e.preventDefault();
        jQuery('#wpdm-downloads').prepend('<div class="wpdm-loading"><i class="fa fa-spin fa-spinner icon icon-spin icon-spinner"></i> <?php _e('Loading','wpdmap'); ?>...</div>').load(this.href);
        return false;
    });
</script>
