<?php
/**
 * Configuración editable de la sección Testimonios.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_testimonials_settings_menu(): void {
	add_submenu_page(
		'edit.php?post_type=sercotec_testimonio',
		__( 'Configuración de sección', 'sercotec-landing' ),
		__( 'Configuración de sección', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-testimonials',
		'sercotec_landing_testimonials_settings_page'
	);
}
add_action( 'admin_menu', 'sercotec_landing_testimonials_settings_menu' );

function sercotec_landing_register_testimonials_settings(): void {
	register_setting(
		'sercotec_testimonials_settings',
		'sercotec_testimonials_label',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => sercotec_landing_default_testimonials_label(),
		)
	);

	register_setting(
		'sercotec_testimonials_settings',
		'sercotec_testimonials_title',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => sercotec_landing_default_testimonials_title(),
		)
	);
}
add_action( 'admin_init', 'sercotec_landing_register_testimonials_settings' );

function sercotec_landing_default_testimonials_label(): string {
	return __( 'Testimonios', 'sercotec-landing' );
}

function sercotec_landing_default_testimonials_title(): string {
	return __( 'Historias reales de emprendedores que crecieron con nosotros', 'sercotec-landing' );
}

function sercotec_landing_get_testimonials_label(): string {
	$label = get_option( 'sercotec_testimonials_label', sercotec_landing_default_testimonials_label() );

	return $label ?: sercotec_landing_default_testimonials_label();
}

function sercotec_landing_get_testimonials_title(): string {
	$title = get_option( 'sercotec_testimonials_title', sercotec_landing_default_testimonials_title() );

	return $title ?: sercotec_landing_default_testimonials_title();
}

function sercotec_landing_testimonials_settings_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$label = sercotec_landing_get_testimonials_label();
	$title = sercotec_landing_get_testimonials_title();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Configuración de la sección Testimonios', 'sercotec-landing' ); ?></h1>
		<p><?php esc_html_e( 'Personaliza los textos del encabezado que se muestran en la landing.', 'sercotec-landing' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'sercotec_testimonials_settings' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_testimonials_label"><?php esc_html_e( 'Etiqueta', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="sercotec_testimonials_label"
							name="sercotec_testimonials_label"
							value="<?php echo esc_attr( $label ); ?>"
							class="regular-text"
						>
						<p class="description"><?php esc_html_e( 'Texto pequeño superior (ej: Testimonios).', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_testimonials_title"><?php esc_html_e( 'Título', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="sercotec_testimonials_title"
							name="sercotec_testimonials_title"
							value="<?php echo esc_attr( $title ); ?>"
							class="large-text"
						>
						<p class="description"><?php esc_html_e( 'Encabezado principal de la sección.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Guardar cambios', 'sercotec-landing' ) ); ?>
		</form>
	</div>
	<?php
}
