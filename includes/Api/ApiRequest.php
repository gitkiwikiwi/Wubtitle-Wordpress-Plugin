<?php
/**
 * Questo file implementa la chiamata http.
 *
 * @author     Alessio Catania
 * @since      0.1.0
 * @package    Ear2Words\Api
 */

namespace Ear2Words\Api;

/**
 * Questa classe implementa la chiamata alla APIGateway,
 */
class ApiRequest {
	/**
	 * Init class action.
	 */
	public function run() {
		add_action( 'wp_ajax_submitVideo', array( $this, 'send_request' ) );
		add_action( 'wp_ajax_nopriv_submitVideo', array( $this, 'send_request' ) );
		add_action( 'init', array( $this, 'status_register_meta' ) );
	}
	/**
	 * Get media attachment.
	 *
	 * @param integer $id_attachment id del video.
	 */
	public function get_media_metadata( $id_attachment ) {
		return wp_get_attachment_metadata( $id_attachment );
	}

	/**
	 * Verifica la validazione
	 *
	 *  @param array $array post.
	 */
	public function sanitize_input( $array ) {
		if ( ! isset( $array['id_attachment'] ) || ! isset( $array['src_attachment'] ) ) {
			return false;
		}
		$array['id_attachment']  = sanitize_text_field( wp_unslash( $array['id_attachment'] ) );
		$array['src_attachment'] = sanitize_text_field( wp_unslash( $array['src_attachment'] ) );
		return $array;
	}
	/**
	 *  Creo il body della richiesta.
	 *
	 * @param array $data contiene id_attachment e src_attachment.
	 */
	public function set_body_request( $data ) {
		$id_attachment = (int) $data['id_attachment'];
		$video_data    = $this->get_media_metadata( $id_attachment );
		if ( ! is_numeric( $id_attachment ) || $video_data['filesize'] <= 0 || $video_data['length'] <= 0 || ! filter_var( $data['src_attachment'], FILTER_VALIDATE_URL ) ) {
			return false;
		}
		$body = array(
			'data' => array(
				'attachmentId' => $id_attachment,
				'url'          => $data['src_attachment'],
				'size'         => $video_data['filesize'],
				'duration'     => $video_data['length'],
			),
		);
		return $body;
	}
	/**
	 * Da qui invierò la richiesta HTTP.
	 */
	public function send_request() {
		$license_key = get_option( 'ear2words_license_key' );

		if ( ! isset( $_POST['_ajax_nonce'] ) ) {
			wp_send_json_error( 'Si è verificato un errore durante la creazione dei sottotitoli. Riprova di nuovo tra qualche minuto' );
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) );
		if ( ! check_ajax_referer( 'itr_ajax_nonce', $nonce ) ) {
			wp_send_json_error( 'Errore, richiesta non valida' );
		}
		$data_attachment = $this->sanitize_input( $_POST );
		if ( ! $data_attachment ) {
			wp_send_json_error( 'Si è verificato un errore durante la creazione dei sottotitoli. Riprova di nuovo tra qualche minuto' );
		}
		if ( empty( $license_key ) ) {
			wp_send_json_error( 'Impossibile creare i sottotitoli. La  licenza del prodotto è assente' );
		}
			$body = $this->set_body_request( $data_attachment );
		if ( ! $body ) {
			wp_send_json_error( 'Si è verificato un errore durante la creazione dei sottotitoli. Riprova di nuovo tra qualche minuto' );
		}
			$response      = wp_remote_post(
				ENDPOINT_URL,
				array(
					'method'  => 'POST',
					'headers' => array(
						'licenseKey'   => $license_key,
						'Content-Type' => 'application/json; charset=utf-8',
					),
					'body'    => wp_json_encode( $body ),
				)
			);
			$code_response = $response['response']['code'];
			$message       = array(
				'401' => 'Si è verificato un errore durante la creazione dei sottotitoli. Riprova di nuovo tra qualche minuto',
				'403' => 'Impossibile creare i sottotitoli. La  licenza del prodotto non è valida',
			);
			if ( 201 !== $code_response ) {
				wp_send_json_error( $message[ $code_response ] );
			}
			update_post_meta( $data_attachment['id_attachment'], 'ear2words_job_uuid', $response['response']['data']['jobId'], true );
			update_post_meta( $data_attachment['$id_attachment'], 'ear2words_status', 'pending' );
			wp_send_json_success( $code_response );
	}

	/**
	 * Registro post meta per lo stato.
	 */
	public function status_register_meta() {
		register_post_meta(
			'attachment',
			'ear2words_status',
			array(
				'show_in_rest' => true,
				'type'         => 'string',
				'single'       => true,
			)
		);
	}
}
