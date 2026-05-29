<?php
/**
 * Custom Post Type: Cards de Nosotros.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_register_about_card_cpt() {
	$labels = array(
		'name'               => __( 'Nosotros', 'sercotec-landing' ),
		'singular_name'      => __( 'Nosotro', 'sercotec-landing' ),
		'menu_name'          => __( 'Nosotros', 'sercotec-landing' ),
		'add_new'            => __( 'Agregar nosotro', 'sercotec-landing' ),
		'add_new_item'       => __( 'Agregar nuevo nosotro', 'sercotec-landing' ),
		'edit_item'          => __( 'Editar nosotro', 'sercotec-landing' ),
		'new_item'           => __( 'Nuevo nosotro', 'sercotec-landing' ),
		'view_item'          => __( 'Ver nosotro', 'sercotec-landing' ),
		'search_items'       => __( 'Buscar nosotros', 'sercotec-landing' ),
		'not_found'          => __( 'No se encontraron nosotros.', 'sercotec-landing' ),
		'not_found_in_trash' => __( 'No hay nosotros en la papelera.', 'sercotec-landing' ),
		'all_items'          => __( 'Todos los nosotros', 'sercotec-landing' ),
	);

	register_post_type(
		'sercotec_about_card',
		array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-groups',
			'menu_position'       => 22,
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
add_action( 'init', 'sercotec_landing_register_about_card_cpt' );

function sercotec_landing_card_layout_options(): array {
	return array(
		'2x1'      => __( '2×1 — Ancho completo (2 espacios, imagen arriba, texto abajo)', 'sercotec-landing' ),
		'1x1'      => __( '1×1 — Un espacio (imagen arriba, texto abajo)', 'sercotec-landing' ),
		'1x2'      => __( '1×2 — Un espacio (imagen izquierda, texto derecha)', 'sercotec-landing' ),
		'carousel' => __( 'Carrusel — Se agrupa con otros bloques marcados como carrusel', 'sercotec-landing' ),
	);
}

/**
 * Agrupa tarjetas en bloques sueltos o carruseles según layout y orden.
 *
 * @param array<int, array<string, mixed>> $cards Tarjetas en orden de visualización.
 * @return array<int, array<string, mixed>>
 */
function sercotec_landing_group_cards_for_display( array $cards ): array {
	$groups          = array();
	$carousel_buffer = array();

	foreach ( $cards as $card ) {
		if ( 'carousel' === ( $card['layout'] ?? '' ) ) {
			$carousel_buffer[] = $card;
			continue;
		}

		if ( ! empty( $carousel_buffer ) ) {
			$groups[]        = array(
				'type'  => 'carousel',
				'items' => $carousel_buffer,
			);
			$carousel_buffer = array();
		}

		$groups[] = array(
			'type' => 'card',
			'item' => $card,
		);
	}

	if ( ! empty( $carousel_buffer ) ) {
		$groups[] = array(
			'type'  => 'carousel',
			'items' => $carousel_buffer,
		);
	}

	return $groups;
}

function sercotec_landing_sanitize_card_layout( $value ): string {
	$allowed = array_keys( sercotec_landing_card_layout_options() );
	$value   = sanitize_key( (string) $value );

	return in_array( $value, $allowed, true ) ? $value : '2x1';
}

function sercotec_landing_card_post_types_with_title_editor(): array {
	return array( 'sercotec_servicio', 'sercotec_about_card' );
}

function sercotec_landing_get_card_editor_content( int $post_id = 0 ): string {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return trim( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ) );
}

function sercotec_landing_get_card_rich_content( int $post_id = 0 ): string {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$raw = (string) get_post_field( 'post_content', $post_id );

	if ( '' === trim( $raw ) ) {
		return '';
	}

	return apply_filters( 'the_content', $raw );
}

function sercotec_landing_get_card_stored_title_html( int $post_id = 0 ): string {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return trim( (string) get_post_meta( $post_id, '_sercotec_card_title_html', true ) );
}

function sercotec_landing_get_card_display_content( int $post_id = 0 ): string {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$content = sercotec_landing_get_card_editor_content( $post_id );

	if ( '' === $content ) {
		$content = get_the_title( $post_id );
	}

	return $content;
}

