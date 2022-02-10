<?php

global $likebtn_mycred_defaults;
$likebtn_mycred_defaults = array(
	'like'    => array(
		'creds'  => 1,
		'share'  => 0.0,
		'log'    => '%plural% for liking content',
		'limit'  => '0/x'
	),
	'get_like'  => array(
		'creds'  => 1,
		'share'  => 0.0,
		'log'    => '%plural% for getting a content like',
		'limit'  => '0/x'
	),
	'dislike'  => array(
		'creds'  => 1,
		'share'  => 0.0,
		'log'    => '%plural% for disliking content',
		'limit'  => '0/x'
	),
	'get_dislike'  => array(
		'creds'  => -1,
		'share'  => 0.0,
		'log'    => '%plural% deduction for getting a content dislike',
		'limit'  => '0/x'
	)
);

/**
 * Custom items not supported for now.
 */
if (class_exists('myCRED_Hook')) {

	class LikeBtn_MyCRED extends myCRED_Hook {

		const ID = 'likebtn';

		// Refence IDs
		const REF_LIKE = 'likebtn_like';
		const REF_GET_LIKE = 'likebtn_get_like';
		const REF_DISLIKE = 'likebtn_dislike';
		const REF_GET_DISLIKE = 'likebtn_get_dislike';

		public $hook_prefs_copy;
		public $type_copy;

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type ) {
			global $likebtn_mycred_defaults;
			$entities_mycred_defaults = array();

			// Modify default prefs
			$likebtn_entities = _likebtn_get_entities(true, false, false);

    		foreach ($likebtn_entities as $entity_name => $entity_title) {
    			foreach ($likebtn_mycred_defaults as $instance => $prefs) {
					$instance = $instance.'_'.$entity_name;
					$entities_mycred_defaults[$instance] = $prefs;
    			}
    		}

			parent::__construct( array(
				'id'       => self::ID,
				'defaults' => $entities_mycred_defaults
			), $hook_prefs, $type );

			// Copy settings to post (to support entities)
			foreach ($likebtn_mycred_defaults as $instance => $prefs) {
				if (isset($hook_prefs[self::ID][$instance])) {
					$this->prefs[$instance.'_'.LIKEBTN_ENTITY_POST] = $hook_prefs[self::ID][$instance];
				}
			}

			$this->hook_prefs_copy = $hook_prefs;
			$this->type_copy = $type;
		}

		/**
		 * Hook into WordPress
		 */
		public function run() {
			add_action('likebtn_mycred_like', array($this, 'like'), 10, 2);
			add_action('likebtn_mycred_dislike', array($this, 'dislike'), 10, 2);
		}

		/**
		 * Check if the user qualifies for points
		 */
		public function like($entity_name, $entity_id) {
			$this->award($entity_name, $entity_id, 'like', self::REF_LIKE, self::REF_GET_LIKE);
		}


		/**
		 * Check if the user qualifies for points
		 */
		public function dislike($entity_name, $entity_id) {
			$this->award($entity_name, $entity_id, 'dislike', self::REF_DISLIKE, self::REF_GET_DISLIKE);
		}

		/**
		 * Award user and author
		 */
		public function award($entity_name, $entity_id, $instance, $ref_user, $ref_author) {

			// We have to call cunstructor again, as custom post types are not
			// ready when it is called first time.
			$this->__construct( $this->hook_prefs_copy, $this->type_copy );

			$user_id 	= get_current_user_id();

			if (!$user_id) {
				return;
			}

			// Check if user is excluded (required)
			if ($this->core->exclude_user($user_id) || !$entity_name) {
				return;
			}

			$instance = $instance.'_'.$entity_name;
			$creds = $this->prefs[$instance]['creds'];
			$get_creds = $this->prefs['get_'.$instance]['creds'];
			$author_id 	= _likebtn_get_author_id($entity_name, $entity_id);
			$share = (float)$this->prefs[$instance]['share'];
			$get_share = (float)$this->prefs['get_'.$instance]['share'];

			if ($share != 0 || $get_share != 0) {
				$user_creds = (float)mycred_get_users_cred($user_id);
			}

			if ($share != 0) {
				$extra_creds = abs(($user_creds / 100.0) * $share);
				if ($creds > 0) {
					$creds = $creds + $extra_creds;
				} else {
					$creds = $creds - $extra_creds;
				}
			}
			if ($get_share != 0) {
				$extra_creds = abs(($user_creds / 100.0) * $get_share);
				if ($get_creds > 0) {
					$get_creds = $get_creds + $extra_creds;
				} else {
					$get_creds = $get_creds - $extra_creds;
				}
			}

			// Award for liking content
			if ($creds != 0) {
				$data = array('entity_name' => $entity_name);
				// Limit and make sure this is unique event
				if (!$this->over_hook_limit($instance, $ref_user, $user_id) &&
					!$this->core->has_entry($ref_user, $entity_id, $user_id, $data))
				{
					// Execute
					$this->core->add_creds(
						$ref_user,
						$user_id,
						$creds,
						$this->prefs[$instance]['log'],
						$entity_id,
						$data,
						$this->mycred_type
					);
				}
			}

			// Award post author for being liked
			// Do nothing is user liked own content
			if ($user_id == $author_id) {
				return;
			}
			if ($get_creds != 0 && $author_id) {
				$data = array('entity_name' => $entity_name, 'user_id' => $user_id);
				// Limit and make sure this is unique event
				if (!$this->over_hook_limit('get_'.$instance, $ref_author, $user_id) &&
					!$this->core->has_entry($ref_author, $entity_id, $user_id, $data))
				{
					// Execute
					$this->core->add_creds(
						$ref_user,
						$author_id,
						$get_creds,
						$this->prefs['get_'.$instance]['log'],
						$entity_id,
						$data,
						$this->mycred_type
					);
				}
			}
		}

		/**
		 * Add Settings
		 */
		public function preferences()
		{
			$prefs = $this->prefs;
			$likebtn_entities = _likebtn_get_entities(true, false, false); 
?>
<h3 class="nav-tab-wrapper likebtn_mycred_tab_labels" style="padding: 0">
    <?php foreach ($likebtn_entities as $tab_entity_name => $tab_entity_title): ?>
        <a class="nav-tab likebtn_mycred_tab_lbl_<?php echo $tab_entity_name; ?> <?php echo ('post' == $tab_entity_name ? 'nav-tab-active' : '') ?>" href="javascript:likebtnGotoTab('<?php echo $tab_entity_name ?>', '.likebtn_mycred_tab', '.likebtn_mycred_tab_', '.likebtn_mycred_tab_labels', 'likebtn_mycred_tab_lbl_');void(0);"><?php _e($tab_entity_title, 'likebtn-like-button'); ?></a>
    <?php endforeach ?>
</h3>

<?php foreach ($likebtn_entities as $entity_name => $entity_title): ?>
<div class="likebtn_mycred_tab postbox likebtn_mycred_tab_<?php echo $entity_name; ?> <?php if ($entity_name !== 'post'): ?>hidden<?php endif ?>" >
	<div class="inside">
		<label class="subheader"><?php echo _e( 'Points for Liking Content', 'likebtn-like-button' ); ?></label>
		<?php
			$instance = 'like_'.$entity_name;
			$this->settings_block($prefs, $instance);
		?>
		<label class="subheader"><?php _e( 'Points for Getting a Content Like', 'likebtn-like-button' ); ?></label>
		<?php
			$instance = 'get_like_'.$entity_name;
			$this->settings_block($prefs, $instance);
		?>
		<label class="subheader"><?php echo _e( 'Points for Disliking Content', 'likebtn-like-button' ); ?></label>
		<?php
			$instance = 'dislike_'.$entity_name;
			$this->settings_block($prefs, $instance);
		?>
		<label class="subheader"><?php _e( 'Points for Getting a Content Dislike', 'likebtn-like-button' ); ?></label>
		<?php
			$instance = 'get_dislike_'.$entity_name;
			$this->settings_block($prefs, $instance);
		?>
	</div>
</div>
<?php endforeach ?>
<?php
		}

		/**
		 * Output settings block
		 */
		function settings_block($prefs, $instance) {
			?>
<ol>
	<li>
		<div class="h2"><input type="text" name="<?php echo $this->field_name( array( $instance => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( $instance => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs[$instance]['creds'] ); ?>" size="8" autocomplete="off" /></div>
	</li>
	<li class="empty"></li>
	<li>
		<label for="<?php echo $this->field_id( array( $instance => 'log' ) ); ?>"><?php _e('Percent from voter\'s points balance added on voting to the points amount above', 'likebtn-like-button'); ?></label>
		<div class="h2"><input type="text" name="<?php echo $this->field_name( array( $instance => 'share' ) ); ?>" id="<?php echo $this->field_id( array( $instance => 'share' ) ); ?>" value="<?php echo (float)$prefs[$instance]['share']; ?>" size="8" autocomplete="off" /><small>%</small></div>
	</li>
	<li class="empty"></li>
	<li>
		<label for="<?php echo $this->field_id( array( $instance => 'limit' ) ); ?>"><?php _e( 'Limit', 'likebtn-like-button' ); ?></label>
		<?php echo $this->hook_limit_setting( $this->field_name( array( $instance => 'limit' ) ), $this->field_id( array( $instance => 'limit' ) ), $prefs[$instance]['limit'] ); ?>
	</li>	
	<li class="empty"></li>
	<li>
		<label for="<?php echo $this->field_id( array( $instance => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
		<div class="h2"><input type="text" name="<?php echo $this->field_name( array( $instance => 'log' ) ); ?>" id="<?php echo $this->field_id( array( $instance => 'log' ) ); ?>" value="<?php echo esc_attr( $prefs[$instance]['log'] ); ?>" class="long" autocomplete="off" /></div>
		<span class="description"><?php echo $this->available_template_tags( array( 'general' ) ); ?></span>
	</li>
</ol>
			<?php
		}
		
		/**
		 * Sanitise Preferences
		 */
		function sanitise_preferences($data) {
			global $likebtn_mycred_defaults;

			foreach ($likebtn_mycred_defaults as $key => $value) {
				if (isset( $data[$key]['limit'] ) && isset( $data[$key]['limit_by'] )) {
					$limit = sanitize_text_field($data[$key]['limit']);
					if ($limit == '') {
						$limit = 0;
					}
					$data[$key]['limit'] = $limit . '/' . $data[$key]['limit_by'];
					unset($data[$key]['limit_by']);
				}	
			}

			return $data;
		}
	}

}
