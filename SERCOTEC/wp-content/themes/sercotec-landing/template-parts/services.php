<?php
$services       = sercotec_landing_get_services();
$services_split = sercotec_landing_split_services_for_mid_paragraph( $services );
$carousel_label = __( 'Servicios', 'sercotec-landing' );
?>

<section class="services section section--alt" id="servicios" aria-labelledby="services-title" style="<?php echo esc_attr( sercotec_landing_get_services_section_style() ); ?>">
	<div class="container">
		<div class="section__header">
			<?php if ( sercotec_landing_get_services_label() ) : ?>
				<p class="section__label services__label"><?php echo esc_html( sercotec_landing_get_services_label() ); ?></p>
			<?php endif; ?>
			<h2 class="section__title services__title" id="services-title">
				<?php echo esc_html( sercotec_landing_get_services_title() ); ?>
			</h2>
			<?php if ( sercotec_landing_get_services_subtitle() ) : ?>
				<p class="section__subtitle services__subtitle">
					<?php echo esc_html( sercotec_landing_get_services_subtitle() ); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $services ) ) : ?>
			<?php if ( ! empty( $services_split['before'] ) ) : ?>
				<div class="about__cards">
					<?php
					get_template_part(
						'template-parts/about-cards-display',
						null,
						array(
							'cards'            => $services_split['before'],
							'carousel_label'   => $carousel_label,
							'carousel_visible' => 3,
						)
					);
					?>
				</div>
			<?php endif; ?>

			<?php if ( $services_split['show'] ) : ?>
				<div class="services__mid-paragraph">
					<?php echo wp_kses_post( sercotec_landing_get_services_mid_paragraph_html() ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $services_split['after'] ) ) : ?>
				<div class="about__cards<?php echo $services_split['show'] ? ' about__cards--after-mid' : ''; ?>">
					<?php
					get_template_part(
						'template-parts/about-cards-display',
						null,
						array(
							'cards'            => $services_split['after'],
							'carousel_label'   => $carousel_label,
							'carousel_visible' => 3,
						)
					);
					?>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<p class="services__empty">
				<?php
				if ( current_user_can( 'edit_posts' ) ) {
					printf(
						wp_kses_post( __( 'Aún no hay servicios publicados. <a href="%s">Agrega el primero</a>.', 'sercotec-landing' ) ),
						esc_url( admin_url( 'post-new.php?post_type=sercotec_servicio' ) )
					);
				} else {
					esc_html_e( 'Pronto publicaremos nuestros servicios.', 'sercotec-landing' );
				}
				?>
			</p>
		<?php endif; ?>
	</div>
</section>
