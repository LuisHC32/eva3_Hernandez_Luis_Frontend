<?php
/**
 * Textos y tipografía editables de la sección Servicios.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_services_settings_menu(): void {
	add_submenu_page(
		'edit.php?post_type=sercotec_servicio',
		__( 'Configuración de sección', 'sercotec-landing' ),
		__( 'Configuración de sección', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-services-texts',
		'sercotec_landing_services_settings_page'
	);
}
add_action( 'admin_menu', 'sercotec_landing_services_settings_menu' );

function sercotec_landing_sanitize_font_size( $value, int $default ): int {
	$size = absint( $value );

	return $size > 0 ? $size : $default;
}

function sercotec_landing_sanitize_services_label_size( $value ): int {
	return sercotec_landing_sanitize_font_size( $value, sercotec_landing_default_services_label_size() );
}
  
function sercotec_landing_sanitize_services_label_align( $value ): string {
	return sercotec_landing_sanitize_text_align( $value, sercotec_landing_default_services_label_align() );
}

function sercotec_landing_sanitize_services_title_size( $value ): int {
	return sercotec_landing_sanitize_font_size( $value, sercotec_landing_default_services_title_size() );
}

function sercotec_landing_sanitize_services_subtitle_size( $value ): int {
	return sercotec_landing_sanitize_font_size( $value, sercotec_landing_default_services_subtitle_size() );
}

function sercotec_landing_sanitize_text_align( $value, string $default ): string {
	$allowed = array( 'left', 'center', 'right', 'justify' );
	$value   = sanitize_key( (string) $value );

	return in_array( $value, $allowed, true ) ? $value : $default;
}

function sercotec_landing_sanitize_services_title_align( $value ): string {
	return sercotec_landing_sanitize_text_align( $value, sercotec_landing_default_services_title_align() );
}

function sercotec_landing_sanitize_services_subtitle_align( $value ): string {
	return sercotec_landing_sanitize_text_align( $value, sercotec_landing_default_services_subtitle_align() );
}

function sercotec_landing_sanitize_services_mid_paragraph( $value ): string {
	return wp_kses_post( wp_unslash( (string) $value ) );
}

function sercotec_landing_sanitize_services_mid_paragraph_size( $value ): int {
	return sercotec_landing_sanitize_font_size( $value, sercotec_landing_default_services_mid_paragraph_size() );
}

function sercotec_landing_sanitize_services_mid_paragraph_align( $value ): string {
	return sercotec_landing_sanitize_text_align( $value, sercotec_landing_default_services_mid_paragraph_align() );
}

function sercotec_landing_sanitize_services_mid_paragraph_after_order( $value ): int {
	return max( 0, (int) $value );
}

function sercotec_landing_register_services_settings(): void {
	register_setting(
		'sercotec_services_texts',
		'sercotec_services_label',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => sercotec_landing_default_services_label(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_title',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => sercotec_landing_default_services_title(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_subtitle',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
			'default'           => sercotec_landing_default_services_subtitle(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_label_size',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_label_size',
			'default'           => sercotec_landing_default_services_label_size(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_label_align',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_label_align',
			'default'           => sercotec_landing_default_services_label_align(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_title_size',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_title_size',
			'default'           => sercotec_landing_default_services_title_size(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_subtitle_size',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_subtitle_size',
			'default'           => sercotec_landing_default_services_subtitle_size(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_title_align',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_title_align',
			'default'           => sercotec_landing_default_services_title_align(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_subtitle_align',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_subtitle_align',
			'default'           => sercotec_landing_default_services_subtitle_align(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_mid_paragraph',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_mid_paragraph',
			'default'           => '',
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_mid_paragraph_after_order',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_mid_paragraph_after_order',
			'default'           => 0,
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_mid_paragraph_size',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_mid_paragraph_size',
			'default'           => sercotec_landing_default_services_mid_paragraph_size(),
		)
	);

	register_setting(
		'sercotec_services_texts',
		'sercotec_services_mid_paragraph_align',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sercotec_landing_sanitize_services_mid_paragraph_align',
			'default'           => sercotec_landing_default_services_mid_paragraph_align(),
		)
	);
}
add_action( 'admin_init', 'sercotec_landing_register_services_settings' );

function sercotec_landing_default_services_label(): string {
	return __( 'Servicios', 'sercotec-landing' );
}

function sercotec_landing_default_services_title(): string {
	return __( 'Programas diseñados para cada etapa de tu empresa', 'sercotec-landing' );
}

function sercotec_landing_default_services_subtitle(): string {
	return __( 'Elige el apoyo que necesitas y postula en línea con acompañamiento de nuestros ejecutivos.', 'sercotec-landing' );
}

function sercotec_landing_default_services_label_size(): int {
	return 13;
}

function sercotec_landing_default_services_title_size(): int {
	return 40;
}

function sercotec_landing_default_services_subtitle_size(): int {
	return 17;
}

function sercotec_landing_default_services_label_align(): string {
	return 'center';
}

function sercotec_landing_default_services_title_align(): string {
	return 'center';
}

function sercotec_landing_default_services_subtitle_align(): string {
	return 'center';
}

function sercotec_landing_default_services_mid_paragraph_size(): int {
	return 17;
}

function sercotec_landing_default_services_mid_paragraph_align(): string {
	return 'center';
}

function sercotec_landing_get_services_label(): string {
	$label = get_option( 'sercotec_services_label', sercotec_landing_default_services_label() );

	return $label ?: sercotec_landing_default_services_label();
}

function sercotec_landing_get_services_title(): string {
	$title = get_option( 'sercotec_services_title', sercotec_landing_default_services_title() );

	return $title ?: sercotec_landing_default_services_title();
}

function sercotec_landing_get_services_subtitle(): string {
	$subtitle = get_option( 'sercotec_services_subtitle', sercotec_landing_default_services_subtitle() );

	return $subtitle ?: sercotec_landing_default_services_subtitle();
}

function sercotec_landing_get_services_label_size(): int {
	return (int) get_option( 'sercotec_services_label_size', sercotec_landing_default_services_label_size() );
}

function sercotec_landing_get_services_title_size(): int {
	return (int) get_option( 'sercotec_services_title_size', sercotec_landing_default_services_title_size() );
}

function sercotec_landing_get_services_subtitle_size(): int {
	return (int) get_option( 'sercotec_services_subtitle_size', sercotec_landing_default_services_subtitle_size() );
}

function sercotec_landing_get_services_label_align(): string {
	return sercotec_landing_sanitize_text_align(
		get_option( 'sercotec_services_label_align', sercotec_landing_default_services_label_align() ),
		sercotec_landing_default_services_label_align()
	);
}

function sercotec_landing_get_services_title_align(): string {
	return sercotec_landing_sanitize_text_align(
		get_option( 'sercotec_services_title_align', sercotec_landing_default_services_title_align() ),
		sercotec_landing_default_services_title_align()
	);
}

function sercotec_landing_get_services_subtitle_align(): string {
	return sercotec_landing_sanitize_text_align(
		get_option( 'sercotec_services_subtitle_align', sercotec_landing_default_services_subtitle_align() ),
		sercotec_landing_default_services_subtitle_align()
	);
}

function sercotec_landing_get_services_mid_paragraph_html(): string {
	return trim( (string) get_option( 'sercotec_services_mid_paragraph', '' ) );
}

function sercotec_landing_get_services_mid_paragraph_after_order(): int {
	return sercotec_landing_sanitize_services_mid_paragraph_after_order(
		get_option( 'sercotec_services_mid_paragraph_after_order', 0 )
	);
}

function sercotec_landing_get_services_mid_paragraph_size(): int {
	return (int) get_option( 'sercotec_services_mid_paragraph_size', sercotec_landing_default_services_mid_paragraph_size() );
}

function sercotec_landing_get_services_mid_paragraph_align(): string {
	return sercotec_landing_sanitize_text_align(
		get_option( 'sercotec_services_mid_paragraph_align', sercotec_landing_default_services_mid_paragraph_align() ),
		sercotec_landing_default_services_mid_paragraph_align()
	);
}

/**
 * @return array{show: bool, before: array<int, array<string, mixed>>, after: array<int, array<string, mixed>>}
 */