function sercotec_landing_card_title_editor_render( WP_Post $post ): void {
	if ( ! in_array( $post->post_type, sercotec_landing_card_post_types_with_title_editor(), true ) ) {
		return;
	}

	$html = sercotec_landing_get_card_stored_title_html( $post->ID );

	if ( '' === $html && '' !== $post->post_title ) {
		$html = esc_html( $post->post_title );
	}
	?>
	<div class="sercotec-card-title-editor">
		<label for="sercotec_card_title_html"><strong><?php esc_html_e( 'Título en la tarjeta (con formato)', 'sercotec-landing' ); ?></strong></label>
		<?php
		wp_editor(
			$html,
			'sercotec_card_title_html',
			array(
				'textarea_name' => 'sercotec_card_title_html',
				'textarea_rows' => 4,
				'media_buttons' => false,
				'teeny'         => false,
				'quicktags'     => true,
			)
		);
		?>
		<p class="description">
			<?php esc_html_e( 'Negrita, centrado, listas, etc. Si está vacío, se usa el título de arriba sin formato.', 'sercotec-landing' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'edit_form_after_title', 'sercotec_landing_card_title_editor_render' );

function sercotec_landing_save_card_title_html( int $post_id, WP_Post $post ): void {
	if ( ! in_array( $post->post_type, sercotec_landing_card_post_types_with_title_editor(), true ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['sercotec_card_title_html'] ) ) {
		return;
	}

	update_post_meta(
		$post_id,
		'_sercotec_card_title_html',
		wp_kses_post( wp_unslash( $_POST['sercotec_card_title_html'] ) )
	);
}
add_action( 'save_post', 'sercotec_landing_save_card_title_html', 10, 2 );

function sercotec_landing_card_color_presets(): array {
	return array(
		'blue'   => array(
			'label' => __( 'Azul', 'sercotec-landing' ),
			'hex'   => '#0052a3',
		),
		'red'    => array(
			'label' => __( 'Rojo', 'sercotec-landing' ),
			'hex'   => '#d62828',
		),
		'white'  => array(
			'label' => __( 'Blanco', 'sercotec-landing' ),
			'hex'   => '#ffffff',
		),
		'custom' => array(
			'label' => __( 'Personalizado', 'sercotec-landing' ),
			'hex'   => '',
		),
	);
}

function sercotec_landing_card_text_color_presets(): array {
	return array(
		'white'  => array(
			'label' => __( 'Blanco', 'sercotec-landing' ),
			'hex'   => '#ffffff',
		),
		'black'  => array(
			'label' => __( 'Negro', 'sercotec-landing' ),
			'hex'   => '#1a2b3c',
		),
		'custom' => array(
			'label' => __( 'Personalizado', 'sercotec-landing' ),
			'hex'   => '',
		),
	);
}

function sercotec_landing_is_light_color_hex( string $hex ): bool {
	$hex = ltrim( sanitize_hex_color( $hex ) ?: '#000000', '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) ) {
		return false;
	}

	$red   = hexdec( substr( $hex, 0, 2 ) );
	$green = hexdec( substr( $hex, 2, 2 ) );
	$blue  = hexdec( substr( $hex, 4, 2 ) );
	$luma  = ( 0.299 * $red + 0.587 * $green + 0.114 * $blue ) / 255;

	return $luma >= 0.62;
}

function sercotec_landing_resolve_card_text_color( string $color_key, string $color_hex, string $fallback = '#ffffff' ): string {
	$presets = sercotec_landing_card_text_color_presets();

	if ( ! isset( $presets[ $color_key ] ) ) {
		$color_key = 'white';
	}

	if ( 'custom' !== $color_key && $presets[ $color_key ]['hex'] ) {
		return $presets[ $color_key ]['hex'];
	}

	return sercotec_landing_sanitize_card_color_hex( $color_hex, $fallback );
}

function sercotec_landing_get_card_text_color( int $post_id, string $type ): string {
	$key_meta = 'about_card' === $type ? '_sercotec_about_card_text_color_key' : '_sercotec_service_text_color_key';
	$hex_meta = 'about_card' === $type ? '_sercotec_about_card_text_color_hex' : '_sercotec_service_text_color_hex';
	$color_key = (string) get_post_meta( $post_id, $key_meta, true );
	$color_hex = (string) get_post_meta( $post_id, $hex_meta, true );

	if ( '' === $color_key && '' === $color_hex ) {
		return '#ffffff';
	}

	return sercotec_landing_resolve_card_text_color( $color_key, $color_hex );
}

function sercotec_landing_save_card_text_color_meta( int $post_id, string $field_prefix ): void {
	$key_field = $field_prefix . '_text_color_key';
	$hex_field = $field_prefix . '_text_color_hex';

	if ( ! isset( $_POST[ $key_field ] ) ) {
		return;
	}

	$presets   = sercotec_landing_card_text_color_presets();
	$color_key = sanitize_key( wp_unslash( $_POST[ $key_field ] ) );

	if ( ! isset( $presets[ $color_key ] ) ) {
		$color_key = 'white';
	}

	update_post_meta( $post_id, '_' . $field_prefix . '_text_color_key', $color_key );

	$fallback  = $presets[ $color_key ]['hex'] ?: '#ffffff';
	$color_hex = sercotec_landing_sanitize_card_color_hex(
		(string) wp_unslash( $_POST[ $hex_field ] ?? '' ),
		$fallback
	);

	if ( 'custom' !== $color_key && $presets[ $color_key ]['hex'] ) {
		$color_hex = $presets[ $color_key ]['hex'];
	}

	update_post_meta( $post_id, '_' . $field_prefix . '_text_color_hex', $color_hex );
}

function sercotec_landing_render_card_text_color_fields( WP_Post $post, string $field_prefix ): void {
	$color_key = get_post_meta( $post->ID, '_' . $field_prefix . '_text_color_key', true );
	$color_hex = get_post_meta( $post->ID, '_' . $field_prefix . '_text_color_hex', true );
	$presets   = sercotec_landing_card_text_color_presets();

	if ( ! $color_key || ! isset( $presets[ $color_key ] ) ) {
		$color_key = 'white';
	}

	if ( '' === $color_hex ) {
		$color_hex = $presets[ $color_key ]['hex'] ?: '#ffffff';
	}
	?>
	<p>
		<strong><?php esc_html_e( 'Color del texto', 'sercotec-landing' ); ?></strong>
	</p>
	<p class="sercotec-about-card-colors">
		<?php foreach ( $presets as $key => $preset ) : ?>
			<label style="margin-right:1rem;">
				<input
					type="radio"
					name="<?php echo esc_attr( $field_prefix . '_text_color_key' ); ?>"
					value="<?php echo esc_attr( $key ); ?>"
					<?php checked( $color_key, $key ); ?>
				>
				<?php echo esc_html( $preset['label'] ); ?>
			</label>
		<?php endforeach; ?>
	</p>
	<p>
		<label for="<?php echo esc_attr( $field_prefix . '_text_color_hex' ); ?>">
			<strong><?php esc_html_e( 'Color de texto personalizado', 'sercotec-landing' ); ?></strong>
		</label><br>
		<input
			type="text"
			id="<?php echo esc_attr( $field_prefix . '_text_color_hex' ); ?>"
			name="<?php echo esc_attr( $field_prefix . '_text_color_hex' ); ?>"
			value="<?php echo esc_attr( $color_hex ); ?>"
			class="regular-text"
			placeholder="#1a2b3c"
		>
	</p>
	<p class="description">
		<?php esc_html_e( 'Ejemplo: fondo blanco + texto negro = selecciona Blanco en fondo y Negro en texto.', 'sercotec-landing' ); ?>
	</p>
	<?php
}

function sercotec_landing_about_card_meta_boxes() {
	add_meta_box(
		'sercotec_about_card_image',
		__( 'Agregar imagen', 'sercotec-landing' ),
		'sercotec_landing_about_card_image_meta_box_render',
		'sercotec_about_card',
		'normal',
		'high'
	);

	add_meta_box(
		'sercotec_about_card_details',
		__( 'Color, layout y opciones', 'sercotec-landing' ),
		'sercotec_landing_about_card_details_meta_box_render',
		'sercotec_about_card',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'sercotec_landing_about_card_meta_boxes' );

function sercotec_landing_about_card_image_meta_box_render( WP_Post $post ) {
	wp_nonce_field( 'sercotec_about_card_meta', 'sercotec_about_card_meta_nonce' );

	$image_id  = (int) get_post_meta( $post->ID, '_sercotec_about_card_image_id', true );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
	?>
	<div class="sercotec-service-image-field">
		<input type="hidden" id="sercotec_about_card_image_id" name="sercotec_about_card_image_id" value="<?php echo esc_attr( (string) $image_id ); ?>">
		<div class="sercotec-service-image-field__preview" id="sercotec-about-card-image-preview">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="">
			<?php else : ?>
				<span class="sercotec-service-image-field__placeholder"><?php esc_html_e( 'Sin imagen seleccionada', 'sercotec-landing' ); ?></span>
			<?php endif; ?>
		</div>
		<p class="sercotec-service-image-field__actions">
			<button type="button" class="button button-primary" id="sercotec-about-card-image-select">
				<?php esc_html_e( 'Agregar imagen', 'sercotec-landing' ); ?>
			</button>
			<button type="button" class="button" id="sercotec-about-card-image-remove" <?php disabled( ! $image_id ); ?>>
				<?php esc_html_e( 'Quitar imagen', 'sercotec-landing' ); ?>
			</button>
		</p>
		<p class="description"><?php esc_html_e( 'Esta imagen se muestra en la parte superior del bloque.', 'sercotec-landing' ); ?></p>
	</div>
	<?php
}

function sercotec_landing_about_card_details_meta_box_render( WP_Post $post ) {
	$color_key = get_post_meta( $post->ID, '_sercotec_about_card_color_key', true );
	$color_hex = get_post_meta( $post->ID, '_sercotec_about_card_color_hex', true );
	$presets   = sercotec_landing_card_color_presets();

	if ( ! $color_key || ! isset( $presets[ $color_key ] ) ) {
		$color_key = 'blue';
	}

	if ( '' === $color_hex ) {
		$color_hex = $presets[ $color_key ]['hex'] ?: '#0052a3';
	}

	$layout         = sercotec_landing_sanitize_card_layout( get_post_meta( $post->ID, '_sercotec_about_card_layout', true ) );
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
					name="sercotec_about_card_layout"
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
					name="sercotec_about_card_color_key"
					value="<?php echo esc_attr( $key ); ?>"
					<?php checked( $color_key, $key ); ?>
				>
				<?php echo esc_html( $preset['label'] ); ?>
			</label>
		<?php endforeach; ?>
	</p>
	<p>
		<label for="sercotec_about_card_color_hex"><strong><?php esc_html_e( 'Color de fondo personalizado', 'sercotec-landing' ); ?></strong></label><br>
		<input type="text" id="sercotec_about_card_color_hex" name="sercotec_about_card_color_hex" value="<?php echo esc_attr( $color_hex ); ?>" class="regular-text" placeholder="#0052a3">
	</p>
	<?php sercotec_landing_render_card_text_color_fields( $post, 'sercotec_about_card' ); ?>
	<p class="description">
		<?php esc_html_e( 'Contenido = texto del bloque (editor, con formato). Título con formato = campo debajo del título. Carrusel = se agrupa con otros bloques marcados como carrusel. Orden = posición en la grilla.', 'sercotec-landing' ); ?>
	</p>
	<?php
}

function sercotec_landing_sanitize_card_color_hex( string $hex, string $fallback = '#0052a3' ): string {
	$hex = sanitize_hex_color( $hex );

	return $hex ?: $fallback;
}

function sercotec_landing_save_about_card_meta( int $post_id ) {
	if ( ! isset( $_POST['sercotec_about_card_meta_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sercotec_about_card_meta_nonce'] ) ), 'sercotec_about_card_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['sercotec_about_card_image_id'] ) ) {
		$image_id = absint( wp_unslash( $_POST['sercotec_about_card_image_id'] ) );

		if ( $image_id > 0 ) {
			update_post_meta( $post_id, '_sercotec_about_card_image_id', $image_id );
			set_post_thumbnail( $post_id, $image_id );
		} else {
			delete_post_meta( $post_id, '_sercotec_about_card_image_id' );
			delete_post_thumbnail( $post_id );
		}
	}

	$presets   = sercotec_landing_card_color_presets();
	$color_key = sanitize_key( wp_unslash( $_POST['sercotec_about_card_color_key'] ?? 'blue' ) );

	if ( ! isset( $presets[ $color_key ] ) ) {
		$color_key = 'blue';
	}

	update_post_meta( $post_id, '_sercotec_about_card_color_key', $color_key );

	$fallback = $presets[ $color_key ]['hex'] ?: '#0052a3';
	$color_hex = sercotec_landing_sanitize_card_color_hex(
		(string) wp_unslash( $_POST['sercotec_about_card_color_hex'] ?? '' ),
		$fallback
	);

	if ( 'custom' !== $color_key && $presets[ $color_key ]['hex'] ) {
		$color_hex = $presets[ $color_key ]['hex'];
	}

	update_post_meta( $post_id, '_sercotec_about_card_color_hex', $color_hex );

	sercotec_landing_save_card_text_color_meta( $post_id, 'sercotec_about_card' );

	update_post_meta(
		$post_id,
		'_sercotec_about_card_layout',
		sercotec_landing_sanitize_card_layout( wp_unslash( $_POST['sercotec_about_card_layout'] ?? '2x1' ) )
	);
}
add_action( 'save_post_sercotec_about_card', 'sercotec_landing_save_about_card_meta' );

function sercotec_landing_about_card_admin_columns( array $columns ): array {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['about_card_thumb']  = __( 'Imagen', 'sercotec-landing' );
			$new['about_card_layout'] = __( 'Layout', 'sercotec-landing' );
			$new['about_card_color']  = __( 'Color', 'sercotec-landing' );
			$new['about_card_order']  = __( 'Orden', 'sercotec-landing' );
		}
	}

	return $new;
}
add_filter( 'manage_sercotec_about_card_posts_columns', 'sercotec_landing_about_card_admin_columns' );

