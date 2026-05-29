<?php
/**
 * Tarjeta individual (Servicios / Nosotros).
 *
 * @var array<string, mixed> $args
 */

$card            = $args['card'] ?? array();
$layout_override = $args['layout_override'] ?? '';
$layout          = $layout_override ?: ( $card['layout'] ?? '1x1' );
$has_body        = ! empty( $card['content_html'] ) || ! empty( $card['title'] ) || ! empty( $card['title_html'] );
$bg_color        = $card['color'] ?? '#0052a3';
$text_color      = $card['text_color'] ?? '#ffffff';
$body_classes    = 'about-card__body';

if ( sercotec_landing_is_light_color_hex( $bg_color ) || ! sercotec_landing_is_light_color_hex( $text_color ) ) {
	$body_classes .= ' about-card__body--dark-text';
}

$body_style = sprintf(
	'background-color:%1$s;color:%2$s;',
	$bg_color,
	$text_color
);
?>
<article class="about-card about-card--layout-<?php echo esc_attr( $layout ); ?><?php echo empty( $card['image'] ) ? ' about-card--no-image' : ''; ?>">
	<?php if ( ! empty( $card['image'] ) ) : ?>
		<div class="about-card__image">
			<img src="<?php echo esc_url( $card['image'] ); ?>" alt="" loading="lazy">
		</div>
	<?php endif; ?>
	<?php if ( $has_body ) : ?>
		<div class="<?php echo esc_attr( $body_classes ); ?>" style="<?php echo esc_attr( $body_style ); ?>">
			<?php if ( ! empty( $card['title_html'] ) ) : ?>
				<div class="about-card__title"><?php echo wp_kses_post( $card['title_html'] ); ?></div>
			<?php elseif ( ! empty( $card['title'] ) ) : ?>
				<?php
				$content_text = ! empty( $card['content_html'] ) ? trim( wp_strip_all_tags( $card['content_html'] ) ) : '';
				if ( '' === $content_text || $content_text !== $card['title'] ) :
					?>
					<h3 class="about-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( ! empty( $card['content_html'] ) ) : ?>
				<div class="about-card__content"><?php echo wp_kses_post( $card['content_html'] ); ?></div>
			<?php endif; ?>
			<?php if ( ! empty( $card['quick_access'] ) && ! empty( $card['title'] ) ) : ?>
				<a
					class="about-card__cta"
					href="#contacto"
					data-contact-service="<?php echo esc_attr( $card['title'] ); ?>"
				>
					<?php esc_html_e( 'Contáctanos', 'sercotec-landing' ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</article>
