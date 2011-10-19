<?php
/*
Plugin Name: Artists
Plugin URI: http://arcanepalette.com
Description: CPT plugin for artists, built for <a href="http://plaguemusic.com">Plague Music</a>.
Version: 1.0.2
Author: Arcane Palette Creative Design
Author URI: http://arcanepalette.com/
*/

/* changelog */
/*
   Version 1.0.1
	removed post thumbnail support (redundant with the profile pic)
	sanitized textarea to prevent malicious scripts from being embedded into artist pages
   Version 1.0.2
	fixed typo
*/

/* create the custom post type */
function post_type_artists() {
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
		'rewrite' => array("slug" => "artists"),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title','editor' ),	
		'exclude_from_search' => false
  );

  register_post_type( 'artists', $args );
}

add_action( 'init', 'post_type_artists', 0 );

/* create custom meta boxes */

function custom_meta_boxes_artists() {
    add_meta_box("artists-details", "Artist Details", "meta_cpt_artists", "artists", "normal", "low");
}

add_action('admin_menu', 'custom_meta_boxes_artists');

function meta_cpt_artists() {
    global $post;

	echo '<input type="hidden" name="artists_noncename" id="artists_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

   	echo '<label for="profile_pic">Profile Pic</label><br />';

	//ajax upload
	$wud = wp_upload_dir();

?>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			var uploader = new qq.FileUploader({
				element: document.getElementById('profile_pic_upload'),
				action: '<?php echo get_bloginfo('siteurl'); ?>/',
				onComplete: function (id,fileName,responseJSON) {
					if(responseJSON.success == true)
						jQuery('#profile_pic').val('<?php echo $wud["url"]; ?>/'+fileName);
				}
			});           
		});	
		</script>
	<input style="width: 55%;" id="profile_pic" name="profile_pic" value="<?php echo get_post_meta($post->ID, 'profile_pic', true); ?>" type="text" /><div id="profile_pic_upload"></div>
	<?php	

	echo '<label for="website_url">Website URL</label><br />';
	echo '<input style="width: 55%;" type="text" name="website_url" value="'.get_post_meta($post->ID, 'website_url', true).'" /><br /><br />';
	
	echo '<label for="facebook_url">Facebook Page URL (full URL)</label><br />';
	echo '<input style="width: 55%;" type="text" name="facebook_url" value="'.get_post_meta($post->ID, 'facebook_url', true).'" /><br /><br />';	

	echo '<label for="twitter_id">Twitter Name (username only)</label><br />';
	echo '<input style="width: 55%;" type="text" name="twitter_id" value="'.get_post_meta($post->ID, 'twitter_id', true).'" /><br /><br />';		

	echo '<label for="myspace_page">MySpace Page (username/page name only)</label><br />';
	echo '<input style="width: 55%;" type="text" name="myspace_page" value="'.get_post_meta($post->ID, 'myspace_page', true).'" /><br /><br />';	

	echo '<label for="reverbnation_page">ReverbNation Page (page name only)</label><br />';
	echo '<input style="width: 55%;" type="text" name="reverbnation_page" value="'.get_post_meta($post->ID, 'reverbnation_page', true).'" /><br /><br />';

	echo '<label for="soundcloud_name">SoundCloud Page (username only)</label><br />';
	echo '<input style="width: 55%;" type="text" name="soundcloud_name" value="'.get_post_meta($post->ID, 'soundcloud_name', true).'" /><br /><br />';	

	echo '<label for="bandcamp_page">BandCamp Page (full URL)</label><br />';
	echo '<input style="width: 55%;" type="text" name="bandcamp_page" value="'.get_post_meta($post->ID, 'bandcamp_page', true).'" /><br /><br />';			

	echo '<label for="alonetone_id">Alonetone Name (username only)</label><br />';
	echo '<input style="width: 55%;" type="text" name="alonetone_id" value="'.get_post_meta($post->ID, 'alonetone_id', true).'" /><br /><br />';	

	echo '<label for="rpm_challenge">RPM Challenge page (full URL)</label><br />';
	echo '<input style="width: 55%;" type="text" name="rpm_challenge" value="'.get_post_meta($post->ID, 'rpm_challenge', true).'" /><br /><br />';			
	
	echo '<label for="press">Press</label><br />Post any reviews here.  HTML is <em>not</em> allowed.<br />';
	echo '<textarea style="width: 55%;" rows="20" cols="50" name="press" />'.htmlspecialchars(get_post_meta($post->ID, 'press', true)).'</textarea><br /><br />';

	echo '<label for="plague_slug">Plague Artist slug</label><br />If you are a Plague Music artist with a release on the Releases page, enter the slug for your artist releases from <a href="http://plaguemusic.com/a/wp-admin/edit-tags.php?taxonomy=artist&post_type=releases">this page</a>.<br />';
	echo '<input style="width: 55%;" type="text" name="plague_slug" value="'.get_post_meta($post->ID, 'plague_slug', true).'" /><br /><br />';			
	
}