function sercotec_landing_about_card_admin_column_content( string $column, int $post_id ): void {
	if ( 'about_card_thumb' === $column ) {
		$image_id = (int) get_post_meta( $post_id, '_sercotec_about_card_image_id', true );

		if ( $image_id ) {
			echo wp_get_attachment_image( $image_id, array( 48, 48 ) );
		} else {
			echo '<span aria-hidden="true">—</span>';
		}
	}

	if ( 'about_card_layout' === $column ) {
		$layout = sercotec_landing_sanitize_card_layout( get_post_meta( $post_id, '_sercotec_about_card_layout', true ) );
		echo esc_html( strtoupper( $layout ) );
	}

	if ( 'about_card_color' === $column ) {
		$color = get_post_meta( $post_id, '_sercotec_about_card_color_hex', true ) ?: '#0052a3';
		echo '<span style="display:inline-block;width:18px;height:18px;border-radius:3px;background:' . esc_attr( $color ) . ';border:1px solid #ccc;" aria-hidden="true"></span>';
	}

	if ( 'about_card_order' === $column ) {
		echo esc_html( (string) get_post( $post_id )->menu_order );
	}
}
add_action( 'manage_sercotec_about_card_posts_custom_column', 'sercotec_landing_about_card_admin_column_content', 10, 2 );

