<header class="navbar" id="inicio">
	<div class="container navbar__inner">
		<?php get_template_part( 'template-parts/brand-mark' ); ?>

		<button class="navbar__toggle" type="button" aria-label="<?php esc_attr_e( 'Abrir menú', 'sercotec-landing' ); ?>" aria-expanded="false" aria-controls="navbar-menu">
			<span></span>
			<span></span>
			<span></span>
		</button>

		<nav class="navbar__nav" id="navbar-menu" aria-label="<?php esc_attr_e( 'Navegación principal', 'sercotec-landing' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'navbar__menu',
						'fallback_cb'    => false,
					)
				);
			} else {
				sercotec_landing_default_menu();
			}
			?>
			<a class="btn btn--primary navbar__cta" href="#contacto"><?php esc_html_e( 'Postula ahora', 'sercotec-landing' ); ?></a>
		</nav>
	</div>
</header>
