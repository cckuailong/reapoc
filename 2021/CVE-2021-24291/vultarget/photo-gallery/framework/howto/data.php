<?php
$howto = array(
  0 => array(
    'title' => __('How to insert Photo Gallery (shortcode) ?', BWG()->prefix),
    'description' => '',
    'content' => array(
      1 => array(
        'title' => __('Click Add Photo Gallery Button', BWG()->prefix),
        'content' => __('To add a gallery using shortcode, please find and click the "Add Photo Gallery" button in your post/page editor navigation.', BWG()->prefix),
        'screenshot' => BWG()->plugin_url . '/framework/howto/screenshots/0_1.png',
        'function' => ''
      ),
      2 => array(
        'title' => __('Configure and Insert', BWG()->prefix),
        'content' => __('In the pop-up, choose gallery view type, gallery, theme and configure other options for your gallery. Click Insert into post button to add the gallery to the post/page.', BWG()->prefix),
        'screenshot' => BWG()->plugin_url . '/framework/howto/screenshots/0_2.png',
        'function' => ''
      ),
      3 => array(
        'title' => __('Congrats !', BWG()->prefix),
        'content' => __('You\'ve added a gallery to your post/page. Click on Photo Gallery icon again if you want to make changes to your gallery.', BWG()->prefix),
        'screenshot' => BWG()->plugin_url . '/framework/howto/screenshots/0_3.png',
        'function' => ''
      ),
    )
  ),
  1 => array(
    'title' => __('How to insert Photo Gallery as widget ?', BWG()->prefix),
    'description' => '',
    'content' => array(
      1 => array(
        'title' => __('Add Gallery Widget', BWG()->prefix),
        'content' => __('In your WordPress dashboard go to Appearance > Widgets. Find Photo Gallery Widget in the list, click and choose the area you want to display the gallery and click "Add Widget button".', BWG()->prefix),
        'screenshot' => BWG()->plugin_url . '/framework/howto/screenshots/1_1.png',
        'function' => ''
      ),
      2 => array(
        'title' => __('Configure and Save', BWG()->prefix),
        'content' => __('Configure gallery options on the right side of the page, including gallery/album you want to display, image count, dimensions and more. Click save to display the changes on your website.', BWG()->prefix),
        'screenshot' => BWG()->plugin_url . '/framework/howto/screenshots/1_2.png',
        'function' => ''
      ),
      3 => array(
        'title' => __('Congrats !', BWG()->prefix),
        'content' => __('Great, you\'ve added a gallery widget to your site. You can always go back to the widgets page on your dashboard to edit or delete the Photo Gallery Widget or add a new one.', BWG()->prefix),
        'screenshot' => '',
        'function' => ''
      ),
    )
  ),
  2 => array(
    'title' => __('How to insert a shortcode in Gutenberg?', BWG()->prefix),
    'description' => '',
    'content' => array(
      1 => array(
        'title' => __('Click the Photo Gallery Button', BWG()->prefix),
        'content' => __('Head to the page/post you want to insert a gallery in. In the Gutenberg editor, click “add block” then click the Photo Gallery button.', BWG()->prefix),
        'screenshot' => BWG()->plugin_url . '/framework/howto/screenshots/2_1.png',
        'function' => ''
      ),
      2 => array(
        'title' => __('Configure and Insert Gallery', BWG()->prefix),
        'content' => __('From the window that follows, select which gallery to insert, its layout, and configure its options. Click “Insert Into Post” to add your selected gallery to the post/page.', BWG()->prefix),
        'screenshot' => BWG()->plugin_url . '/framework/howto/screenshots/2_2.png',
        'function' => ''
      ),
      3 => array(
        'title' => __('Congrats!', BWG()->prefix),
        'content' => __('You’ve successfully inserted your gallery to your post/page. Click the Photo Gallery icon in your editor to make any further changes to your gallery.', BWG()->prefix),
        'screenshot' => BWG()->plugin_url . '/framework/howto/screenshots/2_3.png',
        'function' => ''
      ),
    )
  )
);
if ( !empty($params['shortcode_id']) ) {
  $howto[] = array(
    'title' => __('Advanced: PHP code', BWG()->prefix),
    'description' => sprintf(__('%s This code should be inserted into a PHP file. This is a more advanced method of publishing and should be used in case you want the galleries to be integrated into your theme files ( for example in your website header or footer area ). You can\'t insert this code into your post / page editor.', BWG()->prefix), '<span class="wh-howto-attention">' . __('Attention:', BWG()->prefix) . '</span>'),
    'content' => array(
      1 => array(
        'title' => __('Copy the Code', BWG()->prefix),
        'content' => __('Copy the PHP code that appears below before proceeding to next step.', BWG()->prefix),
        'screenshot' => '',
        'function' => 'bwg_howto_php_code'
      ),
      2 => array(
        'title' => __('Paste the Code', BWG()->prefix),
        'content' => __('In your dashboard navigation go to Appearance > Editor. Choose the specific section you want the gallery to appear and paste the PHP code you copied in that section. Press Update File button to save the changes in the code.', BWG()->prefix),
        'screenshot' => BWG()->plugin_url . '/framework/howto/screenshots/3_1.png',
        'function' => ''
      ),
      3 => array(
        'title' => __('Congrats !', BWG()->prefix),
        'content' => __('The Photo Gallery will now appear in the section of the site you’ve chosen.', BWG()->prefix),
        'screenshot' => '',
        'function' => ''
      )
    )
  );
}
function bwg_howto_php_code($params) {
  ?>
  <script>
    function wd_copy_input_value(input) {
      var copyText = document.getElementById(input);
      copyText.select();
      document.execCommand("copy");
    }
  </script>
  <style>
    .wd-howto-phpcode {
      background: #F1F1F1;
      border: #0000000d 1px solid;
      border-radius: 10px;
      padding: 20px;
      margin: 20px 0;
    }
    #wd_howto_php_code.wd-howto-phpinput {
      color:#323A45;
      background-color: #FFFFFF;
      border: #00000026 0.5px solid;
      border-radius: 18px;
      box-shadow: 0px 3px 2px #EBEBEBB5;
      width: calc(100% - 140px);
      height: 36px;
      font-family: Ubuntu;
      font-size: 14px;
      font-weight: 300;
      padding: 0 10px;
    }
    .wd-howto-phpinput:focus {
      outline: none;
    }
    .wd-howto-copy-button {
      background: #2160B5;
      border: #00000026 0.5px solid;
      border-radius: 100px;
      box-shadow: 0px 3px 2px #EBEBEBB5;
      color: #FFFFFF;
      font-family: Ubuntu;
      font-size: 12px;
      font-weight: 300;
      text-transform: uppercase;
      width: 90px;
      height: 36px;
      margin: 0 20px;
      cursor: pointer;
    }
    .wd-howto-copy-button:hover {
      opacity: 0.8;
    }
    .wd-howto-copy-button:focus {
      outline: none;
    }
    @media (max-width:480px) {
      .wd-howto-phpcode {
        text-align: center;
      }
      #wd_howto_php_code.wd-howto-phpinput {
        margin-bottom: 10px;
        width: 100%;
      }
    }
  </style>
  <div class="wd-howto-phpcode">
    <input id="wd_howto_php_code" class="wd-howto-phpinput" value="&#60;?php if( function_exists('photo_gallery') ) { photo_gallery(<?php echo $params['shortcode_id']; ?>); } ?&#62;" onclick="spider_select_value(this)" readonly="readonly" />
    <button class="wd-howto-copy-button" onclick="wd_copy_input_value('wd_howto_php_code'); return false;"><?php _e('Copy', BWG()->prefix); ?></button>
  </div>
  <?php
}