/* deal with uploading image */
if(isset ($_GET["qqfile"]) && strlen($_GET["qqfile"]))
{
	$pluginurl = WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__));
	include(WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)) . '/' . 'includes/upload.php');
	$wud = wp_upload_dir();

	/* list of valid extensions */
	$allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'ico');

	/* max file size in bytes */
	$sizeLimit = 6 * 1024 * 1024;

	$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
	$result = $uploader->handleUpload($wud['path'].'/',true);
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	exit;
}


function artists_uploader_scripts() {
	
	$pluginurl = WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__));

	wp_enqueue_script('fileuploader', $pluginurl.'/includes/fileuploader.js',array('jquery'));
	wp_enqueue_style('fileuploadercss',$pluginurl.'/css/fileuploader.css');
}

function artists_uploader_styles() {
	$pluginurl = WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__));
	
	wp_enqueue_style('thickbox');
	wp_enqueue_style('fileuploadercss', $pluginurl.'/css/fileuploader.css');
}

add_action('admin_print_scripts', 'artists_uploader_scripts');
add_action('admin_print_styles', 'artists_uploader_styles');

/* When the post is saved, saves our product data */
function artists_save_product_postdata($post_id, $post) {
   	if ( !wp_verify_nonce( $_POST['artists_noncename'], plugin_basename(__FILE__) )) {
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
	foreach ($_POST as $key => $value) {
        $mydata[$key] = $value;
    }

	/* Add values of $mydata as custom fields */
	foreach ($mydata as $key => $value) {
		if( $post->post_type == 'revision' ) return;
		$value = implode(',', (array)$value);
		if(get_post_meta($post->ID, $key, FALSE)) {
			update_post_meta($post->ID, $key, $value);
		} else {
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key);
	}
}

add_action('save_post', 'artists_save_product_postdata', 1, 2); // save the custom fields

add_action( 'admin_head', 'artists_icon' );
function artists_icon() {
    ?>
    <style type="text/css" media="screen">
        #menu-posts-artists .wp-menu-image {
            background: url(<?php bloginfo('wpurl'); ?>/wp-content/plugins/artists/images/microphone.png) no-repeat 6px -17px !important;
        }
	#menu-posts-artists:hover .wp-menu-image, #menu-posts-artists.wp-has-current-submenu .wp-menu-image {
			background: url(<?php bloginfo('wpurl'); ?>/wp-content/plugins/artists/images/microphone.png) no-repeat 6px 7px !important;
        }
    </style>

<?php } 

add_action('admin_head', 'artists_header');
function artists_header() {
        global $post_type;
	?>
	<style>
	<?php if (($_GET['post_type'] == 'artists') || ($post_type == 'artists')) : ?>
	#icon-edit { background: url(<?php bloginfo('wpurl'); ?>/wp-content/plugins/artists/images/music.png) no-repeat!important; }		
	<?php endif; ?>
        </style>
    <?php } ?>
