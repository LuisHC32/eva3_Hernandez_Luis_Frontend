<?php
/**
 * Landing page template.
 */

get_header();
?>

<main class="landing">
	<?php get_template_part( 'template-parts/navbar' ); ?>
	<?php get_template_part( 'template-parts/hero-section' ); ?>
	<?php get_template_part( 'template-parts/about-us' ); ?>
	<?php get_template_part( 'template-parts/services' ); ?>
	<?php get_template_part( 'template-parts/testimonials-carousel' ); ?>
	<?php get_template_part( 'template-parts/faq' ); ?>
	<?php get_template_part( 'template-parts/contact-form' ); ?>
	<?php get_template_part( 'template-parts/footer' ); ?>
</main>

<?php
get_footer();