function sercotec_landing_get_about_cards(): array {
	$query = new WP_Query(
		array(
			'post_type'      => 'sercotec_about_card',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);

	$cards = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id  = get_the_ID();
			$image_id = (int) get_post_meta( $post_id, '_sercotec_about_card_image_id', true );

			if ( ! $image_id ) {
				$image_id = (int) get_post_thumbnail_id( $post_id );
			}

			$color        = get_post_meta( $post_id, '_sercotec_about_card_color_hex', true ) ?: '#0052a3';
			$content_html = sercotec_landing_get_card_rich_content( $post_id );

			if ( '' === trim( wp_strip_all_tags( $content_html ) ) ) {
				$title_html   = sercotec_landing_get_card_stored_title_html( $post_id );
				$content_html = $title_html ?: '<p>' . esc_html( get_the_title( $post_id ) ) . '</p>';
			}

			$cards[] = array(
				'image'        => $image_id ? wp_get_attachment_image_url( $image_id, 'sercotec-about-card' ) : '',
				'title'        => get_the_title( $post_id ),
				'title_html'   => sercotec_landing_get_card_stored_title_html( $post_id ),
				'content_html' => $content_html,
				'color'        => sercotec_landing_sanitize_card_color_hex( $color ),
				'text_color'   => sercotec_landing_get_card_text_color( $post_id, 'about_card' ),
				'layout'       => sercotec_landing_sanitize_card_layout( get_post_meta( $post_id, '_sercotec_about_card_layout', true ) ),
			);
		}
		wp_reset_postdata();
	}

	return $cards;
}

function sercotec_landing_about_card_admin_assets( string $hook ): void {
	$screen = get_current_screen();

	if ( ! $screen || 'sercotec_about_card' !== $screen->post_type ) {
		return;
	}

	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	wp_enqueue_media();
	sercotec_landing_enqueue_theme_styles();
	sercotec_landing_enqueue_admin_main_js();
}
add_action( 'admin_enqueue_scripts', 'sercotec_landing_about_card_admin_assets' );
