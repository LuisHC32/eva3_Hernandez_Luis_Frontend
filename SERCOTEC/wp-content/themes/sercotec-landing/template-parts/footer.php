<?php

$footer_links  = sercotec_landing_get_footer_links();

$social_links  = array_values(

	array_filter(

		sercotec_landing_get_footer_social_links(),

		'sercotec_landing_footer_social_has_icon'

	)

);

?>

<footer class="site-footer">

	<div class="container site-footer__inner">

		<div class="site-footer__brand">

			<?php get_template_part( 'template-parts/brand-mark' ); ?>

			<?php if ( sercotec_landing_get_footer_description() ) : ?>

				<p><?php echo esc_html( sercotec_landing_get_footer_description() ); ?></p>

			<?php endif; ?>

		</div>



		<?php if ( ! empty( $footer_links ) ) : ?>

			<div class="site-footer__links">

				<?php if ( sercotec_landing_get_footer_links_title() ) : ?>

					<h3><?php echo esc_html( sercotec_landing_get_footer_links_title() ); ?></h3>

				<?php endif; ?>

				<ul>

					<?php foreach ( $footer_links as $link ) : ?>

						<li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a></li>

					<?php endforeach; ?>

				</ul>

			</div>

		<?php endif; ?>



		<?php if ( ! empty( $social_links ) ) : ?>

			<div class="site-footer__social">

				<?php if ( sercotec_landing_get_footer_social_title() ) : ?>

					<h3><?php echo esc_html( sercotec_landing_get_footer_social_title() ); ?></h3>

				<?php endif; ?>

				<div class="site-footer__social-links">

					<?php foreach ( $social_links as $social ) : ?>

						<?php if ( ! empty( $social['url'] ) && '#' !== $social['url'] ) : ?>

							<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sercotec_landing_get_footer_social_aria_label( $social ) ); ?>">

								<?php sercotec_landing_render_footer_social_icon( $social ); ?>

							</a>

						<?php else : ?>

							<span class="site-footer__social-placeholder" aria-label="<?php echo esc_attr( sercotec_landing_get_footer_social_aria_label( $social ) ); ?>">

								<?php sercotec_landing_render_footer_social_icon( $social ); ?>

							</span>

						<?php endif; ?>

					<?php endforeach; ?>

				</div>

			</div>

		<?php endif; ?>

	</div>

	<div class="site-footer__bottom">

		<div class="container">

			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( sercotec_landing_get_footer_copyright() ); ?></p>

		</div>

	</div>

</footer>

