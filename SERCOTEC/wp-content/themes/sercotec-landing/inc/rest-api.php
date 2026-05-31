<?php
/**
 * REST API — endpoints de la landing (sercotec/v1).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_get_contact_messages(): array {
	$query = new WP_Query(
		array(
			'post_type'      => 'sercotec_mensaje',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$messages = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			$messages[] = array(
				'id'          => $post_id,
				'name'        => get_the_title( $post_id ),
				'email'       => (string) get_post_meta( $post_id, '_sercotec_message_email', true ),
				'phone'       => (string) get_post_meta( $post_id, '_sercotec_message_phone', true ),
				'service'     => (string) get_post_meta( $post_id, '_sercotec_message_service', true ),
				'message'     => (string) get_post_meta( $post_id, '_sercotec_message_body', true ),
				'received_at' => get_the_date( 'c', $post_id ),
			);
		}
		wp_reset_postdata();
	}

	return $messages;
}

function sercotec_landing_rest_api_namespace(): string {
	return 'sercotec/v1';
}

function sercotec_landing_rest_can_read_comentarios(): bool {
	return current_user_can( 'edit_posts' );
}

function sercotec_landing_rest_resources(): array {
	return array(
		'servicios'   => array(
			'post_type'   => 'sercotec_servicio',
			'public_read' => true,
			'creatable'   => true,
			'list'        => 'sercotec_landing_rest_get_servicios',
			'format'      => 'sercotec_landing_rest_format_servicio',
			'create'      => 'sercotec_landing_rest_create_servicio',
			'update'      => 'sercotec_landing_rest_update_servicio',
		),
		'nosotros'    => array(
			'post_type'   => 'sercotec_about_card',
			'public_read' => true,
			'creatable'   => true,
			'list'        => 'sercotec_landing_rest_get_nosotros',
			'format'      => 'sercotec_landing_rest_format_nosotros_item',
			'create'      => 'sercotec_landing_rest_create_nosotros_item',
			'update'      => 'sercotec_landing_rest_update_nosotros_item',
		),
		'testimonios' => array(
			'post_type'   => 'sercotec_testimonio',
			'public_read' => true,
			'creatable'   => true,
			'list'        => 'sercotec_landing_rest_get_testimonios',
			'format'      => 'sercotec_landing_rest_format_testimonio',
			'create'      => 'sercotec_landing_rest_create_testimonio',
			'update'      => 'sercotec_landing_rest_update_testimonio',
		),
		'faq'         => array(
			'post_type'   => 'sercotec_faq',
			'public_read' => true,
			'creatable'   => true,
			'list'        => 'sercotec_landing_rest_get_faq',
			'format'      => 'sercotec_landing_rest_format_faq',
			'create'      => 'sercotec_landing_rest_create_faq',
			'update'      => 'sercotec_landing_rest_update_faq',
		),
		'comentarios' => array(
			'post_type'   => 'sercotec_mensaje',
			'public_read' => false,
			'list'        => 'sercotec_landing_rest_get_comentarios',
			'format'      => 'sercotec_landing_rest_format_comentario',
			'update'      => 'sercotec_landing_rest_update_comentario',
		),
	);
}

function sercotec_landing_rest_get_resource_slug( WP_REST_Request $request ): string {
	$route = (string) $request->get_route();
	$parts = array_values( array_filter( explode( '/', trim( $route, '/' ) ) ) );

	if ( count( $parts ) >= 2 && is_numeric( end( $parts ) ) ) {
		return sanitize_key( $parts[ count( $parts ) - 2 ] );
	}

	return sanitize_key( (string) end( $parts ) );
}

function sercotec_landing_rest_resolve_post( WP_REST_Request $request ) {
	$slug   = sercotec_landing_rest_get_resource_slug( $request );
	$config = sercotec_landing_rest_resources()[ $slug ] ?? null;

	if ( ! $config ) {
		return new WP_Error( 'rest_invalid_resource', __( 'Recurso no válido.', 'sercotec-landing' ), array( 'status' => 404 ) );
	}

	$post_id = absint( $request->get_param( 'id' ) );

	if ( $post_id < 1 ) {
		return new WP_Error( 'rest_invalid_id', __( 'ID no válido.', 'sercotec-landing' ), array( 'status' => 400 ) );
	}

	$post = get_post( $post_id );

	if ( ! $post || $config['post_type'] !== $post->post_type ) {
		return new WP_Error( 'rest_not_found', __( 'Elemento no encontrado.', 'sercotec-landing' ), array( 'status' => 404 ) );
	}

	return $post;
}

function sercotec_landing_rest_item_is_readable( WP_Post $post, array $config ): bool {
	if ( ! $config['public_read'] ) {
		return current_user_can( 'edit_posts' );
	}

	return 'publish' === $post->post_status;
}

function sercotec_landing_rest_can_read_item( WP_REST_Request $request ): bool {
	$config = sercotec_landing_rest_resources()[ sercotec_landing_rest_get_resource_slug( $request ) ] ?? null;

	if ( ! $config ) {
		return false;
	}

	if ( ! $config['public_read'] ) {
		return current_user_can( 'edit_posts' );
	}

	return true;
}

function sercotec_landing_rest_can_edit_item( WP_REST_Request $request ): bool {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	$post = sercotec_landing_rest_resolve_post( $request );

	if ( is_wp_error( $post ) ) {
		return false;
	}

	return current_user_can( 'edit_post', $post->ID );
}

function sercotec_landing_rest_can_create_item( WP_REST_Request $request ): bool {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	$config = sercotec_landing_rest_resources()[ sercotec_landing_rest_get_resource_slug( $request ) ] ?? null;

	return $config && ! empty( $config['creatable'] );
}

function sercotec_landing_rest_missing_field_error( string $field ): WP_Error {
	return new WP_Error(
		'rest_missing_field',
		/* translators: %s: field name */
		sprintf( __( 'El campo %s es obligatorio.', 'sercotec-landing' ), $field ),
		array( 'status' => 400 )
	);
}

