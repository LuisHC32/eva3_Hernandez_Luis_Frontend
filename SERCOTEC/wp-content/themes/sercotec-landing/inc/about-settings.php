<?php
/**
 * Configuración editable de la sección Nosotros.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_about_settings_menu(): void {
	add_submenu_page(
		'edit.php?post_type=sercotec_about_card',
		__( 'Configuración de sección', 'sercotec-landing' ),
		__( 'Configuración de sección', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-about',
		'sercotec_landing_about_settings_page'
	);
}
add_action( 'admin_menu', 'sercotec_landing_about_settings_menu' );

function sercotec_landing_register_about_settings(): void {
	register_setting(
		'sercotec_about_settings',
		'sercotec_about_label',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => sercotec_landing_default_about_label(),
		)
	);

	register_setting(
		'sercotec_about_settings',
		'sercotec_about_content',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
			'default'           => sercotec_landing_default_about_content(),
		)
	);

	register_setting(
		'sercotec_about_settings',
		'sercotec_about_label_size',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_about_label_size',
			'default'           => sercotec_landing_default_about_label_size(),
		)
	);

	register_setting(
		'sercotec_about_settings',
		'sercotec_about_label_align',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sercotec_landing_sanitize_about_label_align',
			'default'           => sercotec_landing_default_about_label_align(),
		)
	);

	register_setting(
		'sercotec_about_settings',
		'sercotec_about_content_size',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_about_content_size',
			'default'           => sercotec_landing_default_about_content_size(),
		)
	);

	register_setting(
		'sercotec_about_settings',
		'sercotec_about_content_align',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sercotec_landing_sanitize_about_content_align',
			'default'           => sercotec_landing_default_about_content_align(),
		)
	);
}
add_action( 'admin_init', 'sercotec_landing_register_about_settings' );

function sercotec_landing_sanitize_about_label_size( $value ): int {
	return sercotec_landing_sanitize_font_size( $value, sercotec_landing_default_about_label_size() );
}

function sercotec_landing_sanitize_about_content_size( $value ): int {
	return sercotec_landing_sanitize_font_size( $value, sercotec_landing_default_about_content_size() );
}

function sercotec_landing_sanitize_about_label_align( $value ): string {
	return sercotec_landing_sanitize_text_align( $value, sercotec_landing_default_about_label_align() );
}

function sercotec_landing_sanitize_about_content_align( $value ): string {
	return sercotec_landing_sanitize_text_align( $value, sercotec_landing_default_about_content_align() );
}

function sercotec_landing_default_about_label(): string {
	return __( 'Nosotros', 'sercotec-landing' );
}

function sercotec_landing_default_about_content(): string {
	return __( 'SERCOTEC es el servicio técnico del Ministerio de Economía que entrega apoyo integral a emprendedores y pequeñas empresas, con foco en innovación, formalización y acceso a mercados.', 'sercotec-landing' );
}

function sercotec_landing_default_about_label_size(): int {
	return 13;
}

function sercotec_landing_default_about_content_size(): int {
	return 17;
}

function sercotec_landing_default_about_label_align(): string {
	return 'left';
}

function sercotec_landing_default_about_content_align(): string {
	return 'justify';
}

function sercotec_landing_get_about_label(): string {
	$label = get_option( 'sercotec_about_label', sercotec_landing_default_about_label() );

	return $label ?: sercotec_landing_default_about_label();
}

function sercotec_landing_get_about_content(): string {
	$content = get_option( 'sercotec_about_content', sercotec_landing_default_about_content() );

	return $content ?: sercotec_landing_default_about_content();
}

function sercotec_landing_get_about_label_size(): int {
	return (int) get_option( 'sercotec_about_label_size', sercotec_landing_default_about_label_size() );
}

function sercotec_landing_get_about_content_size(): int {
	return (int) get_option( 'sercotec_about_content_size', sercotec_landing_default_about_content_size() );
}

function sercotec_landing_get_about_label_align(): string {
	return sercotec_landing_sanitize_text_align(
		get_option( 'sercotec_about_label_align', sercotec_landing_default_about_label_align() ),
		sercotec_landing_default_about_label_align()
	);
}

function sercotec_landing_get_about_content_align(): string {
	return sercotec_landing_sanitize_text_align(
		get_option( 'sercotec_about_content_align', sercotec_landing_default_about_content_align() ),
		sercotec_landing_default_about_content_align()
	);
}

function sercotec_landing_get_about_section_style(): string {
	$styles = array(
		'--about-label-size'   => sercotec_landing_get_about_label_size() . 'px',
		'--about-label-align'  => sercotec_landing_get_about_label_align(),
		'--about-content-size' => sercotec_landing_get_about_content_size() . 'px',
		'--about-content-align' => sercotec_landing_get_about_content_align(),
	);

	$parts = array();

	foreach ( $styles as $property => $value ) {
		$parts[] = $property . ':' . $value;
	}

	return implode( ';', $parts );
}

function sercotec_landing_about_settings_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$label         = sercotec_landing_get_about_label();
	$content       = sercotec_landing_get_about_content();
	$label_size    = sercotec_landing_get_about_label_size();
	$content_size  = sercotec_landing_get_about_content_size();
	$label_align   = sercotec_landing_get_about_label_align();
	$content_align = sercotec_landing_get_about_content_align();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Configuración de la sección Nosotros', 'sercotec-landing' ); ?></h1>
		<p><?php esc_html_e( 'Personaliza los textos, tamaños de letra y alineación que se muestran en la landing.', 'sercotec-landing' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'sercotec_about_settings' ); ?>

			<h2><?php esc_html_e( 'Textos', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_about_label"><?php esc_html_e( 'Etiqueta', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="sercotec_about_label"
							name="sercotec_about_label"
							value="<?php echo esc_attr( $label ); ?>"
							class="regular-text"
						>
						<p class="description"><?php esc_html_e( 'Texto superior (ej: Nosotros).', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_about_content"><?php esc_html_e( 'Párrafo', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<textarea
							id="sercotec_about_content"
							name="sercotec_about_content"
							rows="6"
							class="large-text"
						><?php echo esc_textarea( $content ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Texto descriptivo de la sección.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Tipografía', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_about_label_size"><?php esc_html_e( 'Etiqueta', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<?php sercotec_landing_services_typography_field( 'sercotec_about_label_size', $label_size, 'sercotec_about_label_align', $label_align ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_about_content_size"><?php esc_html_e( 'Párrafo', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<?php sercotec_landing_services_typography_field( 'sercotec_about_content_size', $content_size, 'sercotec_about_content_align', $content_align ); ?>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Guardar cambios', 'sercotec-landing' ) ); ?>
		</form>
	</div>
	<?php
}
