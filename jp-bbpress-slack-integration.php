<?php
/**
 * Plugin Name: JP bbPress Slack Integration
 * Description: Send notifications of new bbPress topics and replies to a Slack channel.
 * Author: Josh Pollock
 * Author URI: http://joshpress.net
 * Version: 0.1.0
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
