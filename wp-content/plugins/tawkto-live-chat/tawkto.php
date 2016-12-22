<?php
/*
Plugin Name: Tawk.to Live Chat
Plugin URI: https://tawk.to
Description: Embeds Tawk.to live chat widget to your site
Version: 0.2.2
Author: Tawkto
*/
if(!class_exists('TawkTo_Settings')){

	class TawkTo_Settings{
		const TAWK_WIDGET_ID_VARIABLE = 'tawkto-embed-widget-widget-id';
		const TAWK_PAGE_ID_VARIABLE = 'tawkto-embed-widget-page-id';
		const TAWK_VISIBILITY_OPTIONS = 'tawkto-visibility-options';

		public function __construct(){

			if(!get_option('tawkto-visibility-options',false))
			{
			$visibility = array (
				'always_display' => 1,
				'show_onfrontpage' => 0,
				'show_oncategory' => 0,
				'show_ontagpage' => 0,
				'show_onarticlepages' => 0,
				'exclude_url' => 0,
				'excluded_url_list' => ''
			);
			update_option( 'tawkto-visibility-options', $visibility);
			}

			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'add_menu'));
			add_action('wp_ajax_tawkto_setwidget',  array(&$this, 'action_setwidget'));
			add_action('wp_ajax_tawkto_removewidget',  array(&$this, 'action_removewidget'));

			add_action('init', array(&$this, 'start_session'), 1);
			add_action('wp_logout', array(&$this, 'end_session'));
			add_action('wp_login', array(&$this, 'end_session'));
		}

		function start_session() {
		    if(!session_id()) {
		        session_start();
		    }
		}

		function end_session() {
		    session_destroy ();
		}

		public function admin_init(){
			register_setting( 'tawk_options', 'tawkto-visibility-options', array(&$this,'validate_options') );
		}

		public function action_setwidget() {
			header('Content-Type: application/json');

			if (!isset($_POST['pageId']) || !isset($_POST['widgetId'])) {
				echo json_encode(array('success' => FALSE));
				die();
			}

			if (!self::ids_are_correct($_POST['pageId'], $_POST['widgetId'])) {
				echo json_encode(array('success' => FALSE));
				die();
			}

			update_option(self::TAWK_PAGE_ID_VARIABLE, $_POST['pageId']);
			update_option(self::TAWK_WIDGET_ID_VARIABLE, $_POST['widgetId']);

			echo json_encode(array('success' => TRUE));
			die();
		}

		public function action_removewidget() {
			header('Content-Type: application/json');

			update_option(self::TAWK_PAGE_ID_VARIABLE, '');
			update_option(self::TAWK_WIDGET_ID_VARIABLE, '');

			echo json_encode(array('success' => TRUE));
			die();
		}

		public function validate_options($input){

			$input['always_display'] = ($input['always_display'] != '1')? 0 : 1;
			$input['show_onfrontpage'] = ($input['show_onfrontpage'] != '1')? 0 : 1;
			$input['show_oncategory'] = ($input['show_oncategory'] != '1')? 0 : 1;
			$input['show_ontagpage'] = ($input['show_ontagpage'] != '1')? 0 : 1;
			$input['show_onarticlepages'] = ($input['show_onarticlepages'] != '1')? 0 : 1;
			$input['exclude_url'] = ($input['exclude_url'] != '1')? 0 : 1;
			$input['excluded_url_list'] = sanitize_text_field($input['excluded_url_list']);

			return $input;
		}

		public function add_menu(){
			add_options_page(
				'Tawk.to Settings',
				'Tawk.to',
				'manage_options',
				'tawkto_plugin',
				array(&$this, 'create_plugin_settings_page')
			);
		}

		public function create_plugin_settings_page(){

			global $wpdb;

			if(!current_user_can('manage_options'))	{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			$page_id = get_option(self::TAWK_PAGE_ID_VARIABLE);
			$widget_id = get_option(self::TAWK_WIDGET_ID_VARIABLE);
			$base_url = 'https://plugins.tawk.to';

			if(
				isset($wpdb->blogid) &&
				(
					!isset($_SESSION['active_page']) ||
					$_SESSION['active_page'] != $wpdb->blogid
				)
			) {
				$_SESSION['active_page'] = $wpdb->blogid;
				$iframe_url = $base_url.'/generic/logout'
					.'?currentWidgetId='.$widget_id
					.'&currentPageId='.$page_id
					.'&transparentBackground=1';
			}else{
				$iframe_url = $base_url.'/generic/widgets'
					.'?currentWidgetId='.$widget_id
					.'&currentPageId='.$page_id
					.'&transparentBackground=1';
			}

			include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
		}

		public static function ids_are_correct($page_id, $widget_id) {
			return preg_match('/^[0-9A-Fa-f]{24}$/', $page_id) === 1 && preg_match('/^[a-z0-9]{1,50}$/i', $widget_id) === 1;
		}
	}
}

