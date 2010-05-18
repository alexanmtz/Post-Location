<?php
/*
Plugin Name: Post location
Plugin URI: http://blog.alexandremagno.net
Description: Adiciona posts simples relacionados a localizacao em um mapa
Author: Alexandre Magno
Version: 0.1
Author URI: http://www.alexandremagno.net
Generated At: www.wp-fun.co.uk;
*/

if (!class_exists('post_location')) {
    class post_location	{


		/**
		* Constructor
		*/
		function __construct(){

			add_action("admin_menu", array($this,"add_admin_pages"));
			add_action("init",array($this,"location_init"));
			add_action('init', array(&$this,'flush_rewrite_rules'));
			add_action("template_redirect",array($this,'location_templates'));
			add_action("admin_init",array($this,'add_scripts'));
			add_action("admin_post_post-location_add-location", array($this,'new_location_handler'));
			add_action("admin_post_post-location_delete-location", array($this,'delete_location_handler'));
			add_action('generate_rewrite_rules', array(&$this,'add_rewrite_rules'));
			add_filter('query_vars', array(&$this,'queryvars') );

			//include the functions
			include('post-location-functions.php');

		}

		/**
		 * Register the new admin pages that are needed
		 *
		 */
		function add_admin_pages(){
				add_submenu_page('post-new.php', "Add Location", "Add Location", 10, 'post-location/post-location-new.php');
				add_submenu_page('edit.php', "Location", "Location", 10, 'post-location/manage-post-location.php');
		}

		/**
		 * Initialise the location object and fill with default content
		 */
		function location_init(){
			global $location_list, $wp_query;

			//set defaults
			$defaults = array(
				'numberposts' => -1, 'offset' => 0,
				'category' => 0, 'orderby' => 'post_date',
				'order' => 'DESC', 'include' => '',
				'exclude' => '', 'meta_key' => '',
				'meta_value' =>'', 'post_type' => 'location',
				'post_parent' => 0
			);

			$location_list = new wp_query($defaults);

		}

		/**
    	* Adds 'wizzard' to the list of query variables that WordPress looks for
    	*/
		function queryvars( $qvars ){
		  $qvars[] = 'location';
		  return $qvars;
		}

		/**
    	* Forces WordPress to reassess the rewrite rules
    	*/
		function flush_rewrite_rules() {
		   global $wp_rewrite;
		   $wp_rewrite->flush_rules();
		}

		/**
    	* Adds a rewrite rule to make sure that wizzards is a queryable variable
    	*/
		function add_rewrite_rules( $wp_rewrite ) {
			$new_rules = array(
			 'locations/(.+?)/?$' => 'index.php?location=' .
			   $wp_rewrite->preg_index(1) );

			$new_rules2 = array(
			 'locations/?$' => 'index.php?location=all');

		  $wp_rewrite->rules = $new_rules + $new_rules2 + $wp_rewrite->rules;
		}


		/**
		 * check if a location, or locations in general are being requested and divert to the appropriate template file
		 * @return
		 */
		function location_templates(){
			global $wp_query, $location_list;

			//set the conditionals
			if ( is_numeric($wp_query->get('location')) ) {
				$wp_query->is_location = true;
				} else { $wp_query->is_location = false; }

			if ( $wp_query->get('location') == 'all' ) {
				$wp_query->is_location_archive = true;
				} else { $wp_query->is_location_archive = false; }

			//if a single location is being requested then restrict the list
			if ( is_location() ) {

				$query_list = array('post__in' => array($wp_query->get('location')),"post_type" => 'location');
				$location_list = new wp_query($query_list);
			}

			//check for theme pages
			if ( is_location() ) {
				if (file_exists(TEMPLATEPATH . '/location.php')) {
					include(TEMPLATEPATH . '/locations.php');
					exit;
				} else {
					include(TEMPLATEPATH . '/index.php');
					exit;
				}
			}

			if ( is_location_archive() ) {
				if (file_exists(TEMPLATEPATH . '/locations.php')) {
					include(TEMPLATEPATH . '/locations.php');
					exit;
				} elseif (file_exists(TEMPLATEPATH . '/archive.php')) {
					include(TEMPLATEPATH . '/archive.php');
					exit;
				} else {
					include(TEMPLATEPATH . '/index.php');
					exit;
				}
			}

		}

		function add_scripts(){

			wp_enqueue_script('googlemaps','http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true_or_false&amp;key=ABQIAAAAhuBEr2Vp3FLuhCT4uuMTCRSx-QwnXZgcxXsgyWiJm1DJPXXp9BSMjcAE0x0vDiKjlzR4NH0fNpbghg');
			wp_enqueue_script('location-script' , '/wp-content/plugins/post-location/js/script.js' , array('jquery','googlemaps') , 1 );
			wp_enqueue_style('admin', '/wp-content/plugins/post-location/style_admin.css');
		}


		function new_location_handler(){

			check_admin_referer('post-location_add-location');

			if ( !current_user_can('edit_posts') ){
				$url = get_bloginfo('wpurl') . '/wp-admin/post-new.php?page=post-location/post-location-new.php&error=na';
				wp_redirect($url);
				die();
			}

			if ( !isset($_POST['content']) ) {
				$url = get_bloginfo('wpurl') . '/wp-admin/post-new.php?page=post-location/post-location-new.php&error=nc';
				wp_redirect($url);
				die();
			}

			//get the post vars
			$title = $_POST['title'];
			$content = $_POST['content'];
			//generic filter
			$repregex = '/[^\w\s\?\.,]/';

			//filter the post vars
			$title = preg_replace( $repregex , '_' , $title );
			$content = preg_replace( $repregex , '_' , $content );

			//send it for saving
			$location_id = $this->new_location( $title , $content );

			//do the redirect back to the display page, not the table page
			$url = get_bloginfo('wpurl') . '/wp-admin/edit.php?page=post-location/manage-post-location.php&added='.$location_id;
			wp_redirect($url);

		}

		function delete_location_handler(){

			check_admin_referer('post-location_delete-location');

			if ( !current_user_can('edit_posts') ){
				$url = get_bloginfo('wpurl') . '/wp-admin/edit.php?page=post-location/manage-post-location.php&error=na';
				wp_redirect($url);
				die();
			}

			if ( isset($_POST['delete_location']) ) {
				foreach( $_POST['delete_location'] as $location_id ){
					$this->delete_location( $location_id );
				}
			}

			//do the redirect back to the display page, not the table page
			$url = get_bloginfo('wpurl') . '/wp-admin/edit.php?page=post-location/manage-post-location.php&del=true';
			wp_redirect($url);
		}


		/**
		 *
		 * @return
		 */
function new_location( $title , $content )  {
	global $user_ID;
	//get the relevent post vars and save away
	// Create post object
	$my_post = array();
	$my_post['post_title'] = $title;
	$my_post['post_content'] = $content;
	$my_post['post_status'] = 'publish';
	$my_post['post_author'] = $user_ID;
	$my_post['post_type'] = 'location';
	$my_post['post_category'] = array(0);

	//Insert the post into the database
	return wp_insert_post( $my_post );
}


		function delete_location($id){

			//just do the deletion
			wp_delete_post($id);

			return $id;

		}



    }
}

//instantiate the class
if (class_exists('post_location')) {
	$post_location = new post_location();
}


?>