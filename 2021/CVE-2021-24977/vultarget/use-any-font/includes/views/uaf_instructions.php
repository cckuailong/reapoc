<?php if ( ! defined( 'ABSPATH' ) ) exit;  ?>
<div class="dcinstructions">
    <div class="instruct-head">        
        Step 1 : Verify API Key
    </div>

    <div class="instruct-content show">        
            You need API key to connect to our server for font upload. From API Key tab, you can generate the Free / Test API key. It allows you to upload single font. For multiple font upload, you can purchase the premium key from <a href="https://dineshkarki.com.np/use-any-font/api-key" target="_blank">here</a>.<br/><br/>
            <em>Note : API key is needed to connect to our server to convert the fonts. Our server converts the fonts to required formats and sends back.</em>
        
    </div>
</div>


<div class="dcinstructions">
    <div class="instruct-head">        
        Step 2 : Upload Your Custom Font
    </div>

    <div class="instruct-content">        
        After API key verification is done, you can upload the font.
        <ul>
            <li>Goto Upload Font Tab</li>
            <li>Keep The Font Name as Reference</li>
            <li>Select The font file.</li>
            <li>Click On Upload</li>
        </ul>
    </div>
</div>

<div class="dcinstructions">
    <div class="instruct-head">        
        Step 3 : Assign Font to elements
    </div>

    <div class="instruct-content">        
        After you have uploaded the font, you can assign the font to your elements as per needed
        <ul>
            <li>You can assign the font using our Assign Font Tab</li>
            <li>You can select the font and predefined elements to assign the fonts</li>
            <li>Also, our plugin supports most of the popular page builder. You can use those page builders to assign the font as well.</li>
            <li>You can also, enable Multi Language support from Settings Tab if you want to assign the font based on the language. (Currently supported for WPML and Polylang only)</li>            
        </ul>
    </div>
</div>

<div class="dcinstructions">
    <div class="instruct-head">        
        Still Having Issue ?
    </div>

    <div class="instruct-content">        
        We love to support our plugin as much as developing it. You can contact us using the followin medium.
        <ul>
            <li><a href="https://www.messenger.com/t/77553779916/" target="_blank">Facebook Page Chat</a></li>
            <li><a href="https://wordpress.org/support/plugin/use-any-font/" target="_blank">Wordpress.org Support Forum</a></li>
            <li><a href="https://dineshkarki.com.np/forums/forum/use-any-fonts" target="_blank">Dnesscarkey's Support Forum</a></li>
            <li><a href="https://dineshkarki.com.np/contact" target="_blank">Contact Us</a></li>
        </ul>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.instruct-head').click(function(){
            jQuery(this).next('.instruct-content').slideToggle('medium');
        })
    });
</script>