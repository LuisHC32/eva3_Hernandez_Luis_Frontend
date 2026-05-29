<?php
/**
 * Biblioteca de logos para redes sociales (SVG incluidos en el tema).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_social_icon_library(): array {
	return array(
		'linkedin'  => array(
			'label' => 'LinkedIn',
			'file'  => 'linkedin.svg',
		),
		'instagram' => array(
			'label' => 'Instagram',
			'file'  => 'instagram.svg',
		),
		'youtube'   => array(
			'label' => 'YouTube',
			'file'  => 'youtube.svg',
		),
		'facebook'  => array(
			'label' => 'Facebook',
			'file'  => 'facebook.svg',
		),
		'x'         => array(
			'label' => 'X (Twitter)',
			'file'  => 'x.svg',
		),
		'tiktok'    => array(
			'label' => 'TikTok',
			'file'  => 'tiktok.svg',
		),
		'whatsapp'  => array(
			'label' => 'WhatsApp',
			'file'  => 'whatsapp.svg',
		),
		'github'    => array(
			'label' => 'GitHub',
			'file'  => 'github.svg',
		),
	);
}

function sercotec_landing_social_icon_exists( string $key ): bool {
	$key = sanitize_key( $key );

	return isset( sercotec_landing_social_icon_library()[ $key ] );
}

function sercotec_landing_get_social_icon_file_path( string $key ): string {
	if ( ! sercotec_landing_social_icon_exists( $key ) ) {
		return '';
	}

	$file = sercotec_landing_social_icon_library()[ $key ]['file'];

	return SERCOTEC_LANDING_DIR . '/assets/icons/social/' . $file;
}

function sercotec_landing_get_social_icon_svg( string $key, string $class = '' ): string {
	$path = sercotec_landing_get_social_icon_file_path( $key );

	if ( ! $path || ! is_readable( $path ) ) {
		return '';
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$svg = (string) file_get_contents( $path );

	if ( '' === trim( $svg ) ) {
		return '';
	}

	$classes = trim( 'site-footer__social-icon site-footer__social-icon--svg ' . $class );

	return preg_replace( '/<svg\b/', '<svg class="' . esc_attr( $classes ) . '" aria-hidden="true"', $svg, 1 );
}

function sercotec_landing_guess_social_icon_key( string $label ): string {
	$label = strtolower( remove_accents( $label ) );

	foreach ( sercotec_landing_social_icon_library() as $key => $icon ) {
		$candidates = array(
			strtolower( $key ),
			strtolower( remove_accents( $icon['label'] ) ),
		);

		foreach ( $candidates as $candidate ) {
			if ( str_contains( $label, $candidate ) ) {
				return $key;
			}
		}
	}

	return '';
}

function sercotec_landing_render_social_icon_picker( string $selected_key = '', int $icon_id = 0, string $icon_url = '' ): void {
	$library = sercotec_landing_social_icon_library();
	?>
	<div class="sercotec-social-icon-picker" role="listbox" aria-label="<?php esc_attr_e( 'Biblioteca de logos', 'sercotec-landing' ); ?>">
		<?php foreach ( $library as $key => $icon ) : ?>
			<button
				type="button"
				class="sercotec-social-icon-picker__option<?php echo ( $selected_key === $key && ! $icon_id ) ? ' is-selected' : ''; ?>"
				data-icon-key="<?php echo esc_attr( $key ); ?>"
				data-icon-label="<?php echo esc_attr( $icon['label'] ); ?>"
				title="<?php echo esc_attr( $icon['label'] ); ?>"
				aria-label="<?php echo esc_attr( $icon['label'] ); ?>"
			>
				<?php echo sercotec_landing_get_social_icon_svg( $key, 'sercotec-social-icon-picker__svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		<?php endforeach; ?>
	</div>
	<?php
}

function sercotec_landing_render_footer_social_icon_preview( string $icon_key = '', int $icon_id = 0 ): void {
	if ( $icon_id ) {
		$icon_url = wp_get_attachment_image_url( $icon_id, 'thumbnail' );

		if ( $icon_url ) {
			echo '<img src="' . esc_url( $icon_url ) . '" alt="">';
			return;
		}
	}

	if ( $icon_key && sercotec_landing_social_icon_exists( $icon_key ) ) {
		echo sercotec_landing_get_social_icon_svg( $icon_key, 'sercotec-social-row__icon-svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo '<span class="sercotec-social-row__icon-placeholder">' . esc_html__( 'Sin logo', 'sercotec-landing' ) . '</span>';
}

function sercotec_landing_render_footer_social_icon( array $social ): void {
	if ( ! empty( $social['icon_url'] ) ) {
		echo '<img class="site-footer__social-icon" src="' . esc_url( $social['icon_url'] ) . '" alt="" loading="lazy">';
		return;
	}

	if ( ! empty( $social['icon_svg'] ) ) {
		echo $social['icon_svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

function sercotec_landing_footer_social_has_icon( array $social ): bool {
	return ! empty( $social['icon_url'] ) || ! empty( $social['icon_svg'] );
}

function sercotec_landing_get_footer_social_aria_label( array $social ): string {
	$label = trim( (string) ( $social['label'] ?? '' ) );

	if ( '' !== $label ) {
		return $label;
	}

	$icon_key = sanitize_key( $social['icon_key'] ?? '' );

	if ( $icon_key && sercotec_landing_social_icon_exists( $icon_key ) ) {
		return sercotec_landing_social_icon_library()[ $icon_key ]['label'];
	}

	return __( 'Red social', 'sercotec-landing' );
}
