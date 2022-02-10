function wpdm_embed(url){
    var frameid = wpdm_frame_id('wpdm');
    document.write("<iframe id='"+frameid+"' style=\"width: 100%; border: 0;\" src=\""+url+"&frameid="+frameid+"\"></iframe>")
}


function wpdm_frame_id (prefix, more_entropy) {

    if (typeof prefix === 'undefined') {
        prefix = "";
    }

    var retId;
    var formatSeed = function (seed, reqWidth) {
        seed = parseInt(seed, 10).toString(16); // to hex str
        if (reqWidth < seed.length) { // so long we split
            return seed.slice(seed.length - reqWidth);
        }
        if (reqWidth > seed.length) { // so short we pad
            return Array(1 + (reqWidth - seed.length)).join('0') + seed;
        }
        return seed;
    };

    // BEGIN REDUNDANT
    if (!this.php_js) {
        this.php_js = {};
    }
    // END REDUNDANT
    if (!this.php_js.uniqidSeed) { // init seed with big random int
        this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
    }
    this.php_js.uniqidSeed++;

    retId = prefix; // start with prefix, add current milliseconds hex string
    retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
    retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
    if (more_entropy) {
        // for more entropy we add a float lower to 10
        retId += (Math.random() * 10).toFixed(8).toString();
    }

    return retId;
}

function wpdm_adjust_frame_height(id,height) {
    document.getElementById(id).style.height = height+"px";
}
function hideLockFrame() {
    var element = document.getElementById("wpdm-lock-frame");
    element.parentNode.removeChild(element);
}


if (window.addEventListener) {
    window.addEventListener("message", listenMsg, false);
}
else if (window.attachEvent) {
    window.attachEvent("onmessage", listenMsg, false);
}

function listenMsg(event) {
    // Check sender origin to be trusted
    //if (event.origin !== "http://example.com") return;

    var data = event.data;

    if (data.task === "showiframe") {
        window.document.body.innerHTML += data.iframe;
    }
    if (data.task === "hideiframe") {
        hideLockFrame();
    }
}
