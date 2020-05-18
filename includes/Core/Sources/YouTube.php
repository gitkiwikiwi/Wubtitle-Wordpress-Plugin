<?php
/**
 * This file describes handle operation on subtitle.
 *
 * @author     Nicola Palermo
 * @since      1.0.0
 * @package    Ear2Words\Core
 */

namespace Ear2Words\Core\Sources;

/**
 * This class handle subtitles.
 */
class YouTube implements \Ear2Words\Core\VideoSource {

	/**
	 * Effettua la chiamata all'endpoint.
	 *
	 * @param string $id_video il body della richiesta da inviare.
	 */
	public function send_job_to_backend( $id_video ) {
		$response = wp_remote_post(
			ENDPOINT . 'job/create',
			array(
				'method'  => 'POST',
				'headers' => array(
					'licenseKey'   => get_option( 'ear2words_license_key' ),
					'Content-Type' => 'application/json; charset=utf-8',
				),
				'body'    => wp_json_encode(
					array(
						'source' => 'YOUTUBE',
						'data'   => array(
							'youtubeId' => $id_video,
						),
					)
				),
			)
		);
		return $response;
	}

	/**
	 * Recupera la trascrizioni.
	 *
	 * @param string $id_video id del video youtube.
	 * @param string $from post type dal quale viene fatta la richiesta.
	 */
	public function get_subtitle( $id_video, $from ) {
		$get_info_url = "https://www.youtube.com/get_video_info?video_id=$id_video";

		$file_info = array();

		$response = wp_remote_get( $get_info_url );

		if ( is_wp_error( $response ) ) {
			return '';
		}

		$file = wp_remote_retrieve_body( $response );

		parse_str( $file, $file_info );

		$url = json_decode( $file_info['player_response'] )->captions->playerCaptionsTracklistRenderer->captionTracks[0]->baseUrl . '&fmt=json3&xorb=2&xobt=3&xovt=3';

		$response = wp_remote_get( $url );

		$text = '';

		foreach ( json_decode( $response['body'] )->events as $event ) {
			if ( isset( $event->segs ) ) {
				foreach ( $event->segs as $seg ) {
					$text .= $seg->utf8;
				}
			}
		}
		if ( 'default_post_type' === $from ) {
			$trascript_post = array(
				'post_title'   => esc_html( __( 'Autogenerated. YouTube ', 'ear2words' ) . $id_video ),
				'post_content' => $text,
				'post_type'    => 'transcript',
				'post_status'  => 'publish',
				'meta_input'   => array(
					'_video_id'          => sanitize_text_field( wp_unslash( $id_video ) ),
					'_transcript_source' => 'youtube',
				),
			);
			$transcript_id  = wp_insert_post( $trascript_post );
			return $transcript_id;
		}
		return $text;
	}
	/**
	 * Esegue la chiamata e poi recupera le trascrizioni.
	 *
	 * @param string $url_video url del video youtube.
	 * @param string $from post type dal quale viene fatta la richiesta.
	 */
	public function send_job_and_get_transcription( $url_video, $from ) {
		$url_parts    = wp_parse_url( $url_video );
		$query_params = array();
		parse_str( $url_parts['query'], $query_params );
		$id_video = $query_params['v'];
		$args     = array(
			'post_type'      => 'transcript',
			'posts_per_page' => 1,
			'meta_key'       => '_video_id',
			'meta_value'     => sanitize_text_field( wp_unslash( $id_video ) ),
		);
		$posts    = get_posts( $args );
		if ( ! empty( $posts ) && 'default_post_type' === $from ) {
			return $posts[0]->ID;
		}
		$response = $this->send_job_to_backend( $id_video );
		if ( '201' === $response ) {
			$response = $this->get_subtitle( $id_video, $from );
			return $response;
		}
		return 'error';
	}
}
