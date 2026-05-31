<?php
/**
 * Separa visualmente la configuración de la landing en el menú del admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_register_site_config_menu_separators(): void {
	global $menu;

	$menu['20.5'] = array(
		'',
		'read',
		'separator-sercotec-site-config-start',
		'',
		'wp-menu-separator sercotec-site-config-separator sercotec-site-config-separator--start',
	);

	$menu['27.5'] = array(
		'',
		'read',
		'separator-sercotec-site-config-end',
		'',
		'wp-menu-separator sercotec-site-config-separator sercotec-site-config-separator--end',
	);
}
add_action( 'admin_menu', 'sercotec_landing_register_site_config_menu_separators', 9 );

function sercotec_landing_site_config_admin_assets(): void {
	sercotec_landing_enqueue_theme_styles();
}
add_action( 'admin_enqueue_scripts', 'sercotec_landing_site_config_admin_assets' );