if(!class_exists('TawkTo')){
	class TawkTo{
		public function __construct(){
			$tawkto_settings = new TawkTo_Settings();
			add_shortcode( 'tawkto', array($this,'shortcode_print_embed_code') );
		}

		public static function activate(){

			$visibility = array (
				'always_display' => 1,
				'show_onfrontpage' => 0,
				'show_oncategory' => 0,
				'show_ontagpage' => 0,
				'show_onarticlepages' => 0,
				'exclude_url' => 0,
				'excluded_url_list' => ''
			);

			add_option(TawkTo_Settings::TAWK_PAGE_ID_VARIABLE, '', '', 'yes');
			add_option(TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE, '', '', 'yes');
			add_option(TawkTo_Settings::TAWK_VISIBILITY_OPTIONS, $visibility, '', 'yes');
		}

		public static function deactivate(){
			delete_option(TawkTo_Settings::TAWK_PAGE_ID_VARIABLE);
			delete_option(TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE);
			delete_option(TawkTo_Settings::TAWK_VISIBILITY_OPTIONS);
		}

		public function shortcode_print_embed_code(){
			add_action('wp_footer',  array($this, 'embed_code'));
		}

		public function embed_code()
		{
			$page_id = get_option('tawkto-embed-widget-page-id');
			$widget_id = get_option('tawkto-embed-widget-widget-id');

			if(!empty($page_id) && !empty($widget_id))
			{
				include(sprintf("%s/templates/widget.php", dirname(__FILE__)));
			}
		}

		public function print_embed_code()
		{
			$vsibility = get_option( 'tawkto-visibility-options' );

			$display = FALSE;

			if(($vsibility['show_onfrontpage'] == 1) && (is_home() || is_front_page()) ){ $display = TRUE; }
			if(($vsibility['show_oncategory'] == 1) && is_category() ){ $display = TRUE; }
			if(($vsibility['show_ontagpage'] == 1) && is_tag() ){ $display = TRUE; }
			if($vsibility['always_display'] == 1){ $display = TRUE; }
			if(($vsibility['show_onarticlepages'] == 1) && is_single() ){ $display = TRUE; }
			if(($vsibility['exclude_url'] == 1)){
				$excluded_url_list = $vsibility['excluded_url_list'];
				$current_url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				$current_url = urldecode($current_url);
				$excluded_url_list = preg_split("/,/", $excluded_url_list);
				foreach($excluded_url_list as $exclude_url)
				{
					$exclude_url = urldecode(trim($exclude_url));
					if (strpos($current_url, $exclude_url) !== false) {
							$display = false;
					}
				}
			}

			if($display == TRUE)
			{
				$this->embed_code();
			}
		}

		/**
		 * migrate old tawk to embed code to new version.
		 *
		 * old version contained embed code script, from that
		 * markup we need only page id and widget id
		 */
		public function migrate_embed_code() {

			$old_tawkto_embed_code = get_option('tawkto-embed-code');

			if(empty($old_tawkto_embed_code)) {
				return;
			}

			$matches = array();
			preg_match('/https:\/\/embed.tawk.to\/([0-9A-Fa-f]{24})\/([a-z0-9]{1,50})/', $old_tawkto_embed_code, $matches);

			if(isset($matches[1]) && isset($matches[2]) && TawkTo_Settings::ids_are_correct($matches[1], $matches[2])) {
				update_option(TawkTo_Settings::TAWK_PAGE_ID_VARIABLE, $matches[1]);
				update_option(TawkTo_Settings::TAWK_WIDGET_ID_VARIABLE, $matches[2]);
			}

			delete_option('tawkto-embed-code');
		}
	}
}

if(class_exists('TawkTo')){
	register_activation_hook(__FILE__, array('TawkTo', 'activate'));
	register_deactivation_hook(__FILE__, array('TawkTo', 'deactivate'));

	$tawkto = new TawkTo();

	if(isset($tawkto)){
		$tawkto->migrate_embed_code();

		function tawkto_plugin_settings_link($links){
			$settings_link = '<a href="options-general.php?page=tawkto_plugin">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}

		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links_$plugin", 'tawkto_plugin_settings_link');
	}

	add_action('wp_footer',  array($tawkto, 'print_embed_code'));
}