function sercotec_landing_split_services_for_mid_paragraph( array $services ): array {
	$mid_html  = sercotec_landing_get_services_mid_paragraph_html();
	$mid_after = sercotec_landing_get_services_mid_paragraph_after_order();

	if ( '' === $mid_html || $mid_after <= 0 || empty( $services ) ) {
		return array(
			'show'   => false,
			'before' => $services,
			'after'  => array(),
		);
	}

	$count = count( $services );

	if ( $mid_after >= $count ) {
		return array(
			'show'   => false,
			'before' => $services,
			'after'  => array(),
		);
	}

	$before = array_slice( $services, 0, $mid_after );
	$after  = array_slice( $services, $mid_after );

	if ( empty( $before ) || empty( $after ) ) {
		return array(
			'show'   => false,
			'before' => $services,
			'after'  => array(),
		);
	}

	return array(
		'show'   => true,
		'before' => $before,
		'after'  => $after,
	);
}

function sercotec_landing_get_subtitle_margin_inline( string $align ): string {
	if ( 'center' === $align ) {
		return 'auto';
	}

	if ( 'right' === $align ) {
		return '0 0 0 auto';
	}

	return '0';
}

function sercotec_landing_get_services_section_style(): string {
	$subtitle_align    = sercotec_landing_get_services_subtitle_align();
	$mid_paragraph_align = sercotec_landing_get_services_mid_paragraph_align();

	$styles = array(
		'--services-label-size'                 => sercotec_landing_get_services_label_size() . 'px',
		'--services-label-align'                => sercotec_landing_get_services_label_align(),
		'--services-title-size'                 => sercotec_landing_get_services_title_size() . 'px',
		'--services-subtitle-size'              => sercotec_landing_get_services_subtitle_size() . 'px',
		'--services-title-align'                => sercotec_landing_get_services_title_align(),
		'--services-subtitle-align'             => $subtitle_align,
		'--services-subtitle-margin-inline'     => sercotec_landing_get_subtitle_margin_inline( $subtitle_align ),
		'--services-mid-paragraph-size'         => sercotec_landing_get_services_mid_paragraph_size() . 'px',
		'--services-mid-paragraph-align'        => $mid_paragraph_align,
		'--services-mid-paragraph-margin-inline' => sercotec_landing_get_subtitle_margin_inline( $mid_paragraph_align ),
	);

	$parts = array();

	foreach ( $styles as $property => $value ) {
		$parts[] = $property . ':' . $value;
	}

	return implode( ';', $parts );
}

