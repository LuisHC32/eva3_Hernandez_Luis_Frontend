<?php
/**
 * Custom Post Type: Servicios (editable desde el admin de WordPress).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_register_service_cpt() {
	$labels = array(
		'name'                  => __( 'Servicios', 'sercotec-landing' ),
		'singular_name'         => __( 'Servicio', 'sercotec-landing' ),
		'menu_name'             => __( 'Servicios', 'sercotec-landing' ),
		'add_new'               => __( 'Agregar servicio', 'sercotec-landing' ),
		'add_new_item'          => __( 'Agregar nuevo servicio', 'sercotec-landing' ),
		'edit_item'             => __( 'Editar servicio', 'sercotec-landing' ),
		'new_item'              => __( 'Nuevo servicio', 'sercotec-landing' ),
		'view_item'             => __( 'Ver servicio', 'sercotec-landing' ),
		'search_items'          => __( 'Buscar servicios', 'sercotec-landing' ),
		'not_found'             => __( 'No se encontraron servicios.', 'sercotec-landing' ),
		'not_found_in_trash'    => __( 'No hay servicios en la papelera.', 'sercotec-landing' ),
		'all_items'             => __( 'Todos los servicios', 'sercotec-landing' ),
		'featured_image'        => __( 'Imagen del servicio', 'sercotec-landing' ),
		'set_featured_image'    => __( 'Establecer imagen', 'sercotec-landing' ),
		'remove_featured_image' => __( 'Quitar imagen', 'sercotec-landing' ),
	);

	register_post_type(
		'sercotec_servicio',
		array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-portfolio',
			'menu_position'       => 21,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'show_in_rest'        => false,
		)
	);
}
add_action( 'init', 'sercotec_landing_register_service_cpt' );

function sercotec_landing_service_meta_boxes() {
	add_meta_box(
		'sercotec_service_image',
		__( 'Agregar imagen', 'sercotec-landing' ),
		'sercotec_landing_service_image_meta_box_render',
		'sercotec_servicio',
		'normal',
		'high'
	);

	add_meta_box(
		'sercotec_service_details',
		__( 'Color, layout y opciones', 'sercotec-landing' ),
		'sercotec_landing_service_details_meta_box_render',
		'sercotec_servicio',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'sercotec_landing_service_meta_boxes' );

function sercotec_landing_service_image_meta_box_render( WP_Post $post ) {
	wp_nonce_field( 'sercotec_service_meta', 'sercotec_service_meta_nonce' );

	$image_id  = (int) get_post_meta( $post->ID, '_sercotec_service_image_id', true );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
	?>
	<div class="sercotec-service-image-field">
		<input type="hidden" id="sercotec_service_image_id" name="sercotec_service_image_id" value="<?php echo esc_attr( (string) $image_id ); ?>">
		<div class="sercotec-service-image-field__preview" id="sercotec-service-image-preview">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="">
			<?php else : ?>
				<span class="sercotec-service-image-field__placeholder"><?php esc_html_e( 'Sin imagen seleccionada', 'sercotec-landing' ); ?></span>
			<?php endif; ?>
		</div>
		<p class="sercotec-service-image-field__actions">
			<button type="button" class="button button-primary" id="sercotec-service-image-select">
				<?php esc_html_e( 'Agregar imagen', 'sercotec-landing' ); ?>
			</button>
			<button type="button" class="button" id="sercotec-service-image-remove" <?php disabled( ! $image_id ); ?>>
				<?php esc_html_e( 'Quitar imagen', 'sercotec-landing' ); ?>
			</button>
		</p>
		<p class="description"><?php esc_html_e( 'Esta imagen se muestra en la parte superior del bloque.', 'sercotec-landing' ); ?></p>
	</div>
	<?php
}

function sercotec_landing_service_details_meta_box_render( WP_Post $post ) {
	$color_key = get_post_meta( $post->ID, '_sercotec_service_color_key', true );
	$color_hex = get_post_meta( $post->ID, '_sercotec_service_color_hex', true );
	$presets   = sercotec_landing_card_color_presets();

	if ( ! $color_key || ! isset( $presets[ $color_key ] ) ) {
		$color_key = 'blue';
	}

	if ( '' === $color_hex ) {
		$color_hex = $presets[ $color_key ]['hex'] ?: '#0052a3';
	}

	$layout         = sercotec_landing_sanitize_card_layout( get_post_meta( $post->ID, '_sercotec_service_layout', true ) );
	$layout_options = sercotec_landing_card_layout_options();
	?>
	<p>
		<strong><?php esc_html_e( 'Layout del bloque', 'sercotec-landing' ); ?></strong>
	</p>
	<p class="sercotec-about-card-layouts">
		<?php foreach ( $layout_options as $key => $option_label ) : ?>
			<label style="display:block;margin-bottom:0.35rem;">
				<input
					type="radio"
					name="sercotec_service_layout"
					value="<?php echo esc_attr( $key ); ?>"
					<?php checked( $layout, $key ); ?>
				>
				<?php echo esc_html( $option_label ); ?>
			</label>
		<?php endforeach; ?>
	</p>
	<p>
		<strong><?php esc_html_e( 'Color de fondo del bloque', 'sercotec-landing' ); ?></strong>
	</p>
	<p class="sercotec-about-card-colors">
		<?php foreach ( $presets as $key => $preset ) : ?>
			<label style="margin-right:1rem;">
				<input
					type="radio"
					name="sercotec_service_color_key"
					value="<?php echo esc_attr( $key ); ?>"
					<?php checked( $color_key, $key ); ?>
				>
				<?php echo esc_html( $preset['label'] ); ?>
			</label>
		<?php endforeach; ?>
	</p>
	<p>
		<label for="sercotec_service_color_hex"><strong><?php esc_html_e( 'Color de fondo personalizado', 'sercotec-landing' ); ?></strong></label><br>
		<input type="text" id="sercotec_service_color_hex" name="sercotec_service_color_hex" value="<?php echo esc_attr( $color_hex ); ?>" class="regular-text" placeholder="#0052a3">
	</p>
	<?php sercotec_landing_render_card_text_color_fields( $post, 'sercotec_service' ); ?>
	<?php
	$quick_access = sercotec_landing_service_has_quick_access( $post->ID );
	?>
	<p>
		<label>
			<input
				type="checkbox"
				name="sercotec_service_quick_access"
				value="1"
				<?php checked( $quick_access ); ?>
			>
			<strong><?php esc_html_e( 'Acceso rápido', 'sercotec-landing' ); ?></strong>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Muestra un botón Contáctanos en la tarjeta que lleva al formulario con el servicio preseleccionado.', 'sercotec-landing' ); ?>
	</p>
	<p class="description">
		<?php esc_html_e( 'Contenido = texto del bloque (editor, con formato). Título con formato = campo debajo del título. Carrusel = se agrupa con otros bloques marcados como carrusel. Orden = posición en la grilla.', 'sercotec-landing' ); ?>
	</p>
	<?php
}

function sercotec_landing_save_service_meta( int $post_id ) {
	if ( ! isset( $_POST['sercotec_service_meta_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sercotec_service_meta_nonce'] ) ), 'sercotec_service_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['sercotec_service_image_id'] ) ) {
		$image_id = absint( wp_unslash( $_POST['sercotec_service_image_id'] ) );

		if ( $image_id > 0 ) {
			update_post_meta( $post_id, '_sercotec_service_image_id', $image_id );
			set_post_thumbnail( $post_id, $image_id );
		} else {
			delete_post_meta( $post_id, '_sercotec_service_image_id' );
			delete_post_thumbnail( $post_id );
		}
	}

	$presets   = sercotec_landing_card_color_presets();
	$color_key = sanitize_key( wp_unslash( $_POST['sercotec_service_color_key'] ?? 'blue' ) );

	if ( ! isset( $presets[ $color_key ] ) ) {
		$color_key = 'blue';
	}

	update_post_meta( $post_id, '_sercotec_service_color_key', $color_key );

	$fallback  = $presets[ $color_key ]['hex'] ?: '#0052a3';
	$color_hex = sercotec_landing_sanitize_card_color_hex(
		(string) wp_unslash( $_POST['sercotec_service_color_hex'] ?? '' ),
		$fallback
	);

	if ( 'custom' !== $color_key && $presets[ $color_key ]['hex'] ) {
		$color_hex = $presets[ $color_key ]['hex'];
	}

	update_post_meta( $post_id, '_sercotec_service_color_hex', $color_hex );

	sercotec_landing_save_card_text_color_meta( $post_id, 'sercotec_service' );

	update_post_meta(
		$post_id,
		'_sercotec_service_quick_access',
		isset( $_POST['sercotec_service_quick_access'] ) ? '1' : '0'
	);

	update_post_meta(
		$post_id,
		'_sercotec_service_layout',
		sercotec_landing_sanitize_card_layout( wp_unslash( $_POST['sercotec_service_layout'] ?? '2x1' ) )
	);
}
add_action( 'save_post_sercotec_servicio', 'sercotec_landing_save_service_meta' );

function sercotec_landing_service_has_quick_access( int $post_id ): bool {
	return '1' === get_post_meta( $post_id, '_sercotec_service_quick_access', true );
}

function sercotec_landing_service_admin_columns( array $columns ): array {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['service_thumb'] = __( 'Imagen', 'sercotec-landing' );
			$new['service_layout'] = __( 'Layout', 'sercotec-landing' );
			$new['service_color'] = __( 'Color', 'sercotec-landing' );
			$new['service_order'] = __( 'Orden', 'sercotec-landing' );
		}
	}

	return $new;
}
add_filter( 'manage_sercotec_servicio_posts_columns', 'sercotec_landing_service_admin_columns' );

function sercotec_landing_service_admin_column_content( string $column, int $post_id ): void {
	if ( 'service_thumb' === $column ) {
		$image_id = (int) get_post_meta( $post_id, '_sercotec_service_image_id', true );

		if ( ! $image_id ) {
			$image_id = (int) get_post_thumbnail_id( $post_id );
		}

		if ( $image_id ) {
			echo wp_get_attachment_image( $image_id, array( 48, 48 ) );
		} else {
			echo '<span aria-hidden="true">—</span>';
		}
	}

	if ( 'service_layout' === $column ) {
		$layout = sercotec_landing_sanitize_card_layout( get_post_meta( $post_id, '_sercotec_service_layout', true ) );
		echo esc_html( strtoupper( $layout ) );
	}

	if ( 'service_color' === $column ) {
		$color = get_post_meta( $post_id, '_sercotec_service_color_hex', true ) ?: '#0052a3';
		echo '<span style="display:inline-block;width:18px;height:18px;border-radius:3px;background:' . esc_attr( $color ) . ';border:1px solid #ccc;" aria-hidden="true"></span>';
	}

	if ( 'service_order' === $column ) {
		echo esc_html( (string) get_post( $post_id )->menu_order );
	}
}
add_action( 'manage_sercotec_servicio_posts_custom_column', 'sercotec_landing_service_admin_column_content', 10, 2 );

function sercotec_landing_get_services(): array {
	$query = new WP_Query(
		array(
			'post_type'      => 'sercotec_servicio',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);

	$services = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id  = get_the_ID();
			$image_id = (int) get_post_meta( $post_id, '_sercotec_service_image_id', true );

			if ( ! $image_id ) {
				$image_id = (int) get_post_thumbnail_id( $post_id );
			}

			$color = get_post_meta( $post_id, '_sercotec_service_color_hex', true ) ?: '#0052a3';

			$services[] = array(
				'title'        => get_the_title( $post_id ),
				'title_html'   => sercotec_landing_get_card_stored_title_html( $post_id ),
				'image'        => $image_id ? wp_get_attachment_image_url( $image_id, 'sercotec-about-card' ) : '',
				'content_html' => sercotec_landing_get_card_rich_content( $post_id ),
				'color'        => sercotec_landing_sanitize_card_color_hex( $color ),
				'text_color'   => sercotec_landing_get_card_text_color( $post_id, 'service' ),
				'layout'       => sercotec_landing_sanitize_card_layout( get_post_meta( $post_id, '_sercotec_service_layout', true ) ),
				'order'        => (int) get_post( $post_id )->menu_order,
				'quick_access' => sercotec_landing_service_has_quick_access( $post_id ),
			);
		}
		wp_reset_postdata();
	}

	return $services;
}

/**
 * @return array<int, string>
 */
