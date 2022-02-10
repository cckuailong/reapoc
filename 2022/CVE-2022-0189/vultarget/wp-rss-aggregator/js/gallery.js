function WpraGallery(config) {
    this.config = config;
    this.gallery = null;

    this.valueEl = null;
    this.openEl = null;
    this.removeEl = null;
    this.previewEl = null;
    this.previewHintEl = null;

    config.elements && (this.valueEl = config.elements.value);
    config.elements && (this.openEl = config.elements.open);
    config.elements && (this.removeEl = config.elements.remove);
    config.elements && (this.previewEl = config.elements.preview);
    config.elements && (this.previewHintEl = config.elements.previewHint);

    this.createGallery();

    if (this.openEl !== null) {
        this.openEl.click(this.open.bind(this));
    }

    if (this.previewEl !== null) {
        this.previewEl.css({cursor: 'pointer'});
        this.previewEl.click(this.open.bind(this));
    }

    if (this.removeEl !== null) {
        this.removeEl.click(this.update.bind(this));
    }

    var image = (this.valueEl)
        ? {id: this.valueEl.val(), url: this.previewEl.attr('src')}
        : null;
    this.update(image);
}

WpraGallery.prototype.createGallery = function () {
    var args = {
        id: this.config.id,
        title: this.config.title,
        button: {
            text: this.config.button
        },
        library: this.config.library,
        multiple: this.config.multiple,
    };

    this.gallery = wp.media.frames[this.config.id] = wp.media(args);

    this.gallery.states.add([
        new wp.media.controller.Library({
            id:         this.config.id,
            title:      this.config.title,
            priority:   0,
            toolbar:    'main-gallery',
            filterable: 'uploaded',
            library:    wp.media.query( this.gallery.options.library ),
            multiple:   this.config.multiple,
            editable:   this.config.editable,
        }),
    ]);

    // Hide the gallery side bar
    this.gallery.on('ready', function () {
        jQuery('#' + this.config.id).addClass('hide-menu');
    }.bind(this));

    // Set selected image when the gallery is opened
    this.gallery.on('open', function () {
        // Hide the gallery side bar
        jQuery('#' + this.config.id).addClass('hide-menu');
        var id = this.valueEl.val();

        if (id) {
            var attachment = wp.media.attachment(id);
            attachment.fetch();
            this.gallery.state().get('selection').add(attachment ? [attachment] : []);
        }
    }.bind(this));

    var selectCb = function () {
        var image = this.gallery.state().get('selection').first();

        this.update({
            id: image.attributes.id,
            url: image.attributes.url,
        });
    }.bind(this);

    // Update fields when an image is selected and the modal is closed
    this.gallery.on('insert', selectCb);
    this.gallery.on('select', selectCb);
};

WpraGallery.prototype.update = function (image) {
    if (image && image.id) {
        this.valueEl && this.valueEl.val(image.id);
        this.previewEl && this.previewEl.attr('src', image.url).show();
        this.previewHintEl && this.previewHintEl.show();
        this.removeEl && this.removeEl.show();
        this.openEl && this.openEl.hide();

        return;
    }

    this.valueEl && this.valueEl.val('');
    this.previewEl && this.previewEl.hide().attr('src', '');
    this.previewHintEl && this.previewHintEl.hide();
    this.removeEl && this.removeEl.hide();
    this.openEl && this.openEl.show();
};

WpraGallery.prototype.open = function (image) {
    this.gallery.open();
};
