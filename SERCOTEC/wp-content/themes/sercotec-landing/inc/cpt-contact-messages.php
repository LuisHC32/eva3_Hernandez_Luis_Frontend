<?php
/**
 * Mensajes del formulario de contacto.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_register_contact_message_cpt() {
	$labels = array(
		'name'               => __( 'Mensajes de contacto', 'sercotec-landing' ),
		'singular_name'      => __( 'Mensaje de contacto', 'sercotec-landing' ),
		'menu_name'          => __( 'Mensajes recibidos', 'sercotec-landing' ),
		'all_items'          => __( 'Todos los mensajes', 'sercotec-landing' ),
		'view_item'          => __( 'Ver mensaje', 'sercotec-landing' ),
		'search_items'       => __( 'Buscar mensajes', 'sercotec-landing' ),
		'not_found'          => __( 'No hay mensajes recibidos.', 'sercotec-landing' ),
		'not_found_in_trash' => __( 'No hay mensajes en la papelera.', 'sercotec-landing' ),
	);

	register_post_type(
		'sercotec_mensaje',
		array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			),
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'show_in_rest'        => false,
		)
	);
}
add_action( 'init', 'sercotec_landing_register_contact_message_cpt' );

function sercotec_landing_contact_messages_menu(): void {
	add_submenu_page(
		'sercotec-contact',
		__( 'Mensajes recibidos', 'sercotec-landing' ),
		__( 'Mensajes recibidos', 'sercotec-landing' ),
		'edit_posts',
		'edit.php?post_type=sercotec_mensaje'
	);
}
add_action( 'admin_menu', 'sercotec_landing_contact_messages_menu' );

function sercotec_landing_contact_message_meta_boxes() {
	add_meta_box(
		'sercotec_contact_message_details',
		__( 'Detalle del mensaje', 'sercotec-landing' ),
		'sercotec_landing_contact_message_meta_box_render',
		'sercotec_mensaje',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'sercotec_landing_contact_message_meta_boxes' );

function sercotec_landing_contact_message_meta_box_render( WP_Post $post ) {
	$email   = get_post_meta( $post->ID, '_sercotec_message_email', true );
	$phone   = get_post_meta( $post->ID, '_sercotec_message_phone', true );
	$service = get_post_meta( $post->ID, '_sercotec_message_service', true );
	$message = get_post_meta( $post->ID, '_sercotec_message_body', true );
	?>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'Nombre', 'sercotec-landing' ); ?></th>
			<td><?php echo esc_html( get_the_title( $post ) ); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Email', 'sercotec-landing' ); ?></th>
			<td>
				<?php if ( $email ) : ?>
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				<?php else : ?>
					<span aria-hidden="true">—</span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Teléfono', 'sercotec-landing' ); ?></th>
			<td><?php echo $phone ? esc_html( $phone ) : '<span aria-hidden="true">—</span>'; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Servicio', 'sercotec-landing' ); ?></th>
			<td><?php echo $service ? esc_html( $service ) : '<span aria-hidden="true">—</span>'; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Mensaje', 'sercotec-landing' ); ?></th>
			<td><p style="margin:0;white-space:pre-wrap;"><?php echo esc_html( $message ); ?></p></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Recibido', 'sercotec-landing' ); ?></th>
			<td><?php echo esc_html( get_the_date( 'd/m/Y H:i', $post ) ); ?></td>
		</tr>
	</table>
	<?php
}

function sercotec_landing_contact_message_hide_editor() {
	$screen = get_current_screen();

	if ( $screen && 'sercotec_mensaje' === $screen->post_type ) {
		remove_post_type_support( 'sercotec_mensaje', 'title' );
		echo '<style>#titlediv,.submitbox .edit-post-status,#delete-action{display:none;}</style>';
	}
}
add_action( 'admin_head', 'sercotec_landing_contact_message_hide_editor' );

function sercotec_landing_contact_message_admin_columns( array $columns ): array {
	$new = array();

	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['title']             = __( 'Nombre', 'sercotec-landing' );
			$new['message_email']     = __( 'Email', 'sercotec-landing' );
			$new['message_phone']     = __( 'Teléfono', 'sercotec-landing' );
			$new['message_excerpt']   = __( 'Mensaje', 'sercotec-landing' );
			continue;
		}

		if ( 'date' === $key ) {
			$new['date'] = __( 'Recibido', 'sercotec-landing' );
			continue;
		}

		$new[ $key ] = $label;
	}

	return $new;
}
add_filter( 'manage_sercotec_mensaje_posts_columns', 'sercotec_landing_contact_message_admin_columns' );

function sercotec_landing_contact_message_admin_column_content( string $column, int $post_id ): void {
	if ( 'message_email' === $column ) {
		$email = get_post_meta( $post_id, '_sercotec_message_email', true );
		echo $email ? esc_html( $email ) : '<span aria-hidden="true">—</span>';
	}

	if ( 'message_phone' === $column ) {
		$phone = get_post_meta( $post_id, '_sercotec_message_phone', true );
		echo $phone ? esc_html( $phone ) : '<span aria-hidden="true">—</span>';
	}

	if ( 'message_excerpt' === $column ) {
		$message = get_post_meta( $post_id, '_sercotec_message_body', true );
		echo $message ? esc_html( wp_trim_words( $message, 12, '…' ) ) : '<span aria-hidden="true">—</span>';
	}
}
add_action( 'manage_sercotec_mensaje_posts_custom_column', 'sercotec_landing_contact_message_admin_column_content', 10, 2 );

function sercotec_landing_save_contact_message( string $name, string $email, string $phone, string $message, string $service = '' ) {
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'sercotec_mensaje',
			'post_title'  => $name,
			'post_status' => 'publish',
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	update_post_meta( $post_id, '_sercotec_message_email', $email );
	update_post_meta( $post_id, '_sercotec_message_phone', $phone );
	update_post_meta( $post_id, '_sercotec_message_service', $service );
	update_post_meta( $post_id, '_sercotec_message_body', $message );

	return (int) $post_id;
}

function sercotec_landing_sanitize_contact_service( string $service ): string {
	$service = sanitize_text_field( $service );

	if ( '' === $service ) {
		return '';
	}

	$allowed = sercotec_landing_get_contact_form_services();

	return in_array( $service, $allowed, true ) ? $service : '';
}

function sercotec_landing_handle_contact_form() {
	check_ajax_referer( 'sercotec_contact_form', 'nonce' );

	if ( sercotec_landing_contact_is_rate_limited() ) {
		wp_send_json_error(
			array(
				'message' => sprintf(
					/* translators: %d: minutes */
					__( 'Demasiados intentos. Espera %d minutos antes de enviar otro mensaje.', 'sercotec-landing' ),
					(int) get_option( 'sercotec_contact_rate_limit_window', 15 )
				),
			),
			429
		);
	}

	sercotec_landing_contact_record_rate_limit_hit();

	$turnstile_token = sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ?? '' ) );
	$turnstile_check = sercotec_landing_verify_turnstile( $turnstile_token );

	if ( is_wp_error( $turnstile_check ) ) {
		wp_send_json_error(
			array( 'message' => $turnstile_check->get_error_message() ),
			400
		);
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$service = sercotec_landing_sanitize_contact_service( (string) wp_unslash( $_POST['service'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

	if ( empty( $name ) || empty( $email ) || empty( $message ) || ! is_email( $email ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Por favor completa todos los campos obligatorios correctamente.', 'sercotec-landing' ) ),
			400
		);
	}

	if ( strlen( $name ) > 120 || strlen( $message ) < 10 || strlen( $message ) > 2000 ) {
		wp_send_json_error(
			array( 'message' => __( 'Por favor completa todos los campos obligatorios correctamente.', 'sercotec-landing' ) ),
			400
		);
	}

	$message_id = sercotec_landing_save_contact_message( $name, $email, $phone, $message, $service );

	if ( ! $message_id ) {
		wp_send_json_error(
			array( 'message' => __( 'No pudimos guardar tu mensaje. Intenta nuevamente.', 'sercotec-landing' ) ),
			500
		);
	}

	$admin_email = sercotec_landing_get_contact_email();
	if ( ! is_email( $admin_email ) ) {
		$admin_email = get_option( 'admin_email' );
	}

	$subject = sprintf( '[SERCOTEC] Nuevo contacto de %s', $name );
	$body    = sprintf(
		"Nombre: %s\nEmail: %s\nTeléfono: %s\nServicio: %s\n\nMensaje:\n%s",
		$name,
		$email,
		$phone,
		$service ?: '—',
		$message
	);

	wp_mail( $admin_email, $subject, $body, array( 'Reply-To: ' . $email ) );

	wp_send_json_success(
		array( 'message' => __( '¡Gracias! Tu mensaje fue enviado correctamente.', 'sercotec-landing' ) )
	);
}
add_action( 'wp_ajax_sercotec_contact', 'sercotec_landing_handle_contact_form' );
add_action( 'wp_ajax_nopriv_sercotec_contact', 'sercotec_landing_handle_contact_form' );

function sercotec_landing_contact_messages_admin_title( string $admin_title ): string {
	$screen = get_current_screen();

	if ( $screen && 'edit-sercotec_mensaje' === $screen->id ) {
		return __( 'Mensajes recibidos', 'sercotec-landing' ) . ' ‹ ' . get_bloginfo( 'name' );
	}

	return $admin_title;
}
add_filter( 'admin_title', 'sercotec_landing_contact_messages_admin_title' );
