<?php

/**
 * Check if there are remaining locations to be ouputted
 * @return
 */
function have_location(){
	global $location_list;
	return $location_list->have_posts();
}

/**
 * Get the location
 * @return
 */
function the_location(){
	global $location , $location_list;

	$location_list->in_the_loop = true;
	$location = $location_list->next_post();
	setup_postdata($location);
}

function the_location_title(){
	global $location;

	echo apply_filters('location_title_out',$location->post_title);
}

function the_location_content(){
	global $location;

	echo apply_filters('location_content_out',$location->post_content);
}

function the_location_date(){
	global $location;

	echo apply_filters('location_date_out',$location->post_date);
}

function rewind_location(){
	global $location_list;

	return $location_list->rewind_posts();
}

function is_location(){
	global $wp_query;

	return $wp_query->is_location;
}

function is_location_archive(){
	global $wp_query;

	return $wp_query->is_location_archive;
}

function location_query($args = null){
	global $location_list;

	//set defaults
	$defaults = array(
		'numberposts' => -1, 'offset' => 0,
		'category' => 0, 'orderby' => 'post_date',
		'order' => 'DESC', 'include' => '',
		'exclude' => '', 'meta_key' => '',
		'meta_value' =>'', 'post_type' => 'location',
		'post_parent' => 0
	);

	$r = wp_parse_args($args, $defaults);

	$location_list = new wp_query($r);

}


?>
