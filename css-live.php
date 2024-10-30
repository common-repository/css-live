<?php
/**
 * Plugin Name: Live CSS
 * Plugin URI: https://wpion.com
 * Description: Add CSS and LESS on the front end.
 * Author: wpIon
 * Version: 1.3
 * License: GPLv2 or later
 * Author URI: https://wpion.com
 */
define('TD_LIVE_CSS_URL', plugins_url('css-live'));
define('TD_LIVE_CSS_VERSION', '1.1');

require_once "includes/td_live_css_ajax.php";
require_once "includes/td_live_css_controller.php";
require_once "includes/td_live_css_css_storage.php";
require_once "includes/td_live_css_util.php";





add_action( 'wp_head', 'td_live_css_inject_css', 10 );
function td_live_css_inject_css() {
	$css_parts = td_live_css_controller::detect_css_parts();
	$css_buffer = '';
	foreach ($css_parts as $css_part) {
		$css_buffer .= td_live_css_css_storage::get($css_part['detect_key'], 'css');
	}

	if (!empty($css_buffer)) {
		echo '	<style id="tdw-css-placeholder">' . $css_buffer . '</style>';
	}
}




add_action( 'wp_footer', 'td_live_css_inject_editor', 100000 );
function td_live_css_inject_editor() {
	$css_parts = td_live_css_controller::detect_css_parts();
	?>



	<div id="tdw-css-writer" style="display: none" class="tdw-css-writer-wrap">
		<div class="tdw-tabs-wrap">
			<?php
			$is_first_tab_class = 'tdc-tab-active';
			foreach ($css_parts as $css_part) {
				//print_r($css_part);


				?>
				<a title="<?php echo $css_part['title'] ?>" class="tdw-tab <?php echo $is_first_tab_class ?>"  href="#" data-tab-content="tdw-tab-<?php echo $css_part['detect_key'] ?>"><?php
					echo $css_part['name'];
						if ( td_live_css_css_storage::get($css_part['detect_key'], 'less') != '') {
							echo '<div class="tdw-tab-has-css"></div>';
						}

				?></a>
				<?php
				$is_first_tab_class = '';
			}
			//die;
			?>
		</div>


		<div class="tdw-tabs-content-wrap">

			<?php
			$is_first_tab_class = 'tdc-tab-content-active';
			foreach ($css_parts as $css_part) {
				$new_editor_uid = td_live_css_util::td_generate_unique_id();

				?>
				<div class="tdw-tabs-content tdw-tab-<?php echo $css_part['detect_key'] ?> <?php echo $is_first_tab_class ?>">

					<textarea class="tdw-css-writer-editor <?php echo $new_editor_uid?>" data-detect-ley="<?php echo $css_part['detect_key'] ?>"><?php echo td_live_css_css_storage::get($css_part['detect_key'], 'less') ?></textarea>
					<div id="<?php echo $new_editor_uid ?>" class="td-code-editor"></div>


					<script>
						(function (){
							var editor_textarea = jQuery('.<?php echo $new_editor_uid ?>');
							ace.require("ace/ext/language_tools");
							var editor = ace.edit("<?php echo $new_editor_uid ?>");
							editor.getSession().setValue(editor_textarea.val());
							editor.getSession().on('change', function(){
								//tdwState.lessWasEdited = true;



								window.onbeforeunload  = function () {
									if (tdwState.lessWasEdited) {
										return "You have attempted to leave this page. Are you sure?";
									}
									return false;
								};



								editor_textarea.val(editor.getSession().getValue());

								var lessBuffer = '';
								jQuery( ".tdw-css-writer-editor" ).each(function( index ) {
									lessBuffer += "\n" + jQuery(this).val()
								});
								tdLiveCssInject.less(lessBuffer);
							});

							editor.setTheme("ace/theme/textmate");
							//editor.setShowPrintMargin(false);
							editor.getSession().setMode("ace/mode/less");
							editor.setOptions({
								enableBasicAutocompletion: true,
								enableSnippets: true,
								enableLiveAutocompletion: false
							});
						})();
					</script>

				</div>
				<?php
				$is_first_tab_class = '';
			}
			?>
		</div>
		<a href="#" class="tdw-save-css">Save</a>
		<div class="tdw-more-info-text">Write CSS OR LESS and hit save. CTRL + SPACE for auto-complete. Made by <a target="_blank" href="https://www.wpion.com/">wpion.com</a></div>
	</div>
	<?php
}




/**
 * WP-admin - add js in header on all the admin pages (wp-admin and the iframe Wrapper. Does not run in the iframe)
 */