function sercotec_landing_services_alignment_options(): array {
	return array(
		'left'    => __( 'Izquierda', 'sercotec-landing' ),
		'center'  => __( 'Centro', 'sercotec-landing' ),
		'right'   => __( 'Derecha', 'sercotec-landing' ),
		'justify' => __( 'Justificado', 'sercotec-landing' ),
	);
}

function sercotec_landing_services_alignment_field( string $name, string $value ): void {
	$options = sercotec_landing_services_alignment_options();
	?>
	<div class="sercotec-align-toolbar" role="radiogroup" aria-label="<?php esc_attr_e( 'Alineación del texto', 'sercotec-landing' ); ?>">
		<?php foreach ( $options as $key => $label ) : ?>
			<label class="sercotec-align-toolbar__option">
				<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $value, $key ); ?>>
				<span class="dashicons dashicons-editor-<?php echo esc_attr( 'justify' === $key ? 'justify' : 'align' . $key ); ?>" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
			</label>
		<?php endforeach; ?>
	</div>
	<?php
}

function sercotec_landing_services_settings_assets( string $hook ): void {
	$allowed_hooks = array(
		'sercotec_servicio_page_sercotec-services-texts',
		'sercotec_about_card_page_sercotec-about',
		'toplevel_page_sercotec-about',
	);

	if ( ! in_array( $hook, $allowed_hooks, true ) ) {
		return;
	}

	if ( 'sercotec_servicio_page_sercotec-services-texts' === $hook ) {
		wp_enqueue_editor();
	}

	wp_enqueue_style( 'dashicons' );
	sercotec_landing_enqueue_theme_styles( array( 'dashicons' ) );
}
add_action( 'admin_enqueue_scripts', 'sercotec_landing_services_settings_assets' );

function sercotec_landing_services_typography_field( string $size_id, int $size_value, string $align_name, string $align_value ): void {
	?>
	<div class="sercotec-typography-controls">
		<input
			type="number"
			id="<?php echo esc_attr( $size_id ); ?>"
			name="<?php echo esc_attr( $size_id ); ?>"
			value="<?php echo esc_attr( (string) $size_value ); ?>"
			step="1"
			class="small-text sercotec-typography-controls__input"
		>
		<span class="sercotec-typography-controls__unit">px</span>
		<span class="sercotec-typography-controls__divider" aria-hidden="true"></span>
		<?php sercotec_landing_services_alignment_field( $align_name, $align_value ); ?>
	</div>
	<?php
}

function sercotec_landing_services_font_size_field( string $id, int $value ): void {
	?>
	<input
		type="number"
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $id ); ?>"
		value="<?php echo esc_attr( (string) $value ); ?>"
		step="1"
		class="small-text"
	>
	<span class="sercotec-typography-controls__unit">px</span>
	<?php
}

