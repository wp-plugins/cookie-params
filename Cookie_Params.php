<?php

/*
Plugin Name: Cookie Params
Description: Ever wanted to save the query string params from your advertising as Javascript cookies because your page is heavily cached? Well, now you can...
Plugin URI: http://www.skyrockinc.com/cookie-params/
Version: 0.2
Author: hypedtext
Author URI: http://www.skyrockinc.com/
*/

class Cookie_Params {

	private $optid = 'cook';

	function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( &$this, 'add_option_page' ) );  
		} else {
			//add_action( 'wp_enqueue_scripts', array( &$this, str_replace( array( "\r", "\n" ), '', 'inline_js' ) ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'inline_js' ) );
		}
		if ( get_option( $this->optid . '_option_02' ) == "On" ) {
			add_action( 'wp_ajax_debug', array( &$this, 'debug' ) );
			add_action( 'wp_ajax_nopriv_debug', array( &$this, 'debug' ) ); 
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_debug_scripts' ) );
		}
	}
	
	function CookieParams () {
		$this->__construct();
	}
	
	function inline_js() {
		$params = get_option( $this->optid . '_option_01' );
		$add_cookies = get_option( $this->optid . '_option_03' );
		$add_js = get_option( $this->optid . '_option_05' );
		?><script type="text/javascript"> function getParameterByName(name) { name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]"); var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"), results = regex.exec(location.search); return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " ")); } var checklist = '<?php echo $params; ?>'.split( ',' ); for ( i = 0; i < checklist.length; i++ ) { var check = getParameterByName( checklist[i] ); if ( check ) { window[checklist[i]] = check; document.cookie=checklist[i] + '=' + check + "; path=/"; } } <?php if ( strlen( $add_cookies ) > 0 ) { ?> var add_cookies = '<?php echo $add_cookies; ?>'.split( ',' ); for ( i = 0; i < add_cookies.length; i++ ) { document.cookie=add_cookies[i] + "; path=/"; } <?php } ?> <?php if ( isset( $add_js ) ) { echo str_replace( array( "\r", "\n" ), '', $add_js ); } ?> </script> <?php
	}

	function debug() {
		print_r( $_COOKIE );
		die();
	}

	function enqueue_debug_scripts () {
			wp_enqueue_script( 'debug', plugins_url( '/js/debug.js', __FILE__ ) );
			wp_localize_script( 'debug', 'debug', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	function add_option_page() {
		if ( function_exists( 'add_options_page' ) ) {
			add_options_page( 'Cookie Params', 'Cookie Params', 'manage_options', __FILE__, array( &$this, 'options_page' ) );
		}
	}

	function options_page() {
		if ( isset( $_POST['update'] ) ) {
			echo '<div id="message" class="updated fade"><p><strong>';
			$option_01 = htmlentities( stripslashes( $_POST[$this->optid . '_option_01'] ) , ENT_COMPAT );
			update_option( $this->optid . '_option_01', $option_01 );
			$option_02 = htmlentities( stripslashes( $_POST[$this->optid . '_option_02'] ) , ENT_COMPAT );
			update_option( $this->optid . '_option_02', $option_02 );
			$option_03 = htmlentities( stripslashes( $_POST[$this->optid . '_option_03'] ) , ENT_COMPAT );
			update_option( $this->optid . '_option_03', $option_03 );
			$option_05 = htmlentities( stripslashes( $_POST[$this->optid . '_option_05'] ) , ENT_COMPAT );
			update_option( $this->optid . '_option_05', $option_05 );
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
								<textarea name="<?php echo $this->optid; ?>_option_01" rows="4" cols="66"><?php echo get_option( $this->optid . '_option_01' ); ?></textarea
							</td>
						</tr>
						<tr class="alternate">
							<td class="plugin-title" width="20%">
								<strong>Debug</strong>
								<span>Print cookie data to web inspector console from PHP using WP localize stuff.</span>
							</td>
							 <td align="left" width="80%">
								<input type="radio" name="<?php echo $this->optid; ?>_option_02" value="On" <?php if ( get_option( $this->optid . '_option_02' ) == "On" ) { echo "checked"; } ?>>On&nbsp;
								<input type="radio" name="<?php echo $this->optid; ?>_option_02" value="Off" <?php if ( get_option( $this->optid . '_option_02' ) == "Off" ) { echo "checked"; } ?>>Off&nbsp;
							</td>
						</tr>
						<tr>
							<td class="plugin-title" width="20%"><strong>Additional Cookies</strong>
								<span>Set additional cookies here using standard JS: <pre><code>key1=value1,key2=value2,key3=value3</code></pre></span>
							</td>
							<td width="80%">
								<textarea name="<?php echo $this->optid; ?>_option_03" rows="4" cols="66"><?php echo get_option( $this->optid . '_option_03' ); ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="plugin-title" width="20%"><strong>Additional JS</strong>
								<span>Set additional inline JS here.</span>
							</td>
							<td width="80%">
								<textarea name="<?php echo $this->optid; ?>_option_05" rows="30" cols="66"><?php echo get_option( $this->optid . '_option_05' ); ?></textarea>
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
?>
