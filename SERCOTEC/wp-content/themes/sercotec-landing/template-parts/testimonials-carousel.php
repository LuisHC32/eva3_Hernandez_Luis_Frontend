<?php
$testimonials = sercotec_landing_get_testimonials();
?>

<section class="testimonials section" id="testimonios" aria-labelledby="testimonials-title">
	<div class="container">
		<div class="section__header">
			<?php if ( sercotec_landing_get_testimonials_label() ) : ?>
				<span class="section__label"><?php echo esc_html( sercotec_landing_get_testimonials_label() ); ?></span>
			<?php endif; ?>
			<h2 class="section__title" id="testimonials-title">
				<?php echo esc_html( sercotec_landing_get_testimonials_title() ); ?>
			</h2>
		</div>

		<?php if ( ! empty( $testimonials ) ) : ?>
			<div class="carousel carousel--video" data-carousel data-carousel-video>
				<div class="carousel__track" data-carousel-track>
					<?php foreach ( $testimonials as $index => $testimonial ) : ?>
						<article class="testimonial-video carousel__slide" data-carousel-slide aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">
							<div class="testimonial-video__embed">
								<iframe
									data-src="<?php echo esc_url( $testimonial['embed_url'] ); ?>"
									<?php if ( 0 === $index ) : ?>
										src="<?php echo esc_url( $testimonial['embed_url'] ); ?>"
									<?php endif; ?>
									title="<?php echo esc_attr( $testimonial['title'] ); ?>"
									loading="lazy"
									allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
									referrerpolicy="strict-origin-when-cross-origin"
									allowfullscreen
								></iframe>
							</div>
							<?php if ( ! empty( $testimonial['title'] ) ) : ?>
								<p class="testimonial-video__title"><?php echo esc_html( $testimonial['title'] ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>

				<?php if ( count( $testimonials ) > 1 ) : ?>
					<div class="carousel__controls">
						<button class="carousel__btn" type="button" data-carousel-prev aria-label="<?php esc_attr_e( 'Video anterior', 'sercotec-landing' ); ?>">‹</button>
						<div class="carousel__dots" data-carousel-dots role="tablist" aria-label="<?php esc_attr_e( 'Navegación de testimonios', 'sercotec-landing' ); ?>"></div>
						<button class="carousel__btn" type="button" data-carousel-next aria-label="<?php esc_attr_e( 'Video siguiente', 'sercotec-landing' ); ?>">›</button>
					</div>
				<?php endif; ?>
			</div>
		<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>
			<p class="testimonials__empty">
				<?php
				printf(
					wp_kses_post( __( 'Aún no hay videos de testimonios. <a href="%s">Agrega el primero</a>.', 'sercotec-landing' ) ),
					esc_url( admin_url( 'post-new.php?post_type=sercotec_testimonio' ) )
				);
				?>
			</p>
		<?php endif; ?>
	</div>
</section>
