<?php
/**
 * Custom Post Type: Preguntas frecuentes (FAQ).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_register_faq_cpt() {
	$labels = array(
		'name'               => __( 'Preguntas frecuentes', 'sercotec-landing' ),
		'singular_name'      => __( 'Pregunta frecuente', 'sercotec-landing' ),
		'menu_name'          => __( 'FAQ', 'sercotec-landing' ),
		'add_new'            => __( 'Agregar pregunta', 'sercotec-landing' ),
		'add_new_item'       => __( 'Agregar nueva pregunta', 'sercotec-landing' ),
		'edit_item'          => __( 'Editar pregunta', 'sercotec-landing' ),
		'new_item'           => __( 'Nueva pregunta', 'sercotec-landing' ),
		'view_item'          => __( 'Ver pregunta', 'sercotec-landing' ),
		'search_items'       => __( 'Buscar preguntas', 'sercotec-landing' ),
		'not_found'          => __( 'No se encontraron preguntas.', 'sercotec-landing' ),
		'not_found_in_trash' => __( 'No hay preguntas en la papelera.', 'sercotec-landing' ),
		'all_items'          => __( 'Todas las preguntas', 'sercotec-landing' ),
	);

	register_post_type(
		'sercotec_faq',
		array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-editor-help',
			'menu_position'       => 25,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'page-attributes' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'show_in_rest'        => false,
		)
	);
}
add_action( 'init', 'sercotec_landing_register_faq_cpt' );

function sercotec_landing_faq_meta_boxes() {
	add_meta_box(
		'sercotec_faq_details',
		__( 'Instrucciones', 'sercotec-landing' ),
		'sercotec_landing_faq_meta_box_render',
		'sercotec_faq',
		'normal',
		'low'
	);
}
add_action( 'add_meta_boxes', 'sercotec_landing_faq_meta_boxes' );

function sercotec_landing_faq_meta_box_render( WP_Post $post ) {
	?>
	<p class="description">
		<?php esc_html_e( 'Pregunta = título. Respuesta = contenido del editor. Orden = posición en el acordeón.', 'sercotec-landing' ); ?>
	</p>
	<?php
}

function sercotec_landing_faq_admin_columns( array $columns ): array {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['faq_order'] = __( 'Orden', 'sercotec-landing' );
		}
	}

	return $new;
}
add_filter( 'manage_sercotec_faq_posts_columns', 'sercotec_landing_faq_admin_columns' );

function sercotec_landing_faq_admin_column_content( string $column, int $post_id ): void {
	if ( 'faq_order' === $column ) {
		echo esc_html( (string) get_post( $post_id )->menu_order );
	}
}
add_action( 'manage_sercotec_faq_posts_custom_column', 'sercotec_landing_faq_admin_column_content', 10, 2 );

function sercotec_landing_get_faqs(): array {
	$query = new WP_Query(
		array(
			'post_type'      => 'sercotec_faq',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);

	$faqs = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();
			$answer  = sercotec_landing_get_card_display_content( $post_id );

			if ( '' === $answer ) {
				continue;
			}

			$faqs[] = array(
				'question' => get_the_title(),
				'answer'   => $answer,
			);
		}
		wp_reset_postdata();
	}

	return $faqs;
}

function sercotec_landing_seed_default_faqs(): void {
	$existing = get_posts(
		array(
			'post_type'      => 'sercotec_faq',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing ) ) {
		return;
	}

	$defaults = array(
		array(
			'question' => '¿Quiénes pueden postular a los programas de SERCOTEC?',
			'answer'   => 'Personas naturales y jurídicas con proyectos de emprendimiento o Pymes en operación. Cada convocatoria define requisitos específicos de antigüedad, tamaño y rubro.',
			'order'    => 1,
		),
		array(
			'question' => '¿El financiamiento debe devolverse?',
			'answer'   => 'Los programas de capital semilla entregan recursos no reembolsables. En cambio, los servicios de consultoría pueden tener copagos según el instrumento.',
			'order'    => 2,
		),
		array(
			'question' => '¿Cuánto demora el proceso de evaluación?',
			'answer'   => 'El plazo promedio es de 30 a 45 días hábiles desde el cierre de la postulación, dependiendo del programa y la región.',
			'order'    => 3,
		),
		array(
			'question' => '¿Puedo postular si recién estoy formalizando mi empresa?',
			'answer'   => 'Sí. Existen líneas para idea de negocio, etapa temprana y consolidación. Te recomendamos revisar la convocatoria vigente en tu región.',
			'order'    => 4,
		),
		array(
			'question' => '¿Cómo recibo acompañamiento después de adjudicar?',
			'answer'   => 'Un ejecutivo SERCOTEC te contactará para definir hitos, mentorías y reportes. También tendrás acceso a la plataforma de seguimiento en línea.',
			'order'    => 5,
		),
	);

	foreach ( $defaults as $faq ) {
		wp_insert_post(
			array(
				'post_type'    => 'sercotec_faq',
				'post_title'   => $faq['question'],
				'post_content' => $faq['answer'],
				'post_status'  => 'publish',
				'menu_order'   => $faq['order'],
			),
			true
		);
	}
}
add_action( 'after_switch_theme', 'sercotec_landing_seed_default_faqs' );
