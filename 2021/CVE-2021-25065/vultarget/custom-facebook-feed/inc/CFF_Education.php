<?php

/**
 *
 * @since 5.5
 */
namespace CustomFacebookFeed;

class CFF_Education {

	var $plugin_version;

	/**
	 * Constructor.
	 *
	 * @since 5.5
	 */
	public function __construct() {

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 5.5
	 */
	public function hooks() {
		$this->plugin_version = defined( 'WPW_SL_STORE_URL' ) ? 'pro' : 'free';
	}

	/**
	 * "Did You Know?" messages.
	 *
	 * @since 5.5
	 */
	public function dyk_messages() {

		$free_messages = array(
			array(
				'title' => esc_html__( 'Bring Your Feeds to Life with Media Content', 'custom-facebook-feeds' ),
				'content' => esc_html__( 'You\'ve done the hard work of getting a visitor onto your site, now keep them there by displaying your Facebook photos and videos directly on your site, rather than sending your visitors away to Facebook.', 'custom-facebook-feeds' ),
				'more' => 'https://smashballoon.com/custom-facebook-feed/',
				'item' => 1,
			),
			array(
				'title' => esc_html__( 'Use Facebook Reviews to Boost Conversions', 'custom-facebook-feeds' ),
				'content' => esc_html__( 'Reviews for your product or service are the best way to give users the confidence to take action. With the Reviews extension, easily add 5 star reviews and recommendations from Facebook to your website help increase conversions.', 'custom-facebook-feeds' ),
				'more' => 'https://smashballoon.com/extensions/reviews/',
				'item' => 2,
			),
			array(
				'title' => esc_html__( 'Bring the Conversation to Your Website', 'custom-facebook-feeds' ),
				'content' => esc_html__( 'Include the Facebook comments with each of your posts to engage your website viewers, keep them on your site for longer, and encourage them to interact with your content.', 'custom-facebook-feeds' ),
				'more' => 'https://smashballoon.com/custom-facebook-feed/wordpress-plugin/#comments',
				'item' => 3,
			),
			array(
				'title' => esc_html__( 'Automatically Feed Facebook Events to Your Website', 'custom-facebook-feeds' ),
				'content' => esc_html__( 'Save yourself the time and effort of posting events both to Facebook and your website by using the Custom Facebook Feed Pro plugin to automatically feeds events right to your site.', 'custom-facebook-feeds' ),
				'more' => 'https://smashballoon.com/custom-facebook-feed/docs/displaying-facebook-events-using-wordpress-plugin/',
				'item' => 4,
			),
		);

		$pro_messages = array(
			array(
				'title' => esc_html__( 'Automated YouTube Live Streaming', 'custom-facebook-feeds' ),
				'content' => esc_html__( 'You can automatically feed live YouTube videos to your website using our Feeds For YouTube Pro plugin. It takes all the hassle out of publishing live videos to your site by automating the process.', 'custom-facebook-feeds' ),
				'more' => 'https://smashballoon.com/youtube-feed/',
				'item' => 1,
			),
			array(
				'title' => esc_html__( 'Use Facebook Reviews to Boost Conversions', 'custom-facebook-feeds' ),
				'content' => esc_html__( 'Reviews for your product or service are the best way to give users the confidence to take action. With the Reviews extension, easily add 5 star reviews and recommendations from Facebook to your website help increase conversions.', 'custom-facebook-feeds' ),
				'more' => 'https://smashballoon.com/extensions/reviews/',
				'item' => 2,
			),
			array(
				'title' => esc_html__( 'Adding Social Proof with Twitter Feeds', 'custom-facebook-feeds' ),
				'content' => esc_html__( 'Twitter testimonials are one of the best ways to add verifiable social proof to your website. They add credibility to your brand, product, or service by displaying reviews from real people to your site, helping to convert more visitors into customers. Our free Custom Twitter Feeds plugin makes displaying Tweets on your website a breeze.', 'custom-facebook-feeds' ),
				'more' => 'https://wordpress.org/plugins/custom-twitter-feeds/',
				'item' => 3,
			),
			array(
				'title' => esc_html__( 'Run Promotions with Instagram Hashtags', 'custom-facebook-feeds' ),
				'content' => esc_html__( 'You can use hashtags on Instagram for so many things; but one of the most effective is for targeted promotions or competitions which engage with your audience and boost your exposure. Our Instagram Feed Pro plugin allows you to bring your hashtagged content into your website and display it in one centralized location.', 'custom-facebook-feeds' ),
				'more' => 'https://smashballoon.com/instagram-feed/features/#hashtag',
				'item' => 4,
			),
		);

		if ( $this->plugin_version === 'pro' ) {
			return $pro_messages;
		}
		return $free_messages;

	}

	/**
	 * "Did You Know?" random message.
	 *
	 * @since 5.5
	 */
	public function dyk_message_rnd() {

		$messages = $this->dyk_messages();

		$index = array_rand( $messages );

		return $messages[ $index ];
	}

	/**
	 * "Did You Know?" display message.
	 *
	 * @since 5.5
	 *
	 */
	public function dyk_display() {

		$dyk_message  = $this->dyk_message_rnd();

		if ( ! empty( $dyk_message['more'] ) ) {
			$dyk_message['more'] = add_query_arg(
				array(
					'utm_campaign' => 'facebook-'.$this->plugin_version,
					'utm_source'   => 'issueemail',
					'utm_content'  => $dyk_message['item'],
				),
				$dyk_message['more']
			);
		}

		return $dyk_message;
	}
}
