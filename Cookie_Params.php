<?php

/*
Plugin Name: Cookie Params
Description: Ever wanted to save the query string params from your advertising as Javascript cookies because your page is heavily cached? Well, now you can...
Plugin URI: http://www.skyrockinc.com/cookie-params/
Version: 0.1
Author: hypedtext
Author URI: http://www.skyrockinc.com/
*/

class Cookie_Params {

	function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( &$this, 'add_option_page' ) );  
		} else {
		}
		if ( get_option( 'debug' ) == "On" ) {
			add_action( 'wp_ajax_debug', array( &$this, 'debug' ) );
			add_action( 'wp_ajax_nopriv_debug', array( &$this, 'debug' ) ); 
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		}
		add_action( 'wp_enqueue_scripts', array( &$this, 'add_js' ) );
	}
	
	function CookieParams () {
		$this->__construct();
	}
	
	function add_js() {
		$params = get_option( 'params' );
		?>
		<script type="text/javascript">

			function getParameterByName(name) {
				name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
				var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
				results = regex.exec(location.search);
				return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
			}

			var checklist = '<?php echo $params; ?>'.split( ',' );
			for ( i = 0; i < checklist.length; i++ ) { 
				var check = getParameterByName( checklist[i] );
				if ( check ) {
					document.cookie=checklist[i] + '=' + check + "; path=/";
				}
			}
		</script>
		<?php
	}

	function enqueue_scripts () {
			wp_enqueue_script( 'check', plugins_url( '/js/check.js', __FILE__ ), array( 'jquery' ) );
			wp_localize_script( 'check', 'check', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	function debug() {
		print_r( $_COOKIE );
	}

	function add_option_page() {
		if ( function_exists( 'add_options_page' ) ) {
			add_options_page( 'Cookie Params', 'Cookie Params', 'manage_options', __FILE__, array( &$this, 'options_page' ) );
		}
	}

	function options_page() {
		if ( isset( $_POST['update'] ) ) {
			echo '<div id="message" class="updated fade"><p><strong>';
			$option_01 = htmlentities( stripslashes( $_POST['params'] ) , ENT_COMPAT );
			update_option( 'params', $option_01 );
			$option_02 = htmlentities( stripslashes( $_POST['debug'] ) , ENT_COMPAT );
			update_option( 'debug', $option_02 );
			echo 'Options Updated!';
			echo '</strong></p></div>';
		}
		?>
		<div class="wrap">
			<h2>Cookie Params</h2>
			<hr>
			<br>
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
				<input type="hidden" name="update" id="update" value="true" />
				<fieldset class="options">
					<table class="wp-list-table widefat">
						<tr>
							<td class="plugin-title" width="20%"><strong>Params</strong>
								<span>A comma seperated string of the params you want to store in a Javascript cookie.</span>
							</td>
							<td width="80%">
								<textarea name="params" rows="4" cols="66"><?php echo get_option( 'params' ); ?></textarea
							</td>
						</tr>
						<tr class="alternate">
							<td class="plugin-title" width="20%">
								<strong>Debug</strong>
								<span>Print cookie data to web inspector console from PHP using WP localize stuff.</span>
							</td>
							 <td align="left" width="80%">
								<input type="radio" name="debug" value="On" <?php if ( get_option('debug') == "On" ) { echo "checked"; } ?>>On&nbsp;
								<input type="radio" name="debug" value="Off" <?php if ( get_option('debug') == "Off" ) { echo "checked"; } ?>>Off&nbsp;
							</td>
						</tr>
					</table>
				</fieldset>
				<div class="submit">
					<input type="submit" class="button-primary" name="update" value="Update options" />
				</div>
				</form>
		</div>
		<?php
	}

}

$Cookie_Params = new Cookie_Params;