function sercotec_landing_rest_insert_published_post( string $post_type, string $title, array $data ): int|WP_Error {
	$post_id = wp_insert_post(
		array(
			'post_type'    => $post_type,
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_content' => wp_kses_post( (string) ( $data['content'] ?? $data['content_html'] ?? $data['answer'] ?? '' ) ),
			'menu_order'   => (int) ( $data['order'] ?? $data['menu_order'] ?? 0 ),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	return (int) $post_id;
}

function sercotec_landing_rest_create_servicio( array $data ): array|WP_Error {
	$title = sanitize_text_field( (string) ( $data['title'] ?? '' ) );

	if ( '' === $title ) {
		return sercotec_landing_rest_missing_field_error( 'title' );
	}

	$post_id = sercotec_landing_rest_insert_published_post( 'sercotec_servicio', $title, $data );

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	$data = array_merge(
		array(
			'color_key' => 'blue',
			'layout'    => '2x1',
		),
		$data
	);

	return sercotec_landing_rest_update_servicio( $post_id, $data );
}

function sercotec_landing_rest_create_nosotros_item( array $data ): array|WP_Error {
	$title = sanitize_text_field( (string) ( $data['title'] ?? '' ) );

	if ( '' === $title ) {
		return sercotec_landing_rest_missing_field_error( 'title' );
	}

	$post_id = sercotec_landing_rest_insert_published_post( 'sercotec_about_card', $title, $data );

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	$data = array_merge(
		array(
			'color_key' => 'blue',
			'layout'    => '2x1',
		),
		$data
	);

	return sercotec_landing_rest_update_nosotros_item( $post_id, $data );
}

function sercotec_landing_rest_create_testimonio( array $data ): array|WP_Error {
	$title = sanitize_text_field( (string) ( $data['title'] ?? '' ) );

	if ( '' === $title ) {
		return sercotec_landing_rest_missing_field_error( 'title' );
	}

	$youtube_url = trim( (string) ( $data['youtube_url'] ?? '' ) );

	if ( '' === $youtube_url ) {
		return sercotec_landing_rest_missing_field_error( 'youtube_url' );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'sercotec_testimonio',
			'post_status' => 'publish',
			'post_title'  => $title,
			'menu_order'  => (int) ( $data['order'] ?? $data['menu_order'] ?? 0 ),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	return sercotec_landing_rest_update_testimonio( (int) $post_id, array_merge( $data, array( 'youtube_url' => $youtube_url ) ) );
}

function sercotec_landing_rest_create_faq( array $data ): array|WP_Error {
	$question = sanitize_text_field( (string) ( $data['question'] ?? $data['title'] ?? '' ) );

	if ( '' === $question ) {
		return sercotec_landing_rest_missing_field_error( 'question' );
	}

	$post_id = sercotec_landing_rest_insert_published_post(
		'sercotec_faq',
		$question,
		array_merge( $data, array( 'answer' => $data['answer'] ?? $data['content'] ?? '' ) )
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	return sercotec_landing_rest_update_faq( $post_id, $data );
}

function sercotec_landing_rest_get_card_image_id( int $post_id, string $meta_key ): int {
	$image_id = (int) get_post_meta( $post_id, $meta_key, true );

	if ( ! $image_id ) {
		$image_id = (int) get_post_thumbnail_id( $post_id );
	}

	return $image_id;
}

function sercotec_landing_rest_format_servicio( int $post_id ): array {
	$image_id = sercotec_landing_rest_get_card_image_id( $post_id, '_sercotec_service_image_id' );
	$color    = get_post_meta( $post_id, '_sercotec_service_color_hex', true ) ?: '#0052a3';

	return array(
		'id'             => $post_id,
		'title'          => get_the_title( $post_id ),
		'title_html'     => sercotec_landing_get_card_stored_title_html( $post_id ),
		'image'          => $image_id ? wp_get_attachment_image_url( $image_id, 'sercotec-about-card' ) : '',
		'image_id'       => $image_id,
		'content_html'   => sercotec_landing_get_card_rich_content( $post_id ),
		'color'          => sercotec_landing_sanitize_card_color_hex( $color ),
		'color_key'      => (string) get_post_meta( $post_id, '_sercotec_service_color_key', true ),
		'text_color'     => sercotec_landing_get_card_text_color( $post_id, 'service' ),
		'text_color_key' => (string) get_post_meta( $post_id, '_sercotec_service_text_color_key', true ),
		'layout'         => sercotec_landing_sanitize_card_layout( get_post_meta( $post_id, '_sercotec_service_layout', true ) ),
		'order'          => (int) get_post( $post_id )->menu_order,
		'quick_access'   => sercotec_landing_service_has_quick_access( $post_id ),
	);
}

function sercotec_landing_rest_format_nosotros_item( int $post_id ): array {
	$image_id     = sercotec_landing_rest_get_card_image_id( $post_id, '_sercotec_about_card_image_id' );
	$color        = get_post_meta( $post_id, '_sercotec_about_card_color_hex', true ) ?: '#0052a3';
	$content_html = sercotec_landing_get_card_rich_content( $post_id );

	if ( '' === trim( wp_strip_all_tags( $content_html ) ) ) {
		$title_html   = sercotec_landing_get_card_stored_title_html( $post_id );
		$content_html = $title_html ?: '<p>' . esc_html( get_the_title( $post_id ) ) . '</p>';
	}

	return array(
		'id'             => $post_id,
		'image'          => $image_id ? wp_get_attachment_image_url( $image_id, 'sercotec-about-card' ) : '',
		'image_id'       => $image_id,
		'title'          => get_the_title( $post_id ),
		'title_html'     => sercotec_landing_get_card_stored_title_html( $post_id ),
		'content_html'   => $content_html,
		'color'          => sercotec_landing_sanitize_card_color_hex( $color ),
		'color_key'      => (string) get_post_meta( $post_id, '_sercotec_about_card_color_key', true ),
		'text_color'     => sercotec_landing_get_card_text_color( $post_id, 'about_card' ),
		'text_color_key' => (string) get_post_meta( $post_id, '_sercotec_about_card_text_color_key', true ),
		'layout'         => sercotec_landing_sanitize_card_layout( get_post_meta( $post_id, '_sercotec_about_card_layout', true ) ),
		'order'          => (int) get_post( $post_id )->menu_order,
	);
}

function sercotec_landing_rest_format_testimonio( int $post_id ): array {
	$url      = (string) get_post_meta( $post_id, '_sercotec_testimonial_youtube_url', true );
	$video_id = sercotec_landing_parse_youtube_id( $url );

	return array(
		'id'          => $post_id,
		'title'       => get_the_title( $post_id ),
		'youtube_url' => $url,
		'video_id'    => $video_id ?: '',
		'embed_url'   => $video_id ? sercotec_landing_get_youtube_embed_url( $video_id ) : '',
		'order'       => (int) get_post( $post_id )->menu_order,
	);
}

function sercotec_landing_rest_format_faq( int $post_id ): array {
	return array(
		'id'       => $post_id,
		'question' => get_the_title( $post_id ),
		'answer'   => sercotec_landing_get_card_display_content( $post_id ),
		'order'    => (int) get_post( $post_id )->menu_order,
	);
}

function sercotec_landing_rest_format_comentario( int $post_id ): array {
	return array(
		'id'          => $post_id,
		'name'        => get_the_title( $post_id ),
		'email'       => (string) get_post_meta( $post_id, '_sercotec_message_email', true ),
		'phone'       => (string) get_post_meta( $post_id, '_sercotec_message_phone', true ),
		'service'     => (string) get_post_meta( $post_id, '_sercotec_message_service', true ),
		'message'     => (string) get_post_meta( $post_id, '_sercotec_message_body', true ),
		'received_at' => get_the_date( 'c', $post_id ),
	);
}

function sercotec_landing_rest_apply_card_color_meta( int $post_id, array $data, string $field_prefix ): void {
	if ( ! isset( $data['color_key'] ) && ! isset( $data['color'] ) ) {
		return;
	}

	$presets   = sercotec_landing_card_color_presets();
	$color_key = sanitize_key( (string) ( $data['color_key'] ?? get_post_meta( $post_id, '_' . $field_prefix . '_color_key', true ) ?: 'blue' ) );

	if ( ! isset( $presets[ $color_key ] ) ) {
		$color_key = 'blue';
	}

	update_post_meta( $post_id, '_' . $field_prefix . '_color_key', $color_key );

	$fallback  = $presets[ $color_key ]['hex'] ?: '#0052a3';
	$color_hex = sercotec_landing_sanitize_card_color_hex(
		(string) ( $data['color'] ?? get_post_meta( $post_id, '_' . $field_prefix . '_color_hex', true ) ),
		$fallback
	);

	if ( 'custom' !== $color_key && $presets[ $color_key ]['hex'] ) {
		$color_hex = $presets[ $color_key ]['hex'];
	}

	update_post_meta( $post_id, '_' . $field_prefix . '_color_hex', $color_hex );
}

function sercotec_landing_rest_apply_card_text_color_meta( int $post_id, array $data, string $field_prefix ): void {
	if ( ! isset( $data['text_color_key'] ) && ! isset( $data['text_color'] ) ) {
		return;
	}

	$presets   = sercotec_landing_card_text_color_presets();
	$color_key = sanitize_key( (string) ( $data['text_color_key'] ?? get_post_meta( $post_id, '_' . $field_prefix . '_text_color_key', true ) ?: 'white' ) );

	if ( ! isset( $presets[ $color_key ] ) ) {
		$color_key = 'white';
	}

	update_post_meta( $post_id, '_' . $field_prefix . '_text_color_key', $color_key );

	$fallback  = $presets[ $color_key ]['hex'] ?: '#ffffff';
	$color_hex = sercotec_landing_sanitize_card_color_hex(
		(string) ( $data['text_color'] ?? get_post_meta( $post_id, '_' . $field_prefix . '_text_color_hex', true ) ),
		$fallback
	);

	if ( 'custom' !== $color_key && $presets[ $color_key ]['hex'] ) {
		$color_hex = $presets[ $color_key ]['hex'];
	}

	update_post_meta( $post_id, '_' . $field_prefix . '_text_color_hex', $color_hex );
}

function sercotec_landing_rest_apply_card_image_meta( int $post_id, array $data, string $meta_key ): void {
	if ( ! array_key_exists( 'image_id', $data ) ) {
		return;
	}

	$image_id = absint( $data['image_id'] );

	if ( $image_id > 0 ) {
		update_post_meta( $post_id, $meta_key, $image_id );
		set_post_thumbnail( $post_id, $image_id );
	} else {
		delete_post_meta( $post_id, $meta_key );
		delete_post_thumbnail( $post_id );
	}
}

function sercotec_landing_rest_update_servicio( int $post_id, array $data ): array|WP_Error {
	$post_data = array( 'ID' => $post_id );

	if ( isset( $data['title'] ) ) {
		$post_data['post_title'] = sanitize_text_field( (string) $data['title'] );
	}

	if ( isset( $data['content'] ) || isset( $data['content_html'] ) ) {
		$post_data['post_content'] = wp_kses_post( (string) ( $data['content'] ?? $data['content_html'] ) );
	}

	if ( isset( $data['order'] ) || isset( $data['menu_order'] ) ) {
		$post_data['menu_order'] = (int) ( $data['order'] ?? $data['menu_order'] );
	}

	if ( count( $post_data ) > 1 ) {
		$result = wp_update_post( $post_data, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}

	if ( isset( $data['title_html'] ) ) {
		update_post_meta( $post_id, '_sercotec_card_title_html', wp_kses_post( (string) $data['title_html'] ) );
	}

	sercotec_landing_rest_apply_card_image_meta( $post_id, $data, '_sercotec_service_image_id' );
	sercotec_landing_rest_apply_card_color_meta( $post_id, $data, 'sercotec_service' );
	sercotec_landing_rest_apply_card_text_color_meta( $post_id, $data, 'sercotec_service' );

	if ( isset( $data['layout'] ) ) {
		update_post_meta(
			$post_id,
			'_sercotec_service_layout',
			sercotec_landing_sanitize_card_layout( (string) $data['layout'] )
		);
	}

	if ( array_key_exists( 'quick_access', $data ) ) {
		update_post_meta( $post_id, '_sercotec_service_quick_access', $data['quick_access'] ? '1' : '0' );
	}

	return sercotec_landing_rest_format_servicio( $post_id );
}

function sercotec_landing_rest_update_nosotros_item( int $post_id, array $data ): array|WP_Error {
	$post_data = array( 'ID' => $post_id );

	if ( isset( $data['title'] ) ) {
		$post_data['post_title'] = sanitize_text_field( (string) $data['title'] );
	}

	if ( isset( $data['content'] ) || isset( $data['content_html'] ) ) {
		$post_data['post_content'] = wp_kses_post( (string) ( $data['content'] ?? $data['content_html'] ) );
	}

	if ( isset( $data['order'] ) || isset( $data['menu_order'] ) ) {
		$post_data['menu_order'] = (int) ( $data['order'] ?? $data['menu_order'] );
	}

	if ( count( $post_data ) > 1 ) {
		$result = wp_update_post( $post_data, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}

	if ( isset( $data['title_html'] ) ) {
		update_post_meta( $post_id, '_sercotec_card_title_html', wp_kses_post( (string) $data['title_html'] ) );
	}

	sercotec_landing_rest_apply_card_image_meta( $post_id, $data, '_sercotec_about_card_image_id' );
	sercotec_landing_rest_apply_card_color_meta( $post_id, $data, 'sercotec_about_card' );
	sercotec_landing_rest_apply_card_text_color_meta( $post_id, $data, 'sercotec_about_card' );

	if ( isset( $data['layout'] ) ) {
		update_post_meta(
			$post_id,
			'_sercotec_about_card_layout',
			sercotec_landing_sanitize_card_layout( (string) $data['layout'] )
		);
	}

	return sercotec_landing_rest_format_nosotros_item( $post_id );
}

function sercotec_landing_rest_update_testimonio( int $post_id, array $data ): array|WP_Error {
	$post_data = array( 'ID' => $post_id );

	if ( isset( $data['title'] ) ) {
		$post_data['post_title'] = sanitize_text_field( (string) $data['title'] );
	}

	if ( isset( $data['order'] ) || isset( $data['menu_order'] ) ) {
		$post_data['menu_order'] = (int) ( $data['order'] ?? $data['menu_order'] );
	}

	if ( count( $post_data ) > 1 ) {
		$result = wp_update_post( $post_data, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}

	if ( isset( $data['youtube_url'] ) ) {
		update_post_meta( $post_id, '_sercotec_testimonial_youtube_url', esc_url_raw( trim( (string) $data['youtube_url'] ) ) );
	}

	return sercotec_landing_rest_format_testimonio( $post_id );
}

function sercotec_landing_rest_update_faq( int $post_id, array $data ): array|WP_Error {
	$post_data = array( 'ID' => $post_id );

	if ( isset( $data['question'] ) ) {
		$post_data['post_title'] = sanitize_text_field( (string) $data['question'] );
	}

	if ( isset( $data['answer'] ) || isset( $data['content'] ) ) {
		$post_data['post_content'] = wp_kses_post( (string) ( $data['answer'] ?? $data['content'] ) );
	}

	if ( isset( $data['order'] ) || isset( $data['menu_order'] ) ) {
		$post_data['menu_order'] = (int) ( $data['order'] ?? $data['menu_order'] );
	}

	if ( count( $post_data ) > 1 ) {
		$result = wp_update_post( $post_data, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}

	return sercotec_landing_rest_format_faq( $post_id );
}

function sercotec_landing_rest_update_comentario( int $post_id, array $data ): array|WP_Error {
	$post_data = array( 'ID' => $post_id );

	if ( isset( $data['name'] ) ) {
		$post_data['post_title'] = sanitize_text_field( (string) $data['name'] );
	}

	if ( count( $post_data ) > 1 ) {
		$result = wp_update_post( $post_data, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}

	if ( isset( $data['email'] ) ) {
		update_post_meta( $post_id, '_sercotec_message_email', sanitize_email( (string) $data['email'] ) );
	}

	if ( isset( $data['phone'] ) ) {
		update_post_meta( $post_id, '_sercotec_message_phone', sanitize_text_field( (string) $data['phone'] ) );
	}

	if ( isset( $data['service'] ) ) {
		update_post_meta( $post_id, '_sercotec_message_service', sanitize_text_field( (string) $data['service'] ) );
	}

	if ( isset( $data['message'] ) ) {
		update_post_meta( $post_id, '_sercotec_message_body', sanitize_textarea_field( (string) $data['message'] ) );
	}

	return sercotec_landing_rest_format_comentario( $post_id );
}

function sercotec_landing_rest_get_servicios(): WP_REST_Response {
	return rest_ensure_response(
		array(
			'section' => array(
				'label'           => sercotec_landing_get_services_label(),
				'title'           => sercotec_landing_get_services_title(),
				'subtitle'        => sercotec_landing_get_services_subtitle(),
				'mid_paragraph'   => sercotec_landing_get_services_mid_paragraph_html(),
				'mid_after_order' => sercotec_landing_get_services_mid_paragraph_after_order(),
			),
			'items'   => sercotec_landing_get_services(),
		)
	);
}

function sercotec_landing_rest_get_nosotros(): WP_REST_Response {
	return rest_ensure_response(
		array(
			'section' => array(
				'label'   => sercotec_landing_get_about_label(),
				'content' => sercotec_landing_get_about_content(),
			),
			'items'   => sercotec_landing_get_about_cards(),
		)
	);
}

function sercotec_landing_rest_get_testimonios(): WP_REST_Response {
	return rest_ensure_response(
		array(
			'section' => array(
				'label' => sercotec_landing_get_testimonials_label(),
				'title' => sercotec_landing_get_testimonials_title(),
			),
			'items'   => sercotec_landing_get_testimonials(),
		)
	);
}

function sercotec_landing_rest_get_faq(): WP_REST_Response {
	return rest_ensure_response(
		array(
			'section' => array(
				'label'    => sercotec_landing_get_faq_label(),
				'title'    => sercotec_landing_get_faq_title(),
				'subtitle' => sercotec_landing_get_faq_subtitle(),
			),
			'items'   => sercotec_landing_get_faqs(),
		)
	);
}

function sercotec_landing_rest_get_comentarios(): WP_REST_Response {
	$messages = sercotec_landing_get_contact_messages();

	return rest_ensure_response(
		array(
			'items' => $messages,
			'total' => count( $messages ),
		)
	);
}

function sercotec_landing_rest_create_item( WP_REST_Request $request ) {
	$slug   = sercotec_landing_rest_get_resource_slug( $request );
	$config = sercotec_landing_rest_resources()[ $slug ] ?? null;

	if ( ! $config || empty( $config['creatable'] ) || empty( $config['create'] ) ) {
		return new WP_Error( 'rest_invalid_resource', __( 'Recurso no válido.', 'sercotec-landing' ), array( 'status' => 404 ) );
	}

	$data   = $request->get_json_params();
	$create = $config['create'];
	$result = $create( is_array( $data ) ? $data : array() );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return new WP_REST_Response(
		array(
			'item' => $result,
		),
		201
	);
}

function sercotec_landing_rest_get_item( WP_REST_Request $request ) {
	$slug   = sercotec_landing_rest_get_resource_slug( $request );
	$config = sercotec_landing_rest_resources()[ $slug ] ?? null;

	if ( ! $config ) {
		return new WP_Error( 'rest_invalid_resource', __( 'Recurso no válido.', 'sercotec-landing' ), array( 'status' => 404 ) );
	}

	$post = sercotec_landing_rest_resolve_post( $request );

	if ( is_wp_error( $post ) ) {
		return $post;
	}

	if ( ! sercotec_landing_rest_item_is_readable( $post, $config ) ) {
		return new WP_Error( 'rest_forbidden', __( 'No tienes permiso para ver este elemento.', 'sercotec-landing' ), array( 'status' => 403 ) );
	}

	$formatter = $config['format'];

	return rest_ensure_response(
		array(
			'item' => $formatter( $post->ID ),
		)
	);
}

function sercotec_landing_rest_update_item( WP_REST_Request $request ) {
	$slug   = sercotec_landing_rest_get_resource_slug( $request );
	$config = sercotec_landing_rest_resources()[ $slug ] ?? null;

	if ( ! $config ) {
		return new WP_Error( 'rest_invalid_resource', __( 'Recurso no válido.', 'sercotec-landing' ), array( 'status' => 404 ) );
	}

	$post = sercotec_landing_rest_resolve_post( $request );

	if ( is_wp_error( $post ) ) {
		return $post;
	}

	$data    = $request->get_json_params();
	$updater = $config['update'];
	$result  = $updater( $post->ID, is_array( $data ) ? $data : array() );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return rest_ensure_response(
		array(
			'item' => $result,
		)
	);
}

function sercotec_landing_rest_delete_item( WP_REST_Request $request ) {
	$post = sercotec_landing_rest_resolve_post( $request );

	if ( is_wp_error( $post ) ) {
		return $post;
	}

	$post_id = $post->ID;
	$force   = rest_sanitize_boolean( $request->get_param( 'force' ) );

	if ( $force ) {
		$deleted = wp_delete_post( $post_id, true );
	} else {
		$deleted = wp_trash_post( $post_id );
	}

	if ( ! $deleted ) {
		return new WP_Error( 'rest_cannot_delete', __( 'No se pudo eliminar el elemento.', 'sercotec-landing' ), array( 'status' => 500 ) );
	}

	return rest_ensure_response(
		array(
			'id'      => $post_id,
			'deleted' => true,
			'force'   => $force,
		)
	);
}

function sercotec_landing_register_rest_routes(): void {
	$namespace = sercotec_landing_rest_api_namespace();
	$id_args   = array(
		'id' => array(
			'required'          => true,
			'validate_callback' => static function ( $value ) {
				return is_numeric( $value ) && (int) $value > 0;
			},
		),
	);

	foreach ( sercotec_landing_rest_resources() as $slug => $config ) {
		$list_routes = array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => $config['list'],
				'permission_callback' => $config['public_read'] ? '__return_true' : 'sercotec_landing_rest_can_read_comentarios',
			),
		);

		if ( ! empty( $config['creatable'] ) ) {
			$list_routes[] = array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'sercotec_landing_rest_create_item',
				'permission_callback' => 'sercotec_landing_rest_can_create_item',
			);
		}

		register_rest_route( $namespace, '/' . $slug, $list_routes );

		register_rest_route(
			$namespace,
			'/' . $slug . '/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => 'sercotec_landing_rest_get_item',
					'permission_callback' => 'sercotec_landing_rest_can_read_item',
					'args'                => $id_args,
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => 'sercotec_landing_rest_update_item',
					'permission_callback' => 'sercotec_landing_rest_can_edit_item',
					'args'                => $id_args,
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => 'sercotec_landing_rest_delete_item',
					'permission_callback' => 'sercotec_landing_rest_can_edit_item',
					'args'                => array_merge(
						$id_args,
						array(
							'force' => array(
								'default'           => false,
								'sanitize_callback' => 'rest_sanitize_boolean',
							),
						)
					),
				),
			)
		);
	}
}
add_action( 'rest_api_init', 'sercotec_landing_register_rest_routes' );
