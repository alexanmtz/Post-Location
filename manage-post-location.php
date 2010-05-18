<?php

//get all locations
$location_defaults = array(
				'numberposts' => -1, 'offset' => 0,
				'category' => 0, 'orderby' => 'post_date',
				'order' => 'DESC', 'include' => '',
				'exclude' => '', 'meta_key' => '',
				'meta_value' =>'', 'post_type' => 'location',
				'post_parent' => 0
			);

$location_list = get_posts($location_defaults);

//check messages
$message = false;

if ( isset( $_GET['error'] ) && $_GET['error'] == 'na') { $message = 'You are not authorised to do that'; }
if ( isset( $_GET['del'] ) && $_GET['del'] == 'true') { $message = 'Location Deleted'; }
if ( isset( $_GET['added'] ) && is_numeric( $_GET['added'] ) ) {
	foreach( $location_list as $location ){
		if ( $location->ID == $_GET['added'] ){
			$message = 'Location Added:<br/>';
			$message .= $location->post_title . '<br/>';
			$message .= $location->post_content;
		}
	}
}

?>
<?php if ( $message != false ) { ?>
<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php } ?>
<div class="wrap">
	<h2><?php _e('Manage Locations'); ?></h2>
	<form action="admin-post.php" method="post">
		<?php wp_nonce_field('post-location_delete-location'); ?>
		<input type="hidden" name="action" value="post-location_delete-location">
		<div class="tablenav">
				<div class="alignleft">
					<input value="Delete" name="delete_location" class="button-secondary delete" type="submit">
				</div>
				</div>
				<br class="clear">
				<table class="widefat">
					<thead>
						<tr valign="top">
							<th class="check-column" scope="col"></th>
							<th scope="col"><?php _e('Date'); ?></th>
							<th scope="col"><?php _e('Title'); ?></th>
							<th scope="col"><?php _e('Content'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach( $location_list as $location ) { ?>
						<tr>

							<th scope="row" class="check-column <?php if ($count == 1){echo 'alternate';} ?>"><input type="checkbox" valign="bottom" value="<?php echo $location->ID; ?>" name="delete_location[]"/></th>
							<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top"><?php echo $location->post_date; ?></td>
							<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top"><?php echo $location->post_title; ?></td>
							<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top"><?php echo $location->post_content; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
	</form>

</div>