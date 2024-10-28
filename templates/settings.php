<div class="wrap">
<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (isset($_POST["update_settings"])):
	
	check_admin_referer( 'update_attributio_settings' );

	$attributio_property = isset($_POST["attributio_property"]) ? $_POST["attributio_property"] : '';
	$attributio_custom_hit = isset($_POST["attributio_custom_hit"]) ? $_POST["attributio_custom_hit"] : '';

	// Validate and Sanitize
	$attributio_property = !empty($attributio_property) ? sanitize_text_field(strval($attributio_property)) : '';
	
	global $wp_version;
	if ( version_compare( $wp_version, '4.7-z', '>=' ) ) {
		$attributio_custom_hit = !empty($attributio_custom_hit) ? sanitize_textarea_field($attributio_custom_hit) : '';
	} else {
		$attributio_custom_hit = !empty($attributio_custom_hit) ? wp_kses_post($attributio_custom_hit) : '';
	}

	update_option("attributio_property", $attributio_property);
	update_option("attributio_custom_hit", $attributio_custom_hit);

?> 
<div id="message" class="updated">Settings saved</div>  
<?php endif; ?>

<h1 class="wp-heading-inline"><?php _e('Attributio Settings', 'Attributio'); ?></h1>
<hr class="wp-header-end">
<div class="panel">
	<div class="container">
		<div class="column">
			<h3>Configure</h3>
			<form method="POST" action="">  
				<?php wp_nonce_field( 'update_attributio_settings' ); ?>

				<label for="attributio_property">  
					<?php _e('Google Analytics Property ID', 'attributio'); ?>
				</label>
				<br>
				<input type="text" id="attributio_property" name="attributio_property" value='<?php echo get_option("attributio_property"); ?>' />
				<br>
				<label for="attributio_custom_hit">  
					<?php _e('Custom Hit Tag', 'attributio'); ?>
				</label> 
				<br>
				<textarea rows="20" style="width:100%;" id="attributio_custom_hit" name="attributio_custom_hit" ><?php echo stripcslashes(get_option("attributio_custom_hit")); ?></textarea>

				<p>  
					<input type="hidden" name="update_settings" value="Y" />  
					<input type="submit" value="Save settings" class="button-primary"/>  
				</p>  
			</form>
		</div>
		<div class="column">
			<h3>Instructions</h3>
			<p>To enable this plugin's proxy to avoid adblockers from blocking Google Analytics or Google Tag Manager, add your Google Analytics Property ID in the form.</p>
			<p>When this plugin detects that Google Analytics is being blocked, the standard pageview hit will be sent to Google Analytics through a proxy.</p>
			<p>To add additional tracking tags like events or tracking custom dimensions like <code>clientId</code>, add it to the Custom Hit Tag field in the form like the example below.</p>
<pre>var clientId;
ga(function(tracker) {
  clientId = tracker.get('clientId');
  tracker.set({dimension1: clientId});
  tracker.send('event', {
    nonInteraction: true
  });
});</pre>
			<p>To configure your analytics to colect custom dimensions for visitors and users for attribution modeling, use our app: <a href="https://attribut.io">Attributio</a></p>
		</div>
	</div>
</div>

<style>
.panel {
  position: relative;
  overflow: auto;
  margin: 16px 0;
  padding: 16px 10px 0;
  border: 1px solid #e5e5e5;
  box-shadow: 0 1px 1px rgba(0,0,0,.04);
  background: #fff;
  font-size: 13px;
  line-height: 2.1em;
}
.container {
	clear: both;
	position: relative;
}
.column {
	width: 46%;
	padding: 0 2% 0 2%;
	margin: 0 0 1em 0;
	min-width: 400px;
	float: left;
}
pre {
	background: rgba(0,0,0,.07);
	line-height: 1.5em;
	padding: 15px;
}

</style>