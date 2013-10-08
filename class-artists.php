<?php

	class Plague_Artist {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   2.0.0
	 *
	 * @var     string
	 */
	protected $version = '2.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'plague-artists';

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     2.0.0
	 */
	private function __construct() {
		// all the actions go here

		add_action( 'init', array( $this, 'post_type_artists' ), 0 );
		add_action('admin_menu', array( $this, 'custom_meta_boxes_artists' ) );
		add_action('save_post', array( $this, 'artists_save_product_postdata' ), 1, 2); // save the custom fields
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles') );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     2.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/* create the custom post type */
	public function post_type_artists() {
	    $labels = array(
			'name' => _x('Artists', 'post type general name'),
			'singular_name' => _x('Artist', 'post type singular name'),
			'add_new' => _x('Add New', 'product'),
			'add_new_item' => __('Add New Artist'),
			'edit_item' => __('Edit Artist'),
			'edit' => _x('Edit', 'artists'),
			'new_item' => __('New Artist'),
			'view_item' => __('View Artist'),
			'search_items' => __('Search Artist'),
			'not_found' =>  __('No artists found'),
			'not_found_in_trash' => __('No artists found in Trash'),
			'view' =>  __('View Artist'),
			'parent_item_colon' => ''
	  );
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array("slug" => "artist"),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			'exclude_from_search' => false
	  );

	  register_post_type( 'plague-artist', $args );
	}

	/* create custom meta boxes */

	public function custom_meta_boxes_artists() {
	    add_meta_box("artist-details", "Artist Details", array( $this, 'meta_cpt_artists' ), "plague-artist", "normal", "low");
	}

	public function meta_cpt_artists() {
	    global $post;

		echo '<input type="hidden" name="artists_noncename" id="artists_noncename" value="' .
		wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

		echo '<label for="website_url">Website URL</label><br />';
		echo '<input style="width: 55%;" type="text" name="website_url" value="' . mysql_real_escape_string( get_post_meta($post->ID, 'website_url', true) ) . '" /><br /><br />';

		echo '<label for="facebook_url">Facebook Page URL (full URL)</label><br />';
		echo '<input style="width: 55%;" type="text" name="facebook_url" value="' . mysql_real_escape_string( get_post_meta($post->ID, 'facebook_url', true) ) . '" /><br /><br />';

		echo '<label for="twitter_id">Twitter Name (username only)</label><br />';
		echo '<input style="width: 55%;" type="text" name="twitter_id" value="' . wp_kses( get_post_meta($post->ID, 'twitter_id', true), array() ) . '" /><br /><br />';

		echo '<label for="myspace_page">MySpace Page (username/page name only)</label><br />';
		echo '<input style="width: 55%;" type="text" name="myspace_page" value="' . wp_kses( get_post_meta($post->ID, 'myspace_page', true), array() ) . '" /><br /><br />';

		echo '<label for="reverbnation_page">ReverbNation Page (page name only)</label><br />';
		echo '<input style="width: 55%;" type="text" name="reverbnation_page" value="' . wp_kses( get_post_meta($post->ID, 'reverbnation_page', true), array() ) . '" /><br /><br />';

		echo '<label for="soundcloud_name">SoundCloud Page (username only)</label><br />';
		echo '<input style="width: 55%;" type="text" name="soundcloud_name" value="' . wp_kses( get_post_meta($post->ID, 'soundcloud_name', true), array() ) . '" /><br /><br />';

		echo '<label for="bandcamp_page">BandCamp Page (full URL)</label><br />';
		echo '<input style="width: 55%;" type="text" name="bandcamp_page" value="' . mysql_real_escape_string( get_post_meta($post->ID, 'bandcamp_page', true) ) . '" /><br /><br />';

		echo '<label for="alonetone_id">Alonetone Name (username only)</label><br />';
		echo '<input style="width: 55%;" type="text" name="alonetone_id" value="' . wp_kses( get_post_meta($post->ID, 'alonetone_id', true), array() ) . '" /><br /><br />';

		echo '<label for="rpm_challenge">RPM Challenge page (full URL)</label><br />';
		echo '<input style="width: 55%;" type="text" name="rpm_challenge" value="' . mysql_real_escape_string( get_post_meta($post->ID, 'rpm_challenge', true) ) . '" /><br /><br />';

		echo '<label for="press">Press</label><br />Post any reviews here.<br />';
		echo wp_kses_post( wp_editor( get_post_meta($post->ID, 'press', true), 'press', array( 'media_buttons' => false, 'teeny' => true ) ) ) . '<br /><br />';

		echo '<label for="plague_slug">Plague Artist slug</label><br />If you are a Plague Music artist with a release on the Releases page, enter the slug for your artist releases from <a href="http://plaguemusic.com/a/wp-admin/edit-tags.php?taxonomy=artist&post_type=releases">this page</a>.<br />';
		echo '<input style="width: 55%;" type="text" name="plague_slug" value="' . wp_kses( get_post_meta($post->ID, 'plague_slug', true), array() ) . '" /><br /><br />';

	}

	/* When the post is saved, saves our product data */
	public function artists_save_product_postdata($post_id, $post) {
		$nonce = isset( $_POST['artists_noncename'] ) ? $_POST['artists_noncename'] : 'all the pigs, all lined up';
		if ( !wp_verify_nonce( $nonce, plugin_basename(__FILE__) )) {
			return $post->ID;
		}

		/* confirm user is allowed to save page/post */
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post->ID ))
			return $post->ID;
		} else {
			if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;
		}

		/* ready our data for storage */
		$meta_keys = array(
			'website_url' => 'url',
			'facebook_url' => 'url',
			'twitter_id' => 'text',
			'myspace_page' => 'text',
			'reverbnation_page' => 'text',
			'soundcloud_name' => 'text',
			'bandcamp_page' => 'url',
			'alonetone_id' => 'text',
			'rpm_challenge' => 'url',
			'press' => 'text',
			'plague_slug' => 'slug'
		);

		/* Add values of $mydata as custom fields */
		foreach ($meta_keys as $meta_key => $type) {
			if( $post->post_type == 'revision' )
				return;
			if ( isset( $_POST[ $meta_key ] ) ) {
				if ( $type == 'text' ) {
					$value = wp_kses_post( $_POST[ $meta_key ] );
				}
				if ( $type == 'embed' ) {
					$kses_allowed = array_merge(wp_kses_allowed_html( 'post' ), array('iframe' => array(
						'src' => array(),
						'style' => array(),
						'width' => array(),
						'height' => array(),
						'scrolling' => array(),
						'frameborder' => array()
						)));
					$value = wp_kses( $_POST[ $meta_key ], $kses_allowed );
				}
				if ( $type == 'url' ) {
					$value = htmlspecialchars( $_POST[ $meta_key ] );
				}
				if ( $type == 'slug' ) {
					$value = sanitize_title( $_POST[ $meta_key ] );
				}

				update_post_meta( $post->ID, $meta_key, $value );
			} else {
				delete_post_meta( $post->ID, $meta_key );
			}
		}
	}

	public function admin_styles() {
		wp_enqueue_style( 'fontawesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ), array(), $this->version );
		wp_enqueue_style( 'artist-admin-css', plugins_url( 'css/artists-admin.css', __FILE__ ), array(), $this->version );
	}
}