function sercotec_landing_services_settings_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$title            = sercotec_landing_get_services_title();
	$subtitle         = sercotec_landing_get_services_subtitle();
	$label            = sercotec_landing_get_services_label();
	$label_size       = sercotec_landing_get_services_label_size();
	$label_align      = sercotec_landing_get_services_label_align();
	$title_size       = sercotec_landing_get_services_title_size();
	$subtitle_size    = sercotec_landing_get_services_subtitle_size();
	$title_align            = sercotec_landing_get_services_title_align();
	$subtitle_align         = sercotec_landing_get_services_subtitle_align();
	$mid_paragraph          = sercotec_landing_get_services_mid_paragraph_html();
	$mid_paragraph_after    = sercotec_landing_get_services_mid_paragraph_after_order();
	$mid_paragraph_size     = sercotec_landing_get_services_mid_paragraph_size();
	$mid_paragraph_align    = sercotec_landing_get_services_mid_paragraph_align();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Configuración de la sección Servicios', 'sercotec-landing' ); ?></h1>
		<p><?php esc_html_e( 'Personaliza los textos, tamaños de letra y alineación que se muestran en la landing.', 'sercotec-landing' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'sercotec_services_texts' ); ?>

			<h2><?php esc_html_e( 'Textos', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_services_label"><?php esc_html_e( 'Etiqueta', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="sercotec_services_label"
							name="sercotec_services_label"
							value="<?php echo esc_attr( $label ); ?>"
							class="regular-text"
						>
						<p class="description"><?php esc_html_e( 'Texto pequeño superior (ej: Servicios).', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_services_title"><?php esc_html_e( 'Título', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="sercotec_services_title"
							name="sercotec_services_title"
							value="<?php echo esc_attr( $title ); ?>"
							class="large-text"
						>
						<p class="description"><?php esc_html_e( 'Encabezado principal de la sección.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_services_subtitle"><?php esc_html_e( 'Subtítulo', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<textarea
							id="sercotec_services_subtitle"
							name="sercotec_services_subtitle"
							rows="3"
							class="large-text"
						><?php echo esc_textarea( $subtitle ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Texto descriptivo debajo del título.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_services_mid_paragraph"><?php esc_html_e( 'Párrafo intermedio', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<?php
						wp_editor(
							$mid_paragraph,
							'sercotec_services_mid_paragraph',
							array(
								'textarea_name' => 'sercotec_services_mid_paragraph',
								'textarea_rows' => 5,
								'media_buttons' => false,
								'teeny'         => false,
								'quicktags'     => true,
							)
						);
						?>
						<p class="description"><?php esc_html_e( 'Texto que aparece entre las tarjetas de servicios (no es un servicio).', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_services_mid_paragraph_after_order"><?php esc_html_e( 'Insertar después del servicio n.º', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="number"
							id="sercotec_services_mid_paragraph_after_order"
							name="sercotec_services_mid_paragraph_after_order"
							value="<?php echo esc_attr( (string) $mid_paragraph_after ); ?>"
							min="0"
							step="1"
							class="small-text"
						>
						<p class="description"><?php esc_html_e( 'Cantidad de servicios que van antes del párrafo (ej: 1 = el párrafo aparece después del primer servicio). Dejar en 0 para ocultarlo.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Tipografía', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_services_label_size"><?php esc_html_e( 'Etiqueta', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<?php sercotec_landing_services_typography_field( 'sercotec_services_label_size', $label_size, 'sercotec_services_label_align', $label_align ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_services_title_size"><?php esc_html_e( 'Título', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<?php sercotec_landing_services_typography_field( 'sercotec_services_title_size', $title_size, 'sercotec_services_title_align', $title_align ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_services_subtitle_size"><?php esc_html_e( 'Subtítulo', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<?php sercotec_landing_services_typography_field( 'sercotec_services_subtitle_size', $subtitle_size, 'sercotec_services_subtitle_align', $subtitle_align ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_services_mid_paragraph_size"><?php esc_html_e( 'Párrafo intermedio', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<?php sercotec_landing_services_typography_field( 'sercotec_services_mid_paragraph_size', $mid_paragraph_size, 'sercotec_services_mid_paragraph_align', $mid_paragraph_align ); ?>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Guardar cambios', 'sercotec-landing' ) ); ?>
		</form>
	</div>
	<?php
}
