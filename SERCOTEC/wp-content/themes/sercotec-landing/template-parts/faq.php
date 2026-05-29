<?php
$faqs = sercotec_landing_get_faqs();
?>

<section class="faq section section--alt" id="faq" aria-labelledby="faq-title">
	<div class="container faq__inner">
		<div class="faq__intro">
			<?php if ( sercotec_landing_get_faq_label() ) : ?>
				<span class="section__label"><?php echo esc_html( sercotec_landing_get_faq_label() ); ?></span>
			<?php endif; ?>
			<h2 class="section__title" id="faq-title">
				<?php echo esc_html( sercotec_landing_get_faq_title() ); ?>
			</h2>
			<?php if ( sercotec_landing_get_faq_subtitle() ) : ?>
				<p class="section__subtitle">
					<?php echo esc_html( sercotec_landing_get_faq_subtitle() ); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $faqs ) ) : ?>
			<div class="faq__list" data-faq>
				<?php foreach ( $faqs as $index => $faq ) : ?>
					<div class="faq__item">
						<button
							class="faq__question"
							type="button"
							aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>"
							aria-controls="faq-answer-<?php echo esc_attr( (string) $index ); ?>"
							id="faq-question-<?php echo esc_attr( (string) $index ); ?>"
						>
							<span><?php echo esc_html( $faq['question'] ); ?></span>
							<span class="faq__icon" aria-hidden="true"></span>
						</button>
						<div
							class="faq__answer"
							id="faq-answer-<?php echo esc_attr( (string) $index ); ?>"
							role="region"
							aria-labelledby="faq-question-<?php echo esc_attr( (string) $index ); ?>"
							<?php echo 0 === $index ? '' : 'hidden'; ?>
						>
							<p><?php echo esc_html( $faq['answer'] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>
			<p class="faq__empty">
				<?php
				printf(
					wp_kses_post( __( 'Aún no hay preguntas publicadas. <a href="%s">Agrega la primera</a>.', 'sercotec-landing' ) ),
					esc_url( admin_url( 'post-new.php?post_type=sercotec_faq' ) )
				);
				?>
			</p>
		<?php endif; ?>
	</div>
</section>
