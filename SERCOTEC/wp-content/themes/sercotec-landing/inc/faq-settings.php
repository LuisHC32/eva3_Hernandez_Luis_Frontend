<?php
/**
 * Configuración editable de la sección FAQ.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_faq_settings_menu(): void {
	add_submenu_page(
		'edit.php?post_type=sercotec_faq',
		__( 'Configuración de sección', 'sercotec-landing' ),
		__( 'Configuración de sección', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-faq',
		'sercotec_landing_faq_settings_page'
	);
}
add_action( 'admin_menu', 'sercotec_landing_faq_settings_menu' );

function sercotec_landing_register_faq_settings(): void {
	register_setting(
		'sercotec_faq_settings',
		'sercotec_faq_label',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => sercotec_landing_default_faq_label(),
		)
	);

	register_setting(
		'sercotec_faq_settings',
		'sercotec_faq_title',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => sercotec_landing_default_faq_title(),
		)
	);

	register_setting(
		'sercotec_faq_settings',
		'sercotec_faq_subtitle',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
			'default'           => sercotec_landing_default_faq_subtitle(),
		)
	);
}
add_action( 'admin_init', 'sercotec_landing_register_faq_settings' );

function sercotec_landing_default_faq_label(): string {
	return __( 'Preguntas frecuentes', 'sercotec-landing' );
}

function sercotec_landing_default_faq_title(): string {
	return __( 'Resolvemos tus dudas antes de postular', 'sercotec-landing' );
}

function sercotec_landing_default_faq_subtitle(): string {
	return __( 'Si no encuentras tu respuesta, escríbenos y un ejecutivo te orientará sin costo.', 'sercotec-landing' );
}

function sercotec_landing_get_faq_label(): string {
	$label = get_option( 'sercotec_faq_label', sercotec_landing_default_faq_label() );

	return $label ?: sercotec_landing_default_faq_label();
}

function sercotec_landing_get_faq_title(): string {
	$title = get_option( 'sercotec_faq_title', sercotec_landing_default_faq_title() );

	return $title ?: sercotec_landing_default_faq_title();
}

function sercotec_landing_get_faq_subtitle(): string {
	$subtitle = get_option( 'sercotec_faq_subtitle', sercotec_landing_default_faq_subtitle() );

	return $subtitle ?: sercotec_landing_default_faq_subtitle();
}

function sercotec_landing_faq_settings_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$label    = sercotec_landing_get_faq_label();
	$title    = sercotec_landing_get_faq_title();
	$subtitle = sercotec_landing_get_faq_subtitle();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Configuración de la sección FAQ', 'sercotec-landing' ); ?></h1>
		<p><?php esc_html_e( 'Personaliza los textos del encabezado que se muestran en la landing.', 'sercotec-landing' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'sercotec_faq_settings' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_faq_label"><?php esc_html_e( 'Etiqueta', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="sercotec_faq_label"
							name="sercotec_faq_label"
							value="<?php echo esc_attr( $label ); ?>"
							class="regular-text"
						>
						<p class="description"><?php esc_html_e( 'Texto pequeño superior (ej: Preguntas frecuentes).', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_faq_title"><?php esc_html_e( 'Título', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="sercotec_faq_title"
							name="sercotec_faq_title"
							value="<?php echo esc_attr( $title ); ?>"
							class="large-text"
						>
						<p class="description"><?php esc_html_e( 'Encabezado principal de la sección.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_faq_subtitle"><?php esc_html_e( 'Subtítulo', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<textarea
							id="sercotec_faq_subtitle"
							name="sercotec_faq_subtitle"
							rows="3"
							class="large-text"
						><?php echo esc_textarea( $subtitle ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Texto descriptivo debajo del título.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Guardar cambios', 'sercotec-landing' ) ); ?>
		</form>
	</div>
	<?php
}
