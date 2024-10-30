<?php
/**
 * Created by ra.
 * Date: 7/18/2016
 * Handle the ajax requests, for now only save
 */




add_action( 'rest_api_init', 'td_live_css_on_rest_api_init');
function td_live_css_on_rest_api_init() {
	$namespace = 'tdw';
	register_rest_route($namespace, '/save_css/', array(
		'methods'  => 'POST',
		'callback' => 'td_live_css_on_ajax_save_css',
	));
}


/**
 * the save_css endpoint
 * @param WP_REST_Request $request
 */
function td_live_css_on_ajax_save_css(WP_REST_Request $request) {
	$compiled_css_items = $request->get_param('compiledCss');
	if (!empty($compiled_css_items) && is_array($compiled_css_items)) {
		foreach ($compiled_css_items as $detect_key => $css) {
			td_live_css_css_storage::update($detect_key, 'css', $css);
		}
	}

	$compiled_less_items = $request->get_param('lessInput');
	if (!empty($compiled_less_items) && is_array($compiled_less_items)) {
		foreach ($compiled_less_items as $detect_key => $less) {
			td_live_css_css_storage::update($detect_key, 'less', $less);
		}
	}
}