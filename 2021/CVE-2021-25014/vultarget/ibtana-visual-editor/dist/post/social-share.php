<?php

if ( isset( $attributes['displaySocialShareIcons'] ) && ( ( $attributes['displaySocialShareIcons'][0] == 'true' ) || ( $attributes['displaySocialShareIcons'][1] == 'true' ) || ( $attributes['displaySocialShareIcons'][2] == 'true' ) ) ) {

  $perma_link = get_permalink( $post_id );
  // Get the featured image.
  if ( has_post_thumbnail( $post_id ) ) {
    $thumbnail_id = get_post_thumbnail_id( $post_id );
    $thumbnail    = $thumbnail_id ? current( wp_get_attachment_image_src( $thumbnail_id, 'large', true ) ) : '';
  } else {
    $thumbnail = null;
  }

  $thumbnail = esc_url( $thumbnail );

  $links  = array(
    'facebook'      =>  "https://www.facebook.com/sharer.php?u=".$perma_link,
    'twitter'       =>  "https://twitter.com/share?url=".$perma_link,
    'google'        =>  "https://plus.google.com/share?url=".$perma_link,
    'linkedin'      =>  "https://www.linkedin.com/shareArticle?url=".$perma_link,
    'digg'          =>  "http://digg.com/submit?url=".$perma_link,
    'blogger'       =>  "https://www.blogger.com/blog_this.pyra?t&amp;u=".$perma_link,
    'reddit'        =>  "https://reddit.com/submit?url=".$perma_link,
    'stumbleupon'   =>  "https://www.stumbleupon.com/submit?url=".$perma_link,
    'tumblr'        =>  "https://www.tumblr.com/widgets/share/tool?canonicalUrl=".$perma_link,
    'myspace'       =>  "https://myspace.com/post?u=".$perma_link,
    'email'         =>  "mailto:?body=" . $perma_link,
    'pinterest'     =>  "https://pinterest.com/pin/create/link/?url=" . $perma_link . "&media=" . $thumbnail,
    'vk'            =>  'https://vkontakte.ru/share.php?url=' . $perma_link,
    'odnoklassniki' =>  'https://connect.ok.ru/offer?url=' . $perma_link,
    'pocket'        =>  'https://getpocket.com/edit?url=' . $perma_link,
    'whatsapp'      =>  'https://api.whatsapp.com/send?text=' . $perma_link, // whatsapp://send?text=*{title}*\n{text}\n{url}',//https://api.whatsapp.com/send?text=textToshare
    'xing'          =>  'https://www.xing.com/app/user?op=share&url=' . $perma_link,
    'telegram'      =>  'https://telegram.me/share/url?url=' . $perma_link,
    'skype'         =>  'https://web.skype.com/share?url=' . $perma_link,
    'buffer'        =>  'https://buffer.com/add?url=' . $perma_link
  );

  $deskIconAlignment  = $attributes['deskIconAlignment'];
  $tabIconAlignment   = $attributes['mobIconAlignment'];
  $mobIconAlignment   = $attributes['tabIconAlignment'];

  $social_share_icons  = $attributes['icons'];

  $icons_html = '';
  foreach ( $social_share_icons as $index => $social_share_icon ) {
    $social_share_icon_socialShareType  = $social_share_icon['socialShareType'];
    $url  = $links[$social_share_icon_socialShareType];
    $icons_html .= sprintf(
      '<div class="ive-svg-style-' . $social_share_icon['style'] . ' ive-svg-icon-wrap ive-svg-item-' . $index . ' ive-svg-icon-margin">
        <a data-href="' . $url . '" target="' . $social_share_icon['target'] . '" rel="noopener">
          <div class="ive-svg-icon-link ive_icon_main_parent ive_icon_parent_icon_padding' . $index . '">
          <i class="ive_icon_parent' .  $index . ' ' . $social_share_icon['iconSvg'] . ' ive_icon_parent_icon_size' . $index . '"></i>
          </div>
        </a>
      </div>'
    );
  }

  $post_loop .=  sprintf(
    '<div class="ive-svg-icons-block ive-svg-icons' . $uniqueID . ' justify-content-xl-' . $deskIconAlignment . ' ' . 'justify-content-lg-' . $deskIconAlignment . ' ' . 'justify-content-sm-' . $tabIconAlignment . ' ' . 'justify-content-' . $mobIconAlignment . '">
    ' . $icons_html . '
    </div>'
  );
}
