<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Amazon_Shop {
	private static $initiated = false;
	
	public function __construct() {
		if ( ! self::$initiated ) {
			self::initiate_hooks();
		}
	}
	
	private static function initiate_hooks() {			    				
	    add_action( 'admin_init', array( __CLASS__, 'add_amazonshop_settings' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_amazonshop_submenu_settings' ) );
		add_action( 'init', array( __CLASS__, 'create_shop_homepage' ) );
		add_action( 'admin_notices', array( __CLASS__, 'caaps_admin_notices' ) );		
		add_action( 'plugins_loaded', array( __CLASS__, 'amazonshop_load_textdomain') );
		add_filter( 'plugin_row_meta',     array( __CLASS__, 'amazonshop_row_link'), 10, 2 );
		self::$initiated = true;
	}
			
	public static function activate_amazonshop() {
		self::check_preactivation_requirements();
		flush_rewrite_rules( true );
		
	}
	
	public static function check_preactivation_requirements() {				
		if ( version_compare( PHP_VERSION, AMZONPRODUCTSHOP_MINIMUM_PHP_VERSION, '<' ) ) {
			wp_die('Minimum PHP Version required: ' . AMZONPRODUCTSHOP_MINIMUM_PHP_VERSION );
		}
        global $wp_version;
		if ( version_compare( $wp_version, AMZONPRODUCTSHOP_MINIMUM_WP_VERSION, '<' ) ) {
			wp_die('Minimum Wordpress Version required: ' . AMZONPRODUCTSHOP_MINIMUM_WP_VERSION );
		}
		if ( ! extension_loaded( 'soap' ) ) {
			wp_die('PHP SOAP extension is not active on your server, it requires before activate plugin!');
		}
	}
	
	public static function amazonshop_load_textdomain() {
		load_plugin_textdomain( 'codeshop-amazon-affiliate', false, AMZONPRODUCTSHOP_PLUGIN_DIR . 'languages/' ); 
	}
		
	public static function add_amazonshop_settings() {
		register_setting( 'caaps_amazon-product-shop-settings', 'caaps_amazon-product-shop-settings' );
		add_settings_section( 'caaps_settings_section', __( 'CodeShop Amazon Affiliate Settings' ), array( __CLASS__, 'settings_section_callback' ), 'caaps_amazon-product-shop-settings' );
		add_settings_field( 'caaps_settings_field_accesskeyid', __( 'Access Key ID', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'settings_section_fields_callback' ), 'caaps_amazon-product-shop-settings', 'caaps_settings_section', $args = array( 'fieldname' => 'accesskey', 'label_for' => 'caaps_settings_field_accesskeyid' ) );
		add_settings_field( 'caaps_settings_field_secretaccesskey', __( 'Secret Access Key', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'settings_section_fields_callback' ), 'caaps_amazon-product-shop-settings', 'caaps_settings_section', $args = array( 'fieldname' => 'secretaccesskey', 'label_for' => 'caaps_settings_field_secretaccesskey' ) );
		add_settings_field( 'caaps_settings_field_associateid', __( 'Associate ID', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'settings_section_fields_callback' ), 'caaps_amazon-product-shop-settings', 'caaps_settings_section', $args = array( 'fieldname' => 'associateid', 'label_for' => 'caaps_settings_field_associateid' ) );
		add_settings_field( 'caaps_settings_field_country', __( 'Select Country', 'codeshop-amazon-affiliate' ), array( __CLASS__, 'settings_section_fields_callback' ), 'caaps_amazon-product-shop-settings', 'caaps_settings_section', $args = array( 'fieldname' => 'selectcountry', 'label_for' => 'caaps_settings_field_country' ) );				
	}
	
	public static function settings_section_callback() {
		include_once( AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/settings-page-amazon-help.php');
	}
	
	public static function settings_section_fields_callback( $args = null ) {		
		$options = get_option('caaps_amazon-product-shop-settings');
		//print_r($options);
		switch ($args['fieldname']) {
			case 'accesskey':
			$value = isset( $options[$args['label_for']] )? $options[$args['label_for']] : '';
			echo '<input type="text" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-settings['.esc_attr($args['label_for']).']" value="'.$value.'" size="100" placeholder="Access Key ID" />';
			break;
			
			case 'secretaccesskey':
			$value = isset( $options[$args['label_for']] )? $options[$args['label_for']] : '';
			echo '<input type="password" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-settings['.esc_attr($args['label_for']).']" value="'.$value.'" size="100" autocomplete="off" placeholder="Secret Access Key" />';
			break;

			case 'associateid':
			$value = isset( $options[$args['label_for']] )? $options[$args['label_for']] : '';
			echo '<input type="text" id="'.$args['label_for'].'" name="caaps_amazon-product-shop-settings['.esc_attr($args['label_for']).']" value="'.$value.'" size="100" placeholder="Associate ID" />';
			break;
			
			case 'selectcountry':
			$countries = self::supported_countries();			
			$value = isset( $options[$args['label_for']] )? $options[$args['label_for']] : '';
			echo '<select id="'.$args['label_for'].'" name="caaps_amazon-product-shop-settings['.esc_attr($args['label_for']).']">';
			foreach ( $countries as $cc => $country) {
				$selected = isset( $options[$args['label_for']] ) ? selected( $options[$args['label_for']], $cc, false) : '';
				echo '<option value="'.$cc.'" ' .$selected.'>'.$country.'</option>';
			}			
			echo '</select>';
			break;						
		}
	}
	
	public static function add_amazonshop_submenu_settings() {
		add_submenu_page(
		    'edit.php?post_type=amazonproductshop',
        __( 'Amazon Product Shop', 'codeshop-amazon-affiliate' ),
        __( 'Settings', 'codeshop-amazon-affiliate' ),
            'manage_options',
            'caaps_amazon-product-shop-settings',
			array( __CLASS__, 'add_amazonshop_submenu_settings_callback' )        
          );
	}	
						
	public static function add_amazonshop_submenu_settings_callback() {
		// check user capabilities
		if ( !current_user_can('manage_options' ) ) {
			return;
		}		
		include_once AMZONPRODUCTSHOP_PLUGIN_DIR . 'admin/views/add_amazonshop_submenu_settings_callback.php';		
	}
	
	public static function supported_countries() {
			$countries = array( 'com.br' => 'Brazil',
								'ca'     => 'Canada',
								'cn'     => 'China',
								'fr'     => 'France',
								'de'     => 'Germany',
								'in'     => 'India',
								'it'     => 'Italy',
								'co.jp'  => 'Japan',
								'com.mx' => 'Mexico',
								'es'     => 'Spain',
								'co.uk'  => 'United Kingdom',
								'com'    => 'United States'
			                  );
		return $countries;					  		
	}
	
	public static function create_shop_homepage() {		
		$page_name = 'amazon-product-shop';	
		$page = get_page_by_path( $page_name );		
		if ( ! isset( $page->ID ) ) {
			$current_user = wp_get_current_user();
			$postarr = array(  
			                'post_type'     => 'page',
							'post_name'     => $page_name,
							'post_title'    => 'Amazon Shop', 
							'post_author'   =>  1,
							'post_status'   => 'publish'
						  );			
		  $amazonshop_frontpageid = wp_insert_post( $postarr );				  
		  update_option( 'caaps_amazonshop_frontpageid', $amazonshop_frontpageid );
		}
	}
	
	public static function caaps_admin_notices() {
		$admin_notice = false;
		$options = get_option('caaps_amazon-product-shop-settings');
		if ( ! isset( $options['caaps_settings_field_accesskeyid']) || 
			empty( $options['caaps_settings_field_accesskeyid'] ) ) {
			$admin_notice = true;
		}		
		if ( ! isset( $options['caaps_settings_field_secretaccesskey']) || 
			empty( $options['caaps_settings_field_secretaccesskey'] ) ) {
			$admin_notice = true;
		}

		if ( ! isset( $options['caaps_settings_field_country'] ) || 
			empty( $options['caaps_settings_field_country'] ) ) {
			$admin_notice = true;
		}
		
		if ( $admin_notice ) {
			$url = admin_url( 'edit.php?post_type=amazonproductshop&page=caaps_amazon-product-shop-settings' );
			$alink = '<a href="'.$url.'">Click to add.</a>';
			printf('<div class="notice notice-warning is-dismissible">');
		    printf('<div class="caaps-amazonshop-notice-wrapper"><h3><span class="dashicons dashicons-products"></span> CodeShop Amazon Affiliate:</h3> <h4>Amazon Access Key , Serect Access Key, Associate ID and Country settings required! Please add them through admin menu CodeShop -> Settings page. ' .$alink.'</h4></div>');
	        printf('</div>');
		}				
	}
	
	public static function amazonshop_row_link( $actions, $plugin_file ) {
		$codeshop_plugin = plugin_basename( AMZONPRODUCTSHOP_PLUGIN_DIR );
		$plugin_name = basename($plugin_file, '.php');
		if ( $codeshop_plugin == $plugin_name ) {
			$doclink[] = '<a href="https://codeapple.net/codeshop-amazon-affiliate/documentation/" title="CodeShop Documentation" target="_blank">Documentation</a>';	
			$doclink[] = '<a href="https://codeapple.net/codeshop-amazon-affiliate/forum/" title="CodeShop Forum Help" target="_blank">Forum</a>';	
			return array_merge( $actions, $doclink );
		}
		return $actions;
	}
	
} // End class