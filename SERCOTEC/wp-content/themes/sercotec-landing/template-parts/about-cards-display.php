<?php
/**
 * Grilla de tarjetas con soporte de carrusel.
 *
 * @var array<string, mixed> $args
 */

$cards            = $args['cards'] ?? array();
$carousel_label   = $args['carousel_label'] ?? __( 'Tarjetas', 'sercotec-landing' );
$carousel_visible = max( 1, (int) ( $args['carousel_visible'] ?? 1 ) );
$groups           = sercotec_landing_group_cards_for_display( $cards );

foreach ( $groups as $group ) :
	if ( 'carousel' === $group['type'] ) :
		$items = $group['items'];
		?>
		<div class="about-cards-carousel-wrap">
			<div
				class="carousel carousel--cards about-cards-carousel"
				data-carousel
				data-carousel-cards
				data-carousel-visible="<?php echo esc_attr( (string) $carousel_visible ); ?>"
				data-carousel-label="<?php echo esc_attr( $carousel_label ); ?>"
			>
				<div class="about-cards-carousel__viewport">
					<div class="about-cards-carousel__track">
						<?php foreach ( $items as $index => $card ) : ?>
							<div class="about-cards-carousel__item" data-carousel-item aria-hidden="<?php echo $index < $carousel_visible ? 'false' : 'true'; ?>">
								<?php
								get_template_part(
									'template-parts/about-card',
									null,
									array(
										'card'            => $card,
										'layout_override' => '1x1',
									)
								);
								?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php if ( count( $items ) > $carousel_visible ) : ?>
					<div class="carousel__controls">
						<button class="carousel__btn" type="button" data-carousel-prev aria-label="<?php esc_attr_e( 'Anterior', 'sercotec-landing' ); ?>">‹</button>
						<div class="carousel__dots" data-carousel-dots role="tablist" aria-label="<?php echo esc_attr( $carousel_label ); ?>"></div>
						<button class="carousel__btn" type="button" data-carousel-next aria-label="<?php esc_attr_e( 'Siguiente', 'sercotec-landing' ); ?>">›</button>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	else :
		get_template_part(
			'template-parts/about-card',
			null,
			array(
				'card' => $group['item'],
			)
		);
	endif;
endforeach;
