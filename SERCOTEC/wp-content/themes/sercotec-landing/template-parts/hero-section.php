<?php
$hero_stats = sercotec_landing_get_hero_stats();
?>

<section class="hero" id="inicio" aria-labelledby="hero-title">
	<div class="hero__bg"></div>
	<div class="container hero__inner">
		<div class="hero__content">
			<?php if ( sercotec_landing_get_hero_badge() ) : ?>
				<span class="hero__badge"><?php echo esc_html( sercotec_landing_get_hero_badge() ); ?></span>
			<?php endif; ?>
			<h1 class="hero__title" id="hero-title">
				<?php echo esc_html( sercotec_landing_get_hero_title() ); ?>
			</h1>
			<?php if ( sercotec_landing_get_hero_description() ) : ?>
				<p class="hero__description">
					<?php echo esc_html( sercotec_landing_get_hero_description() ); ?>
				</p>
			<?php endif; ?>
			<div class="hero__actions">
				<?php if ( sercotec_landing_get_hero_primary_btn_text() ) : ?>
					<a class="btn btn--primary btn--lg" href="<?php echo esc_attr( sercotec_landing_get_hero_primary_btn_url() ); ?>">
						<?php echo esc_html( sercotec_landing_get_hero_primary_btn_text() ); ?>
					</a>
				<?php endif; ?>
				<?php if ( sercotec_landing_get_hero_secondary_btn_text() ) : ?>
					<a class="btn btn--outline btn--lg" href="<?php echo esc_attr( sercotec_landing_get_hero_secondary_btn_url() ); ?>">
						<?php echo esc_html( sercotec_landing_get_hero_secondary_btn_text() ); ?>
					</a>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( array_filter( $hero_stats, static fn( $stat ) => '' !== $stat['value'] || '' !== $stat['label'] ) ) ) : ?>
				<ul class="hero__stats">
					<?php foreach ( $hero_stats as $stat ) : ?>
						<?php if ( '' === $stat['value'] && '' === $stat['label'] ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<li>
							<?php if ( $stat['value'] ) : ?>
								<strong><?php echo esc_html( $stat['value'] ); ?></strong>
							<?php endif; ?>
							<?php if ( $stat['label'] ) : ?>
								<span><?php echo esc_html( $stat['label'] ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<div class="hero__visual" aria-hidden="true">
			<?php if ( sercotec_landing_get_hero_card_main_title() || sercotec_landing_get_hero_card_main_text() ) : ?>
				<div class="hero__card hero__card--main">
					<?php if ( sercotec_landing_get_hero_card_main_title() ) : ?>
						<h3><?php echo esc_html( sercotec_landing_get_hero_card_main_title() ); ?></h3>
					<?php endif; ?>
					<?php if ( sercotec_landing_get_hero_card_main_text() ) : ?>
						<p><?php echo esc_html( sercotec_landing_get_hero_card_main_text() ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( sercotec_landing_get_hero_card_secondary_title() || sercotec_landing_get_hero_card_secondary_text() ) : ?>
				<div class="hero__card hero__card--secondary">
					<?php if ( sercotec_landing_get_hero_card_secondary_title() ) : ?>
						<h3><?php echo esc_html( sercotec_landing_get_hero_card_secondary_title() ); ?></h3>
					<?php endif; ?>
					<?php if ( sercotec_landing_get_hero_card_secondary_text() ) : ?>
						<p><?php echo esc_html( sercotec_landing_get_hero_card_secondary_text() ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
