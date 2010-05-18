<?php
//check messages
$message = false;

if ( isset( $_GET['error'] ) && $_GET['error'] == 'na') { $message = 'You are not authorised to do that'; }
if ( isset( $_GET['error'] ) && $_GET['error'] == 'nc') { $message = 'There wasn\'t any content to save'; }

?>
<?php if ( $message != false ) { ?>
<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php } ?>
<div class="wrap">
	<h2><?php _e('New Location'); ?></h2>
	<form action="admin-post.php" method="post">
		<?php wp_nonce_field('post-location_add-location'); ?>
		<input type="hidden" name="action" value="post-location_add-location">

		<div id="poststuff">
			<div class="submitbox" id="submitpost">
				<div id="previewview"></div>
				<div class="inside">
					<p><?php _e('Please enter the information to add location info');?></p>
				</div>
				<p class="submit">
					<input name="publish" type="submit" class="button" id="publish" tabindex="5" accesskey="p" value="<?php _e('Publish location') ?>" />
				</p>
			</div>
			<div id="post-body">
				<div id="location-address">
					<h3><label for="title"><?php _e('Address') ?></label></h3>
					<div id="titlewrap">
						<input type="text" id="location_address" name="address" size="20" tabindex="1" autocomplete="off" value="" />
						<a class="buttom" id="location-find"><?php _e('Find'); ?></a>
					</div>
					<span>Ex: Rua do catete, 1500, centro, Rio de janeiro</span>
					<div id="location-map"></div>
				</div>
				<div id="titlediv">
					<h3><label for="title"><?php _e('Title') ?></label></h3>
					<div id="titlewrap">
						<input type="text" name="title" size="30" tabindex="1" id="title" autocomplete="off" />
					</div>
				</div>
				<div id="postdiv" class="postarea">
					<h3><?php _e('Location Content');?></h3>
					<textarea id="content" tabindex="2" name="content" cols="97" rows="4"></textarea>
				</div>
			</div>
	</div>



	</form>

</div>
