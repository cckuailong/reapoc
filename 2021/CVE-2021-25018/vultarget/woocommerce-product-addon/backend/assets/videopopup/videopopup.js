/*
    GitHub URL: https://github.com/gucastiliao/video-popup-js
*/

(function($) {
    $.fn.videoPopup = function(options) {
        var videoPopup = {
            embedLink: ''
        }

        var settings = $.extend({
            autoplay: false,
            showControls: true,
            controlsColor: null,
            loopVideo: false,
            showVideoInformations: true,
            width: null,
            customOptions: {}
        }, options);

        var parsers = {
            youtube: {
                regex: /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/,
                test: function (videoUrl, regex) {
                    var match = videoUrl.match(regex);
                    return (match && match[7].length==11) ? match[7] : false;
                },
                mount: function (videoCode) {
                    var youtubeOptions = { 
                        autoplay: settings.autoplay,
                        color: settings.controlsColor,
                        loop: settings.loopVideo,
                        controls: settings.showControls,
                        showinfo: settings.showVideoInformations,
                    }

                    Object.assign(youtubeOptions, settings.customOptions);
                    
                    return "https://www.youtube.com/embed/"+videoCode+"/?"+$.param(youtubeOptions);
                }
            },
            vimeo: {
                regex: /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/,
                test: function (videoUrl, regex) {
                    var match = videoUrl.match(regex);
                    return (match && match[5].length) ? match[5] : false;
                },
                mount: function (videoCode) {
                    var vimeoOptions = {
                        autoplay: settings.autoplay,
                        color: settings.controlsColor,
                        loop: settings.loopVideo,
                        controls: settings.showControls,
                        title: settings.showVideoInformations,
                    }

                    Object.assign(vimeoOptions, settings.customOptions);
                    
                    return "https://player.vimeo.com/video/"+videoCode+"/?"+$.param(vimeoOptions);
                }
            }
        }
        
        function mountEmbedLink(videoUrl) {
            $.each(parsers, function(index, parser){
                var videoCode = parser.test(videoUrl, parser.regex);
                
                if(videoCode) {
                    videoPopup.embedLink = parser.mount(videoCode);
                    return this;
                }
            })
        }

        function mountIframe() {
            var iframeElement = '<iframe src="'+videoPopup.embedLink+'" allowfullscreen frameborder="0" width="'+settings.width+'"></iframe>';

            if(!videoPopup.embedLink) {
                iframeElement = '<div class="videopopupjs__block--notfound">Video not found</div>';
            }

            return '<div class="videopopupjs videopopupjs--animation">'+
                        '<div class="videopopupjs__content">'+
                            '<span class="videopopupjs__close"></span>'+
                            iframeElement+
                        '</div>'+
                    '</div>';
        }

        $(this).css('cursor', 'pointer');
        $(this).on('click', function (event) {
            event.preventDefault();
            
            var videoUrl = $(this).attr("video-url");
            var videoIframe = mountEmbedLink(videoUrl);

            $("body").append(mountIframe());

            $('.videopopupjs__content').css('max-width', 700);
            if(settings.width) {
                $('.videopopupjs__content').css('max-width', settings.width);
            }

            if($('.videopopupjs').hasClass('videopopupjs--animation')){
                setTimeout(function() {
                    $('.videopopupjs').removeClass("videopopupjs--animation");
                }, 200);
            }

            $(".videopopupjs, .videopopupjs__close").click(function(){
                $(".videopopupjs").addClass("videopopupjs--hide").delay(515).queue(function() { $(this).remove(); });
            });
        });

        $(document).keyup(function(event) {
            if (event.keyCode == 27){
                $('.videopopupjs__close').click();
            }
        });

        return this;
    };
}(jQuery));