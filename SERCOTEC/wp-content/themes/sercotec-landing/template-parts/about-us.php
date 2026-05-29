<section class="about section" id="nosotros" aria-labelledby="about-title" style="<?php echo esc_attr( sercotec_landing_get_about_section_style() ); ?>">
	<div class="container about__inner">
		<div class="about__content">
			<?php if ( sercotec_landing_get_about_label() ) : ?>
				<p class="about__label" id="about-title"><?php echo esc_html( sercotec_landing_get_about_label() ); ?></p>
			<?php endif; ?>
			<p class="about__text"><?php echo esc_html( sercotec_landing_get_about_content() ); ?></p>
		</div>

		<?php
		$about_cards = sercotec_landing_get_about_cards();

		if ( ! empty( $about_cards ) ) :
			?>
			<div class="about__cards">
				<?php
				get_template_part(
					'template-parts/about-cards-display',
					null,
					array(
						'cards'          => $about_cards,
						'carousel_label' => __( 'Nosotros', 'sercotec-landing' ),
					)
				);
				?>
			</div>
		<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>
			<p class="about__cards-empty">
				<?php
				printf(
					wp_kses_post( __( 'Aún no hay elementos en Nosotros. <a href="%s">Agrega el primero</a>.', 'sercotec-landing' ) ),
					esc_url( admin_url( 'post-new.php?post_type=sercotec_about_card' ) )
				);
				?>
			</p>
		<?php endif; ?>
	</div>
</section>
