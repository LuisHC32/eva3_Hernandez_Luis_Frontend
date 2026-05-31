<?php
/**
 * One-time setup: activate theme and configure front page.
 * Run: docker compose exec wordpress php /var/www/html/wp-content/themes/sercotec-landing/setup.php
 */

require_once dirname( __DIR__, 3 ) . '/wp-load.php';

if ( ! function_exists( 'switch_theme' ) ) {
	echo "WordPress not loaded.\n";
	exit( 1 );
}

switch_theme( 'sercotec-landing' );

$page = get_page_by_path( 'inicio' );

if ( ! $page ) {
	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Inicio',
			'post_name'    => 'inicio',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		)
	);
} else {
	$page_id = $page->ID;
}

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_id );

sercotec_landing_seed_default_content( true );

echo "Theme activated: sercotec-landing\n";
echo "Front page set to: Inicio (ID {$page_id})\n";
echo "Site defaults seeded (options + CPTs).\n";
