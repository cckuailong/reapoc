 /* Image loader */
function addListener(element, type, expression, bubbling) {
    bubbling = bubbling || false;
    if (window.addEventListener) { // Standard
        element.addEventListener(type, expression, bubbling);
        return true;
    } else if (window.attachEvent) { // IE
        element.attachEvent('on' + type, expression);
        return true;
    } else return false;
}


var ImageLoader = function (url) {
    this.url = url;
    this.image = null;
    this.loadEvent = null;
};

ImageLoader.prototype = {
    load: function () {
        this.image = document.createElement('img');
        var url = this.url;
        var image = this.image;
        var loadEvent = this.loadEvent;
        addListener(this.image, 'load', function (e) {
            if (loadEvent != null) {
                loadEvent(url, image);
            }
        }, false);
        this.image.src = this.url;
    },
    getImage: function () {
        return this.image;
    }
};
/* End of image loader */