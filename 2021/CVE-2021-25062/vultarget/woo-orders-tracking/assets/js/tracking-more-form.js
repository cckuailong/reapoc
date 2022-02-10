var head = document.getElementsByTagName('HEAD').item(0);
var style = document.createElement('link');
style.rel = 'stylesheet';
style.type = 'text/css';
style.href = '//s.trackingmore.com/plugins/v1/plugins.css?time=20190110';
head.appendChild(style);
jQuery(document).ready(function ($) {
    var LC = {
        cnNumbersRequired: "è¯·è¾“å…¥å•å·.",
        csNumbersRequired: "Zadejte ÄÃ­slo pro sledovÃ¡nÃ­.",
        deNumbersRequired: "Geben Sie Ihre Tracking-Nummer.",
        enNumbersRequired: "Enter your tracking number.",
        esNumbersRequired: "Introduzca su nÃºmero de seguimiento",
        fiNumbersRequired: "Seuranta numero.",
        frNumbersRequired: "Entrez votre numÃ©ro de suivi.",
        itNumbersRequired: "Immettere il numero di tracciatura",
        jaNumbersRequired: "è¿½è·¡ç•ªå·ã‚’å…¥åŠ›ã—ã¾ã™ã€‚",
        koNumbersRequired: "ë°°ì†¡ ì¶”ì  ë²ˆí˜¸ë¥¼ ìž…ë ¥í•˜ì„¸ìš”.",
        nlNumbersRequired: "Voer uw tracking-nummer.",
        plNumbersRequired: "Wpisz numer Å›ledzenia.",
        ptNumbersRequired: "Digite seu nÃºmero de rastreamento.",
        ruNumbersRequired: "Ð’Ð²ÐµÐ´Ð¸Ñ‚Ðµ Ð²Ð°Ñˆ Ð½Ð¾Ð¼ÐµÑ€ Ð´Ð»Ñ Ð¾Ñ‚ÑÐ»ÐµÐ¶Ð¸Ð²Ð°Ð½Ð¸Ñ.",
        trNumbersRequired: "Takip numarasÄ± girin.",
        twNumbersRequired: "è«‹è¼¸å…¥å–®è™Ÿ.",
    };
});
function doTrack() {
    var num, expCode, width, TRNum, src, iframe, color, lang, from, a, l;
    number = document.getElementById("button_tracking_number");
    expCode = document.getElementById("button_express_code").value;
    width = document.getElementById("query").parentNode.parentNode.offsetWidth;
    TRNum = document.getElementById("TRNum");
    iframe = document.createElement('iframe');
    a = document.createElement('a');
    from = jQuery("#button_tracking_number").closest("form");
    lang = from.find("input[name='lang']").val();
    if (!lang) lang = 'cn';
    num = number.value;
    color = number.style.borderColor;
    num = num.replace(/\s+/g, "");
    TRNum.innerHTML = '<a class="trFrameClose" onmouseover="this.style.backgroundColor=\'#BDBDBD\'" onmouseout="this.style.backgroundColor=\'#e3e3e3\'" style="position: absolute; right: 11px; top: 22px; width: 28px; height: 28px; line-height: 28px; background: rgb(227, 227, 227) none repeat scroll 0% 0%; color: rgb(33, 33, 33); text-align: center; font-family: Arial,Helvetica,sans-senif; z-index: 100; cursor: pointer; font-size: 20px; text-decoration: none; font-weight: 700;" onclick="TRNum.innerHTML=\'\'">Ã—</a>';
    if (num == "" || !/^[A-Za-z0-9-]{4,}$/.test(num)) {
        alert(eval("LC." + lang + "NumbersRequired") ? eval("LC." + lang + "NumbersRequired") : "Enter your tracking number.");
        return false;
    }
    src = '//track.trackingmore.com/choose.php?trackpath=plugins&express=&tracknumber=' + num + '&lang=' + lang;
    if (expCode) src = '//track.trackingmore.com/plugins.php?express=' + expCode + '&tracknumber=' + num + '&lang=' + lang;
    iframe.src = src;
    iframe.style.width = "100%";
    iframe.style.height = "600px";
    iframe.style.marginTop = "12px";
    iframe.style.border = "1px solid #ddd";
    if (!jQuery('#button_tracking_number').hasClass('TM_small_input_style')) iframe.style.borderRadius = "6px";
    iframe.scrolling = "no";
    TRNum.style.position = 'relative';
    TRNum.style.minWidth = '220px';
    TRNum.appendChild(iframe);
    return false;
}
