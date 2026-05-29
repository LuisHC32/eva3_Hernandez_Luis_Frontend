<?php
/**
 * Protección del formulario de contacto: rate limiting y Turnstile.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_get_client_ip(): string {
	$headers = array(
		'HTTP_CF_CONNECTING_IP',
		'HTTP_X_FORWARDED_FOR',
		'REMOTE_ADDR',
	);

	foreach ( $headers as $header ) {
		if ( empty( $_SERVER[ $header ] ) ) {
			continue;
		}

		$value = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );

		if ( 'HTTP_X_FORWARDED_FOR' === $header ) {
			$parts = explode( ',', $value );
			$value = trim( $parts[0] );
		}

		if ( filter_var( $value, FILTER_VALIDATE_IP ) ) {
			return $value;
		}
	}

	return '0.0.0.0';
}

function sercotec_landing_get_contact_rate_limit_max(): int {
	$max = (int) get_option( 'sercotec_contact_rate_limit_max', 3 );

	return max( 1, min( 20, $max ) );
}

function sercotec_landing_get_contact_rate_limit_window(): int {
	$minutes = (int) get_option( 'sercotec_contact_rate_limit_window', 15 );

	return max( 1, min( 1440, $minutes ) ) * MINUTE_IN_SECONDS;
}

function sercotec_landing_contact_is_rate_limited(): bool {
	$ip   = sercotec_landing_get_client_ip();
	$key  = 'sercotec_contact_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );

	return $hits >= sercotec_landing_get_contact_rate_limit_max();
}

function sercotec_landing_contact_record_rate_limit_hit(): void {
	$ip   = sercotec_landing_get_client_ip();
	$key  = 'sercotec_contact_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );

	set_transient( $key, $hits + 1, sercotec_landing_get_contact_rate_limit_window() );
}

function sercotec_landing_get_turnstile_site_key(): string {
	if ( defined( 'SERCOTEC_TURNSTILE_SITE_KEY' ) && SERCOTEC_TURNSTILE_SITE_KEY ) {
		return sanitize_text_field( (string) SERCOTEC_TURNSTILE_SITE_KEY );
	}

	return sanitize_text_field( (string) get_option( 'sercotec_contact_turnstile_site_key', '' ) );
}

function sercotec_landing_get_turnstile_secret_key(): string {
	if ( defined( 'SERCOTEC_TURNSTILE_SECRET_KEY' ) && SERCOTEC_TURNSTILE_SECRET_KEY ) {
		return sanitize_text_field( (string) SERCOTEC_TURNSTILE_SECRET_KEY );
	}

	return sanitize_text_field( (string) get_option( 'sercotec_contact_turnstile_secret_key', '' ) );
}

function sercotec_landing_turnstile_is_enabled(): bool {
	return '' !== sercotec_landing_get_turnstile_site_key() && '' !== sercotec_landing_get_turnstile_secret_key();
}

function sercotec_landing_verify_turnstile( string $token ) {
	if ( ! sercotec_landing_turnstile_is_enabled() ) {
		return true;
	}

	if ( '' === $token ) {
		return new WP_Error(
			'turnstile_missing',
			__( 'Completa la verificación de seguridad.', 'sercotec-landing' )
		);
	}

	$response = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => sercotec_landing_get_turnstile_secret_key(),
				'response' => $token,
				'remoteip' => sercotec_landing_get_client_ip(),
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error(
			'turnstile_request',
			__( 'No pudimos verificar la seguridad del formulario. Intenta nuevamente.', 'sercotec-landing' )
		);
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( empty( $data['success'] ) ) {
		return new WP_Error(
			'turnstile_invalid',
			__( 'Verificación de seguridad fallida. Intenta nuevamente.', 'sercotec-landing' )
		);
	}

	return true;
}