add_action( 'wp_head', 'td_live_css_on_wp_head' );
function td_live_css_on_wp_head() {


	// the settings that we load in wp-admin and wrapper. We need json to be sure we don't get surprises with the encoding/escaping
	$td_live_css_blobal = array(
		'adminUrl' => admin_url(),
		'wpRestNonce' => wp_create_nonce('wp_rest'),
		'wpRestUrl' => rest_url(),
		'permalinkStructure' => get_option('permalink_structure'),
	);

	ob_start();
	?>
	<script>
		window.tdwGlobal = <?php echo json_encode( $td_live_css_blobal );?>;
	</script>
	<?php
	$buffer = ob_get_clean();
	echo $buffer;
}



add_action('admin_enqueue_scripts', 'td_live_css_load_plugin_css');
add_action('wp_enqueue_scripts', 'td_live_css_load_plugin_css');
function td_live_css_load_plugin_css() {
	wp_enqueue_style('td_live_css_main', TD_LIVE_CSS_URL . '/assets/css/td_live_css_main.css', false, false);
}



add_action( 'wp_enqueue_scripts', 'td_live_css_load_plugin_js' );
function td_live_css_load_plugin_js() {
	wp_enqueue_script('td_live_css_ace', TD_LIVE_CSS_URL . '/assets/external/ace/ace.js', array('jquery'), TD_LIVE_CSS_VERSION, true);
	wp_enqueue_script('td_live_css_ace_ext_language_tools', TD_LIVE_CSS_URL . '/assets/external/ace/ext-language_tools.js', array('td_live_css_ace'), TD_LIVE_CSS_VERSION, true);
	wp_enqueue_script('td_live_css_state', TD_LIVE_CSS_URL . '/assets/js/tdLiveCssState.js', array('jquery'), TD_LIVE_CSS_VERSION, true); //first, load it with jQuery dependency
	wp_enqueue_script('td_live_css_tdMain', TD_LIVE_CSS_URL . '/assets/js/tdLiveCssMain.js', array('td_live_css_ace_ext_language_tools', 'underscore'), TD_LIVE_CSS_VERSION, true); //first, load it with jQuery dependency
	wp_enqueue_script('td_live_css_less', TD_LIVE_CSS_URL . '/assets/external/less.min.js', array('td_live_css_ace_ext_language_tools'), TD_LIVE_CSS_VERSION, true);
	wp_enqueue_script('td_live_css_inject', TD_LIVE_CSS_URL . '/assets/js/tdLiveCssInject.js', array('td_live_css_ace_ext_language_tools', 'td_live_css_less'), TD_LIVE_CSS_VERSION, true); //first, load it with jQuery dependency
}




add_action('admin_bar_menu', 'td_live_css_admin_bar_button', 9999);
function td_live_css_admin_bar_button() {
	global $wp_admin_bar;
	if (!is_super_admin() || !is_admin_bar_showing() || is_admin()) {
		return;
	}

	$wp_admin_bar->add_menu( array(
		'id'   => 'td_live_css_css_writer',
		'meta' => array('title' => 'Live CSS'),
		'title' => 'Live CSS',
		'href' => '#' ));

}


// add the admin menus
add_action('admin_menu', 'td_live_css_on_admin_menus', -10);
function td_live_css_on_admin_menus() {

	// the welcome screen
	add_management_page( 'Live CSS', 'Live CSS', 'manage_options', 'td_live_css_admin', 'td_live_css_admin_page' );
	function td_live_css_admin_page() {
		?>
		<div class="wrap tdw-welcome-panel" style="max-width: 800px;">

			<div class="welcome-panel">
				<div class="welcome-panel-content">
					<h2>Welcome to Live CSS:</h2>
					<p class="about-description"> - You can find the live editor in the admin bar while viewing a post on the frontend.</p>
					<p class="about-description"> - TIP: while editing your css hit ctrl+space to open auto-complete.</p>
					
					<p><img src="<?php echo TD_LIVE_CSS_URL?>/screenshot-1.gif" alt=""></p>
				</div>
			</div>


			<div class="welcome-panel">
				<div class="welcome-panel-content">
					<h2>Short introduction to the plugin:</h2>
					<p><img src="<?php echo TD_LIVE_CSS_URL?>/screenshot-2.png" alt=""></p>
				</div>
			</div>


			<div class="welcome-panel">
				<div class="welcome-panel-content">
					<h2>Live editing</h2>
					<p><img src="<?php echo TD_LIVE_CSS_URL?>/screenshot-3.png" alt=""></p>
				</div>
			</div>
		</div>
		<?php
	}
}





function cyb_activation_redirect( $plugin ) {
	if( $plugin == plugin_basename( __FILE__ ) ) {
		exit( wp_redirect( admin_url( 'tools.php?page=td_live_css_admin' ) ) );
	}
}
add_action( 'activated_plugin', 'cyb_activation_redirect' );