function sercotec_landing_get_contact_form_services(): array {
	$query = new WP_Query(
		array(
			'post_type'      => 'sercotec_servicio',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'   => '_sercotec_service_quick_access',
					'value' => '1',
				),
			),
		)
	);

	$titles = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$title = get_the_title();

			if ( '' !== $title ) {
				$titles[] = $title;
			}
		}
		wp_reset_postdata();
	}

	return $titles;
}

function sercotec_landing_seed_default_services(): void {
	$existing = get_posts(
		array(
			'post_type'      => 'sercotec_servicio',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing ) ) {
		return;
	}

	$presets = sercotec_landing_card_color_presets();

	$defaults = array(
		array(
			'title'     => 'Capital Semilla',
			'content'   => 'Financiamiento no reembolsable para proyectos innovadores en etapa temprana.',
			'order'     => 1,
			'color_key' => 'blue',
		),
		array(
			'title'     => 'Consultoría Técnica',
			'content'   => 'Diagnóstico empresarial y planes de mejora en gestión, ventas y procesos.',
			'order'     => 2,
			'color_key' => 'red',
		),
		array(
			'title'     => 'Internacionalización',
			'content'   => 'Apoyo para exportar productos y servicios con foco en mercados estratégicos.',
			'order'     => 3,
			'color_key' => 'blue',
		),
		array(
			'title'     => 'Capacitación',
			'content'   => 'Talleres y bootcamps en habilidades digitales, financieras y de liderazgo.',
			'order'     => 4,
			'color_key' => 'red',
		),
		array(
			'title'     => 'Red de Mentores',
			'content'   => 'Conexión con expertos del sector privado para acelerar decisiones clave.',
			'order'     => 5,
			'color_key' => 'blue',
		),
		array(
			'title'     => 'Formalización Pyme',
			'content'   => 'Asesoría para cumplir requisitos legales, tributarios y de calidad.',
			'order'     => 6,
			'color_key' => 'red',
		),
	);

	foreach ( $defaults as $service ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'sercotec_servicio',
				'post_title'   => $service['title'],
				'post_content' => $service['content'],
				'post_status'  => 'publish',
				'menu_order'   => $service['order'],
			),
			true
		);

		if ( ! is_wp_error( $post_id ) ) {
			$color_key = $service['color_key'];
			$color_hex = $presets[ $color_key ]['hex'] ?: '#0052a3';

			update_post_meta( $post_id, '_sercotec_service_color_key', $color_key );
			update_post_meta( $post_id, '_sercotec_service_color_hex', $color_hex );
		}
	}
}
add_action( 'after_switch_theme', 'sercotec_landing_seed_default_services' );
