<?php
/**
 * Plugin Name: JP bbPress Slack Integration
 * Description: Send notifications of new bbPress topics and replies to a Slack channel.
 * Author: Josh Pollock
 * Author URI: http://joshpress.net
 * Version: 0.2.0
 * Plugin URI: https://github.com/Shelob9/jp-bbpress-slack-integration
 * License: GNU GPLv2+
 */
/**
 * Copyright (c) 2014 Josh Pollock (email : jpollock412@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

add_action( 'bbp_new_reply',  'jp_bbp_slack_integration', 20 );
add_action( 'bbp_new_topic',  'jp_bbp_slack_integration', 20 );
function jp_bbp_slack_integration(  $id ) {
	$url = get_option( 'jp_bbpress_slack_webhook', false );
	if ( $url ) {
		$post = get_post( $id );
		$link = get_permalink( $id );
		$link = htmlspecialchars( $link );

		$excerpt = wp_trim_words( $post->post_content );
		if ( 500 < strlen( $excerpt ) ) {
			$excerpt = substr( $excerpt, 0, 500 );
		}

		$payload = array(
			'text'        => __( 'New bbPress Update', 'jp-bbpress-slack' ),
			'attachments' => array(
				'fallback' => $link,
				'color'    => '#ff000',
				'fields'   => array(
					'title' => $link,
					'value' => $link,
					'text'  => $excerpt,
				)
			),
		);
		$output  = 'payload=' . json_encode( $payload );


		wp_remote_post( $url, array(
			'body' => $output,
		) );

	}

}

if ( is_admin() ) {
	new jp_bbp_slack_integration_admin();
}

class jp_bbp_slack_integration_admin {

	private $option_name = 'jp_bbpress_slack_webhook';
	private $nonce_name = '_jp_bbpress_slack_nonce';
	private $nonce_action = '_jp_bbpress_slack_nonce_action';

	function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	/**
	 * Add the menu
	 *
	 * @since 0.2.0
	 */
	function menu() {
		add_options_page(
			__( 'bbPress Slack Integration', 'jp-bbpress-slack' ),
			__( 'bbPress Slack Integration', 'jp-bbpress-slack' ),
			'manage_options',
			'jp_bbp_slack',
			array( $this, 'page' )
		);
	}

	/**
	 * Render admin page and handle saving.
	 *
	 * @TODO Use AJAX for saving
	 *
	 * @since 0.2.0
	 *
	 * @return string the admin page.
	 */
	function page() {
		echo $this->instructions();
		echo $this->form();
		if ( isset( $_POST ) && isset( $_POST[ $this->nonce_name ] ) && wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action ) ) {
			if ( isset( $_POST[ 'slack-hook' ] )) {
				$option = esc_url_raw( $_POST[ 'slack-hook' ] );
				$option = filter_var( $option, FILTER_VALIDATE_URL );
				if ( $option ) {
					update_option( $this->option_name, $option );
					if ( isset( $_POST[ '_wp_http_referer' ] ) && $_POST[ '_wp_http_referer' ] ) {
						$location = $_POST['_wp_http_referer'];
						die( '<script type="text/javascript">'
						     . 'document.location = "' . str_replace( '&amp;', '&', esc_js( $location ) ) . '";'
						     . '</script>' );
					}
				}
			}

		}

	}

	/**
	 * Admin form
	 *
	 * @since 0.2.0
	 *
	 * @return string The form.
	 */
	function form() {
		$out[] = '<form id="jp_bbp_slack_integration" method="POST" action="options-general.php?page=jp_bbp_slack">';
		$out[] = wp_nonce_field( $this->nonce_action, $this->nonce_name, true, false );
		$url = get_option( $this->option_name, '' );
		$out[] = '<input id="slack-hook" name="slack-hook"type="text" value="'.esc_url( $url ).'"></input>';
		$out[] = '<input type="submit" class="button-primary">';
		$out[] = '</form>';

		return implode( $out );

	}

	/**
	 * Show instructions.
	 *
	 * @since 0.2.0
	 *
	 * @return string The instructions.
	 */
	function instructions() {
		$header = '<h3>' . __( 'Instructions:', 'jp-bbpress-slack' ) .'</h3>';
		$instructions = array(
			__( 'Go To https://<your-team-name>.slack.com/services/new/incoming-webhook', 'jp-bbpress-slack' ),
			__( ' Create a new webhook', 'jp-bbpress-slack' ),
			__( 'Set a channel to receive the notifications', 'jp-bbpress-slack' ),
			__( 'Copy the URL for the webhook	', 'jp-bbpress-slack' ),
			__( 'Past the URL into the field below and click submit', 'jp-bbpress-slack' ),
		);

		return $header. "<ol><li>" .implode( "</li><li>", $instructions ) . "</li></ol>";

	}

}
