<?php
/**
 * Custom Post Type: Testimonios (videos de YouTube).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_register_testimonial_cpt() {
	$labels = array(
		'name'               => __( 'Testimonios', 'sercotec-landing' ),
		'singular_name'      => __( 'Testimonio', 'sercotec-landing' ),
		'menu_name'          => __( 'Testimonios', 'sercotec-landing' ),
		'add_new'            => __( 'Agregar testimonio', 'sercotec-landing' ),
		'add_new_item'       => __( 'Agregar nuevo testimonio', 'sercotec-landing' ),
		'edit_item'          => __( 'Editar testimonio', 'sercotec-landing' ),
		'new_item'           => __( 'Nuevo testimonio', 'sercotec-landing' ),
		'view_item'          => __( 'Ver testimonio', 'sercotec-landing' ),
		'search_items'       => __( 'Buscar testimonios', 'sercotec-landing' ),
		'not_found'          => __( 'No se encontraron testimonios.', 'sercotec-landing' ),
		'not_found_in_trash' => __( 'No hay testimonios en la papelera.', 'sercotec-landing' ),
		'all_items'          => __( 'Todos los testimonios', 'sercotec-landing' ),
	);

	register_post_type(
		'sercotec_testimonio',
		array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-video-alt3',
			'menu_position'       => 24,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'page-attributes' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'show_in_rest'        => false,
		)
	);
}
add_action( 'init', 'sercotec_landing_register_testimonial_cpt' );

function sercotec_landing_parse_youtube_id( string $url ): string {
	$url = trim( $url );

	if ( '' === $url ) {
		return '';
	}

	if ( preg_match( '/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches ) ) {
		return $matches[1];
	}

	if ( preg_match( '/^[a-zA-Z0-9_-]{11}$/', $url ) ) {
		return $url;
	}

	return '';
}

function sercotec_landing_get_youtube_embed_url( string $video_id ): string {
	return 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $video_id ) . '?rel=0&modestbranding=1';
}

function sercotec_landing_get_youtube_watch_url( string $video_id ): string {
	return 'https://www.youtube.com/watch?v=' . rawurlencode( $video_id );
}

function sercotec_landing_testimonial_meta_boxes() {
	add_meta_box(
		'sercotec_testimonial_video',
		__( 'Video de YouTube', 'sercotec-landing' ),
		'sercotec_landing_testimonial_meta_box_render',
		'sercotec_testimonio',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'sercotec_landing_testimonial_meta_boxes' );

function sercotec_landing_testimonial_meta_box_render( WP_Post $post ) {
	wp_nonce_field( 'sercotec_testimonial_meta', 'sercotec_testimonial_meta_nonce' );

	$youtube_url = get_post_meta( $post->ID, '_sercotec_testimonial_youtube_url', true );
	$video_id    = sercotec_landing_parse_youtube_id( $youtube_url );
	?>
	<p>
		<label for="sercotec_testimonial_youtube_url"><strong><?php esc_html_e( 'URL del video', 'sercotec-landing' ); ?></strong></label><br>
		<input
			type="url"
			id="sercotec_testimonial_youtube_url"
			name="sercotec_testimonial_youtube_url"
			value="<?php echo esc_attr( $youtube_url ); ?>"
			class="widefat"
			placeholder="https://www.youtube.com/watch?v=..."
		>
		<span class="description"><?php esc_html_e( 'Pega el enlace completo de YouTube o youtu.be.', 'sercotec-landing' ); ?></span>
	</p>
	<?php if ( $video_id ) : ?>
		<p>
			<strong><?php esc_html_e( 'Vista previa', 'sercotec-landing' ); ?></strong>
		</p>
		<div style="max-width:480px;aspect-ratio:16/9;">
			<iframe
				src="<?php echo esc_url( sercotec_landing_get_youtube_embed_url( $video_id ) ); ?>"
				title="<?php echo esc_attr( get_the_title( $post ) ); ?>"
				style="width:100%;height:100%;border:0;border-radius:8px;"
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
				allowfullscreen
			></iframe>
		</div>
	<?php endif; ?>
	<p class="description">
		<?php esc_html_e( 'Título = nombre del testimonio. Orden = posición en el carrusel.', 'sercotec-landing' ); ?>
	</p>
	<?php
}

function sercotec_landing_save_testimonial_meta( int $post_id ) {
	if ( ! isset( $_POST['sercotec_testimonial_meta_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sercotec_testimonial_meta_nonce'] ) ), 'sercotec_testimonial_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['sercotec_testimonial_youtube_url'] ) ) {
		$url = esc_url_raw( trim( wp_unslash( $_POST['sercotec_testimonial_youtube_url'] ) ) );
		update_post_meta( $post_id, '_sercotec_testimonial_youtube_url', $url );
	}
}
add_action( 'save_post_sercotec_testimonio', 'sercotec_landing_save_testimonial_meta' );

function sercotec_landing_testimonial_admin_columns( array $columns ): array {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['testimonial_video'] = __( 'Video', 'sercotec-landing' );
			$new['testimonial_order'] = __( 'Orden', 'sercotec-landing' );
		}
	}

	return $new;
}
add_filter( 'manage_sercotec_testimonio_posts_columns', 'sercotec_landing_testimonial_admin_columns' );

function sercotec_landing_testimonial_admin_column_content( string $column, int $post_id ): void {
	if ( 'testimonial_video' === $column ) {
		$url      = get_post_meta( $post_id, '_sercotec_testimonial_youtube_url', true );
		$video_id = sercotec_landing_parse_youtube_id( $url );

		if ( $video_id ) {
			printf(
				'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
				esc_url( sercotec_landing_get_youtube_watch_url( $video_id ) ),
				esc_html__( 'Ver en YouTube', 'sercotec-landing' )
			);
		} else {
			echo '<span aria-hidden="true">—</span>';
		}
	}

	if ( 'testimonial_order' === $column ) {
		echo esc_html( (string) get_post( $post_id )->menu_order );
	}
}
add_action( 'manage_sercotec_testimonio_posts_custom_column', 'sercotec_landing_testimonial_admin_column_content', 10, 2 );

function sercotec_landing_get_testimonials(): array {
	$query = new WP_Query(
		array(
			'post_type'      => 'sercotec_testimonio',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);

	$testimonials = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id  = get_the_ID();
			$url      = get_post_meta( $post_id, '_sercotec_testimonial_youtube_url', true );
			$video_id = sercotec_landing_parse_youtube_id( $url );

			if ( ! $video_id ) {
				continue;
			}

			$testimonials[] = array(
				'title'     => get_the_title(),
				'video_id'  => $video_id,
				'embed_url' => sercotec_landing_get_youtube_embed_url( $video_id ),
			);
		}
		wp_reset_postdata();
	}

	return $testimonials;
}
