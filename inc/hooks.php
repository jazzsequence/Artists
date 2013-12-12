<?php

add_filter( 'the_content', 'plague_filter_artist_single_content', 20 );

function plague_filter_artist_single_content( $content ) {
	global $post;

	$profile_pic = null;
	$website_url = null;
	$facebook_url = null;
	$twitter_id = null;
	$myspace_page = null;
	$reverbnation_page = null;
	$soundcloud_page = null;
	$bandcamp_page = null;
	$alonetone_id = null;
	$rpm_challenge = null;
	$releases_heading = null;
	$the_release = null;
	$releases_wrapper_open = null;
	$releases_wrapper_close = null;
	$press = null;
	if ( 'plague-artist' == get_post_type() ) {
		$row_open = '<div class="row">';
		$row_close = '</div> <!-- closes row -->';
		$wrapper_open = '<div class="row artist-wrapper">';
		$left_col_open = '<div class="art-col-1 col-md-3 artist-meta">';
		if ( has_post_thumbnail( $post->ID ) ) {
			$profile_pic = '<div class="profile-pic row img-responsive">' . get_the_post_thumbnail( $post->ID, 'medium' ) . '</div>';
		}
		if ( get_post_meta( $post->ID, 'website_url', true ) ) {
			$website_url = '<a href="' . esc_url( get_post_meta( $post->ID, 'website_url', true ) ) . '" target="_blank"><i class="plague-i-link"></i></a>';
		}
		if ( get_post_meta( $post->ID, 'facebook_url', true ) ) {
			$facebook_url = '<a href="' . esc_url( get_post_meta( $post->ID, 'facebook_url', true ) ) . '" target="_blank"><i class="plague-i-facebook"></i></a>';
		}
		if ( get_post_meta( $post->ID, 'twitter_id', true ) ) {
			$twitter_id = '<a href="http://twitter.com/' . esc_attr( get_post_meta( $post->ID, 'twitter_id', true ) ) . '" target="_blank"><i class="plague-i-twitter"></i></a>';
		}
		if ( get_post_meta( $post->ID, 'myspace_page', true ) ) {
			$myspace_page = '<a href="http://myspace.com/' . esc_attr( get_post_meta( $post->ID, 'myspace_page', true ) ) . '" target="_blank"><i class="plague-i-myspace"></i></a>';
		}
		if ( get_post_meta( $post->ID, 'reverbnation_page', true ) ) {
			$reverbnation_page = '<a href="http://reverbnation.com/' . esc_attr( get_post_meta( $post->ID, 'reverbnation_page', true ) ) . '" target="_blank"><i class="plague-i-reverbnation"></i></a>';
		}
		if ( get_post_meta( $post->ID, 'soundcloud_page', true ) ) {
			$soundcloud_page = '<a href="http://soundcloud.com/' . esc_attr( get_post_meta( $post->ID, 'soundcloud_page', true ) ) . '" target="_blank"><i class="plague-i-soundcloud></i></a>';
		}
		if ( get_post_meta( $post->ID, 'bandcamp_page', true ) ) {
			$bandcamp_page = '<a href="' . esc_url( get_post_meta( $post->ID, 'bandcamp_page', true ) ) . '" target="_blank"><i class="plague-i-bandcamp"></i></a>';
		}
		if ( get_post_meta( $post->ID, 'alonetone_id', true ) ) {
			$alonetone_id = '<a href="http://alonetone.com/' . esc_attr( get_post_meta( $post->ID, 'alonetone_id', true ) ) . '" target="_blank"><i class="plague-i-alonetone"></i></a>';
		}
		if ( get_post_meta( $post->ID, 'rpm_challenge', true ) ) {
			$rpm_challenge = '<a href="' . esc_url( get_post_meta( $post->ID, 'rpm_challenge', true ) ) . '" target="_blank"><i class="plague-i-rpm"></i></a>';
		}
		$left_col_close = '</div><!-- closes left column -->';
		$right_col_open = '<div class="art-col-2 col-md-9">';
		$content = '<div class="artist-bio">' . $content . '</div>';
		if ( get_post_meta( $post->ID, 'press', true ) ) {
			$press = '<div class="well well-sm press">' . wpautop( wp_kses_post( get_post_meta( $post->ID, 'press', true ) ) ) . '</div>';
		}
		$right_col_close = '</div><!-- closes right column -->';
		$wrapper_close = '</div><!-- closes wrapper -->';

		// now do releases stuff if the releases plugin exists
		if ( class_exists( 'Album_Releases' ) ) {
			$artist_slug = $post->post_name;
			$artist_name = get_the_title( $post->ID );
			$releases_wrapper_open = '<div class="row releases-wrapper">';
			$releases_wrapper_close = '</div><!-- closes releases wrapper -->';
			global $wp_query;

			$args = array(
				'post_type' => 'plague-release',
				'artist' => $artist_slug,
				'post_status' => 'publish',
				'posts_per_page' => -1

			);

			$release_query = new WP_Query($args);
			if ( $release_query->have_posts() ) :
				$releases_heading = '<h2>' . sprintf( __( 'Releases by %s', 'plague-artists' ), $artist_name ) . '</h2>';
				while ( $release_query->have_posts() ) : $release_query->the_post();

					$the_release .= '<article class="entry row">';
					if ( has_post_thumbnail( $post->ID ) ) {
						$the_release .= '<div class="col-md-3 art-col-1"><div class="pull-left alignleft">' . get_the_post_thumbnail( $post->ID ) . '</div></div>';
					}
					$the_release .= '<div class="artist-release col-md-9 art-col-2">';
					$the_release .= '<h3 class="the_title release-title"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a></h3>';
					$the_release .= get_the_excerpt();
					$the_release .= '</div>';
					$the_release .= '</article>';

			endwhile; endif; wp_reset_query();

		}

		// return new content
		return $wrapper_open . $left_col_open . $profile_pic . $row_open . $website_url . $facebook_url . $twitter_id . $myspace_page . $reverbnation_page . $soundcloud_page . $bandcamp_page . $alonetone_id . $rpm_challenge . $row_close . $left_col_close . $right_col_open . $content . $press . $right_col_close . $wrapper_close . $releases_wrapper_open . $releases_heading . $row_open . $the_release . $releases_wrapper_close . $row_close;
	} else {
		// return old content
		return $content;
	}
}