<?php
$logo_id    = sercotec_landing_get_footer_logo_id();
$logo_url   = $logo_id ? wp_get_attachment_image_url( $logo_id, 'sercotec-logo' ) : '';
$brand_name = sercotec_landing_get_footer_brand_name();
?>
<a class="navbar__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
	<?php if ( $logo_url ) : ?>
		<img class="navbar__brand-image" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( sercotec_landing_get_footer_brand_alt() ); ?>" loading="lazy">
	<?php else : ?>
		<span class="navbar__logo"><?php echo esc_html( sercotec_landing_get_footer_logo_letter() ); ?></span>
	<?php endif; ?>
	<?php if ( $brand_name ) : ?>
		<span class="navbar__title"><?php echo esc_html( $brand_name ); ?></span>
	<?php endif; ?>
</a>
