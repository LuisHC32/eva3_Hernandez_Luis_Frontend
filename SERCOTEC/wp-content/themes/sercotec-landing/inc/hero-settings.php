<?php
/**
 * Configuración editable de la sección Hero (Inicio).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_hero_settings_menu(): void {
	add_menu_page(
		__( 'Inicio', 'sercotec-landing' ),
		__( 'Inicio', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-hero',
		'sercotec_landing_hero_settings_page',
		'dashicons-admin-home',
		21
	);

	add_submenu_page(
		'sercotec-hero',
		__( 'Configuración de sección', 'sercotec-landing' ),
		__( 'Configuración de sección', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-hero',
		'sercotec_landing_hero_settings_page'
	);
}
add_action( 'admin_menu', 'sercotec_landing_hero_settings_menu', 9 );

function sercotec_landing_sanitize_hero_link( $value ): string {
	$value = trim( (string) wp_unslash( $value ) );

	if ( '' === $value ) {
		return '';
	}

	if ( str_starts_with( $value, '#' ) ) {
		return sanitize_text_field( $value );
	}

	return esc_url_raw( $value );
}

function sercotec_landing_register_hero_settings(): void {
	$text_fields = array(
		'sercotec_hero_badge'                => 'sercotec_landing_default_hero_badge',
		'sercotec_hero_title'                => 'sercotec_landing_default_hero_title',
		'sercotec_hero_primary_btn_text'     => 'sercotec_landing_default_hero_primary_btn_text',
		'sercotec_hero_secondary_btn_text'   => 'sercotec_landing_default_hero_secondary_btn_text',
		'sercotec_hero_stat_1_value'         => 'sercotec_landing_default_hero_stat_1_value',
		'sercotec_hero_stat_1_label'         => 'sercotec_landing_default_hero_stat_1_label',
		'sercotec_hero_stat_2_value'         => 'sercotec_landing_default_hero_stat_2_value',
		'sercotec_hero_stat_2_label'         => 'sercotec_landing_default_hero_stat_2_label',
		'sercotec_hero_stat_3_value'         => 'sercotec_landing_default_hero_stat_3_value',
		'sercotec_hero_stat_3_label'         => 'sercotec_landing_default_hero_stat_3_label',
		'sercotec_hero_card_main_title'      => 'sercotec_landing_default_hero_card_main_title',
		'sercotec_hero_card_secondary_title' => 'sercotec_landing_default_hero_card_secondary_title',
	);

	$textarea_fields = array(
		'sercotec_hero_description'          => 'sercotec_landing_default_hero_description',
		'sercotec_hero_card_main_text'       => 'sercotec_landing_default_hero_card_main_text',
		'sercotec_hero_card_secondary_text'  => 'sercotec_landing_default_hero_card_secondary_text',
	);

	$link_fields = array(
		'sercotec_hero_primary_btn_url'   => 'sercotec_landing_default_hero_primary_btn_url',
		'sercotec_hero_secondary_btn_url' => 'sercotec_landing_default_hero_secondary_btn_url',
	);

	foreach ( $text_fields as $option => $default_fn ) {
		register_setting(
			'sercotec_hero_settings',
			$option,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => call_user_func( $default_fn ),
			)
		);
	}

	foreach ( $textarea_fields as $option => $default_fn ) {
		register_setting(
			'sercotec_hero_settings',
			$option,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
				'default'           => call_user_func( $default_fn ),
			)
		);
	}

	foreach ( $link_fields as $option => $default_fn ) {
		register_setting(
			'sercotec_hero_settings',
			$option,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sercotec_landing_sanitize_hero_link',
				'default'           => call_user_func( $default_fn ),
			)
		);
	}
}
add_action( 'admin_init', 'sercotec_landing_register_hero_settings' );

function sercotec_landing_default_hero_badge(): string {
	return __( 'Impulsa tu emprendimiento', 'sercotec-landing' );
}

function sercotec_landing_default_hero_title(): string {
	return __( 'Financiamiento y apoyo técnico para hacer crecer tu negocio', 'sercotec-landing' );
}

function sercotec_landing_default_hero_description(): string {
	return __( 'Accede a capital semilla, mentorías especializadas y herramientas digitales diseñadas para emprendedores y Pymes en todo Chile.', 'sercotec-landing' );
}

function sercotec_landing_default_hero_primary_btn_text(): string {
	return __( 'Solicitar información', 'sercotec-landing' );
}

function sercotec_landing_default_hero_primary_btn_url(): string {
	return '#contacto';
}

function sercotec_landing_default_hero_secondary_btn_text(): string {
	return __( 'Ver programas', 'sercotec-landing' );
}

function sercotec_landing_default_hero_secondary_btn_url(): string {
	return '#servicios';
}

function sercotec_landing_default_hero_stat_1_value(): string {
	return '+12.000';
}

function sercotec_landing_default_hero_stat_1_label(): string {
	return __( 'Emprendedores apoyados', 'sercotec-landing' );
}

function sercotec_landing_default_hero_stat_2_value(): string {
	return '$45.000M';
}

function sercotec_landing_default_hero_stat_2_label(): string {
	return __( 'Capital entregado', 'sercotec-landing' );
}

function sercotec_landing_default_hero_stat_3_value(): string {
	return '16';
}

function sercotec_landing_default_hero_stat_3_label(): string {
	return __( 'Regiones con cobertura', 'sercotec-landing' );
}

function sercotec_landing_default_hero_card_main_title(): string {
	return __( 'Programa Semilla', 'sercotec-landing' );
}

function sercotec_landing_default_hero_card_main_text(): string {
	return __( 'Hasta $25 millones para validar y escalar tu idea de negocio.', 'sercotec-landing' );
}

function sercotec_landing_default_hero_card_secondary_title(): string {
	return __( 'Mentoría 1:1', 'sercotec-landing' );
}

function sercotec_landing_default_hero_card_secondary_text(): string {
	return __( 'Acompañamiento experto en finanzas, marketing y operaciones.', 'sercotec-landing' );
}

function sercotec_landing_get_hero_option( string $option, callable $default_fn ): string {
	$value = get_option( $option, call_user_func( $default_fn ) );

	return is_string( $value ) && '' !== $value ? $value : call_user_func( $default_fn );
}

function sercotec_landing_get_hero_badge(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_badge', 'sercotec_landing_default_hero_badge' );
}

function sercotec_landing_get_hero_title(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_title', 'sercotec_landing_default_hero_title' );
}

function sercotec_landing_get_hero_description(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_description', 'sercotec_landing_default_hero_description' );
}

function sercotec_landing_get_hero_primary_btn_text(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_primary_btn_text', 'sercotec_landing_default_hero_primary_btn_text' );
}

function sercotec_landing_get_hero_primary_btn_url(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_primary_btn_url', 'sercotec_landing_default_hero_primary_btn_url' );
}

function sercotec_landing_get_hero_secondary_btn_text(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_secondary_btn_text', 'sercotec_landing_default_hero_secondary_btn_text' );
}

function sercotec_landing_get_hero_secondary_btn_url(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_secondary_btn_url', 'sercotec_landing_default_hero_secondary_btn_url' );
}

function sercotec_landing_get_hero_stats(): array {
	return array(
		array(
			'value' => sercotec_landing_get_hero_option( 'sercotec_hero_stat_1_value', 'sercotec_landing_default_hero_stat_1_value' ),
			'label' => sercotec_landing_get_hero_option( 'sercotec_hero_stat_1_label', 'sercotec_landing_default_hero_stat_1_label' ),
		),
		array(
			'value' => sercotec_landing_get_hero_option( 'sercotec_hero_stat_2_value', 'sercotec_landing_default_hero_stat_2_value' ),
			'label' => sercotec_landing_get_hero_option( 'sercotec_hero_stat_2_label', 'sercotec_landing_default_hero_stat_2_label' ),
		),
		array(
			'value' => sercotec_landing_get_hero_option( 'sercotec_hero_stat_3_value', 'sercotec_landing_default_hero_stat_3_value' ),
			'label' => sercotec_landing_get_hero_option( 'sercotec_hero_stat_3_label', 'sercotec_landing_default_hero_stat_3_label' ),
		),
	);
}

function sercotec_landing_get_hero_card_main_title(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_card_main_title', 'sercotec_landing_default_hero_card_main_title' );
}

function sercotec_landing_get_hero_card_main_text(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_card_main_text', 'sercotec_landing_default_hero_card_main_text' );
}

function sercotec_landing_get_hero_card_secondary_title(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_card_secondary_title', 'sercotec_landing_default_hero_card_secondary_title' );
}

function sercotec_landing_get_hero_card_secondary_text(): string {
	return sercotec_landing_get_hero_option( 'sercotec_hero_card_secondary_text', 'sercotec_landing_default_hero_card_secondary_text' );
}

function sercotec_landing_hero_settings_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$badge                = sercotec_landing_get_hero_badge();
	$title                = sercotec_landing_get_hero_title();
	$description          = sercotec_landing_get_hero_description();
	$primary_btn_text     = sercotec_landing_get_hero_primary_btn_text();
	$primary_btn_url      = sercotec_landing_get_hero_primary_btn_url();
	$secondary_btn_text   = sercotec_landing_get_hero_secondary_btn_text();
	$secondary_btn_url    = sercotec_landing_get_hero_secondary_btn_url();
	$stats                = sercotec_landing_get_hero_stats();
	$card_main_title      = sercotec_landing_get_hero_card_main_title();
	$card_main_text       = sercotec_landing_get_hero_card_main_text();
	$card_secondary_title = sercotec_landing_get_hero_card_secondary_title();
	$card_secondary_text  = sercotec_landing_get_hero_card_secondary_text();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Configuración de la sección Inicio (Hero)', 'sercotec-landing' ); ?></h1>
		<p><?php esc_html_e( 'Personaliza el contenido principal que aparece al entrar a la landing.', 'sercotec-landing' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'sercotec_hero_settings' ); ?>

			<h2><?php esc_html_e( 'Encabezado', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="sercotec_hero_badge"><?php esc_html_e( 'Etiqueta', 'sercotec-landing' ); ?></label></th>
					<td>
						<input type="text" id="sercotec_hero_badge" name="sercotec_hero_badge" value="<?php echo esc_attr( $badge ); ?>" class="regular-text">
						<p class="description"><?php esc_html_e( 'Texto del distintivo superior.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sercotec_hero_title"><?php esc_html_e( 'Título', 'sercotec-landing' ); ?></label></th>
					<td>
						<input type="text" id="sercotec_hero_title" name="sercotec_hero_title" value="<?php echo esc_attr( $title ); ?>" class="large-text">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sercotec_hero_description"><?php esc_html_e( 'Descripción', 'sercotec-landing' ); ?></label></th>
					<td>
						<textarea id="sercotec_hero_description" name="sercotec_hero_description" rows="3" class="large-text"><?php echo esc_textarea( $description ); ?></textarea>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Botones de acción', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="sercotec_hero_primary_btn_text"><?php esc_html_e( 'Botón principal — texto', 'sercotec-landing' ); ?></label></th>
					<td><input type="text" id="sercotec_hero_primary_btn_text" name="sercotec_hero_primary_btn_text" value="<?php echo esc_attr( $primary_btn_text ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="sercotec_hero_primary_btn_url"><?php esc_html_e( 'Botón principal — enlace', 'sercotec-landing' ); ?></label></th>
					<td>
						<input type="text" id="sercotec_hero_primary_btn_url" name="sercotec_hero_primary_btn_url" value="<?php echo esc_attr( $primary_btn_url ); ?>" class="regular-text" placeholder="#contacto">
						<p class="description"><?php esc_html_e( 'Usa anclas internas (#contacto) o una URL completa.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="sercotec_hero_secondary_btn_text"><?php esc_html_e( 'Botón secundario — texto', 'sercotec-landing' ); ?></label></th>
					<td><input type="text" id="sercotec_hero_secondary_btn_text" name="sercotec_hero_secondary_btn_text" value="<?php echo esc_attr( $secondary_btn_text ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="sercotec_hero_secondary_btn_url"><?php esc_html_e( 'Botón secundario — enlace', 'sercotec-landing' ); ?></label></th>
					<td><input type="text" id="sercotec_hero_secondary_btn_url" name="sercotec_hero_secondary_btn_url" value="<?php echo esc_attr( $secondary_btn_url ); ?>" class="regular-text" placeholder="#servicios"></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Estadísticas', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( sprintf( __( 'Estadística %d', 'sercotec-landing' ), $i ) ); ?></th>
						<td>
							<input type="text" name="sercotec_hero_stat_<?php echo esc_attr( (string) $i ); ?>_value" value="<?php echo esc_attr( $stats[ $i - 1 ]['value'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Valor', 'sercotec-landing' ); ?>">
							<input type="text" name="sercotec_hero_stat_<?php echo esc_attr( (string) $i ); ?>_label" value="<?php echo esc_attr( $stats[ $i - 1 ]['label'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Etiqueta', 'sercotec-landing' ); ?>" style="margin-top:8px;">
						</td>
					</tr>
				<?php endfor; ?>
			</table>

			<h2><?php esc_html_e( 'Tarjetas laterales', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="sercotec_hero_card_main_title"><?php esc_html_e( 'Tarjeta principal — título', 'sercotec-landing' ); ?></label></th>
					<td><input type="text" id="sercotec_hero_card_main_title" name="sercotec_hero_card_main_title" value="<?php echo esc_attr( $card_main_title ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="sercotec_hero_card_main_text"><?php esc_html_e( 'Tarjeta principal — texto', 'sercotec-landing' ); ?></label></th>
					<td><textarea id="sercotec_hero_card_main_text" name="sercotec_hero_card_main_text" rows="2" class="large-text"><?php echo esc_textarea( $card_main_text ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="sercotec_hero_card_secondary_title"><?php esc_html_e( 'Tarjeta secundaria — título', 'sercotec-landing' ); ?></label></th>
					<td><input type="text" id="sercotec_hero_card_secondary_title" name="sercotec_hero_card_secondary_title" value="<?php echo esc_attr( $card_secondary_title ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="sercotec_hero_card_secondary_text"><?php esc_html_e( 'Tarjeta secundaria — texto', 'sercotec-landing' ); ?></label></th>
					<td><textarea id="sercotec_hero_card_secondary_text" name="sercotec_hero_card_secondary_text" rows="2" class="large-text"><?php echo esc_textarea( $card_secondary_text ); ?></textarea></td>
				</tr>
			</table>

			<?php submit_button( __( 'Guardar cambios', 'sercotec-landing' ) ); ?>
		</form>
	</div>
	<?php
}
