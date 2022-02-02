var Attachment = React.createClass({

    readableFileSize: function(size) {
        var units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        var i = 0;
        if (size >= 1024) {
	        while(size >= 1024) {
	            size /= 1024;
	            ++i;
	        }
	        return size.toFixed(1) + ' ' + units[i];
        } else {
        	return size + ' ' + units[0];
        }
        
    },

    render: function() {
        //console.log(this.props.attachment);

        return (
            <div>
                { this.props.attachment.map(function(item) {

                    var date = item.time.substring(0,4)+"-"+item.time.substring(4,6)+"-"+item.time.substring(6,8);

                    jQuery('#library-form').find('img').attr('src', item.img);
                    jQuery('#library-form #fbc_id').val(item.id);
                    jQuery('#library-form #fbc_scheme').val(item.scheme);
                    jQuery('#library-form #alt-text').val(item.name);
                    jQuery('#library-form #description').val(item.description);
                    jQuery('#library-form #copyright').val(item.copyright);
                    jQuery('#library-form #terms').val(item.terms);
                    jQuery('#library-form .filename').html(item.name);
                    jQuery('#library-form .filesize').html( this.readableFileSize(item.size) );
                    jQuery('#library-form .dimensions').html('');
                    jQuery('#library-form .uploaded').html(date);
                    jQuery("#library-form").appendTo("#fbc_media-sidebar");
                    jQuery("#library-form").show();
                    
                    //return();
                }, this)}
            </div>
        );
    }

});
