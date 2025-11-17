<?php
declare(strict_types=1);

/*
 * Plugin Name: Remove Yoast SEO Comments
 * Plugin URI: https://wordpress.org/plugins/remove-yoast-seo-comments/
 * Description: Removes the Yoast SEO advertisement HTML comments from your front-end source code.
 * Version: 3.2
 * Requires PHP: 8.0
 * Requires at least: 4.0
 * Tested up to: 6.7
 * Tested up to ClassicPress: 2.2
 * Author: Mitch
 * Author URI: https://profiles.wordpress.org/lowest
 * License: GPL-2.0+
 * Text Domain: rysc
 * Domain Path: /languages
 * Network: false
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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

if ( ! defined( 'ABSPATH' ) ) { exit; }

class RYSC {
	private string $version = '3.2';
	private bool $debug_marker_removed = false;
	private bool $head_marker_removed = false;
	private bool $backup_plan_active = false;

	public function __construct() {
		add_action( 'init', array( $this, 'bundle' ), 1 );
	}

	public function bundle(): void {
		if ( defined( 'WPSEO_VERSION' ) ) {
			$debug_marker = ( version_compare( WPSEO_VERSION, '4.4', '>=' ) ) ? 'debug_mark' : 'debug_marker';

			// Main function to unhook the debug message
			if ( class_exists( 'WPSEO_Frontend' ) && method_exists( 'WPSEO_Frontend', $debug_marker ) ) {
				remove_action( 'wpseo_head', array( WPSEO_Frontend::get_instance(), $debug_marker ), 2 );

				$this->debug_marker_removed = true;

				// Also removes the end debug message as of Yoast SEO 5.9
				if ( version_compare( WPSEO_VERSION, '5.9', '>=' ) ) {
					$this->head_marker_removed = true;
				}
			}

			// Compatible solution for everything below Yoast SEO 5.8 - uses output buffering instead of unsafe eval()
			if ( class_exists( 'WPSEO_Frontend' ) && method_exists( 'WPSEO_Frontend', 'head' ) && version_compare( WPSEO_VERSION, '5.8', '<' ) ) {
				add_action( 'get_header', array( $this, 'buffer_header' ) );
				add_action( 'wp_head', array( $this, 'buffer_head' ), 999 );
				$this->head_marker_removed = true;
			}

			// Temporary solution for all installations on Yoast SEO 5.8
			if ( version_compare( WPSEO_VERSION, '5.8', '==' ) ) {
				add_action( 'get_header', array( $this, 'buffer_header' ) );
				add_action( 'wp_head', array( $this, 'buffer_head' ), 999 );
				$this->head_marker_removed = true;
			}

			// Backup solution for partial support
			if ( $this->operating_status() === 2 ) {
				add_action( 'get_header', array( $this, 'buffer_header' ) );
				add_action( 'wp_head', array( $this, 'buffer_head' ), 999 );
			}

			if ( current_user_can( 'manage_options' ) ) {
				add_action( 'wp_dashboard_setup', array( $this, 'dash_widget' ) );
			}
		}

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );
	}
	
	public function operating_status(): int {
		if ( $this->debug_marker_removed && $this->head_marker_removed ) {
			return 1; // Fully supported
		} elseif ( ( ! $this->debug_marker_removed && $this->head_marker_removed ) || ( $this->debug_marker_removed && ! $this->head_marker_removed ) ) {
			return 2; // Partially supported
		} else {
			return 3; // Not supported
		}
	}
	
	public function dash_widget(): void {
		wp_add_dashboard_widget( 'dashboard_widget', __( 'Remove Yoast SEO Comments', 'rysc' ), array( $this, 'dash_widget_content' ) );
	}

	public function dash_widget_content(): void {
		$status_code = $this->operating_status();

		if ( $status_code === 1 ) {
			$status = '<span style="color:#04B404;font-weight:bold">' . esc_html__( 'Fully supported', 'rysc' ) . '</span>';
			/* translators: 1: Yoast SEO version, 2: RYSC version */
			$content = '<p>' . sprintf( esc_html__( 'Version %1$s of Yoast SEO is fully supported by RYSC %2$s. The HTML comments have been removed from your front-end source code.', 'rysc' ), esc_html( WPSEO_VERSION ), esc_html( $this->version ) ) . '</p>';
		} elseif ( $status_code === 2 ) {
			$status = '<span style="color:#FF8000;font-weight:bold">' . esc_html__( 'Partially supported', 'rysc' ) . '</span>';
			/* translators: 1: Yoast SEO version, 2: RYSC version */
			$content = '<p>' . sprintf( esc_html__( 'Version %1$s of Yoast SEO is not properly supported by RYSC %2$s. Some functions are not working. A backup solution has been enabled to keep the HTML comments removed.', 'rysc' ), esc_html( WPSEO_VERSION ), esc_html( $this->version ) ) . '</p>';
		} else {
			$status = '<span style="color:#DF0101;font-weight:bold">' . esc_html__( 'Not supported', 'rysc' ) . '</span>';
			/* translators: 1: Yoast SEO version, 2: RYSC version */
			$content = '<p>' . sprintf( esc_html__( 'Version %1$s of Yoast SEO is not supported by RYSC %2$s. A backup solution has been enabled to keep the HTML comments removed.', 'rysc' ), esc_html( WPSEO_VERSION ), esc_html( $this->version ) ) . '</p>';
		}

		echo '<div class="activity-block"><h3><span class="dashicons dashicons-admin-plugins"></span> ' . sprintf( esc_html__( 'Yoast SEO %s Compatibility Status: %s', 'rysc' ), esc_html( WPSEO_VERSION ), $status ) . '</h3></div>';
		echo wp_kses_post( $content );
	}
	
	/**
	 * Start output buffering to remove Yoast SEO comments.
	 *
	 * This is a safe, reliable method that works across all Yoast SEO versions.
	 * Replaces the previous unsafe eval() approach.
	 *
	 * @since 3.2
	 * @return void
	 */
	public function buffer_header(): void {
		ob_start( function ( $output ) {
			// Remove all Yoast SEO HTML comments from the output
			return preg_replace( '/\n?<.*?yoast.*?>/mi', '', $output );
		});
	}

	/**
	 * End output buffering and flush the cleaned content.
	 *
	 * @since 3.2
	 * @return void
	 */
	public function buffer_head(): void {
		ob_end_flush();
	}
	
	/**
	 * Add plugin action links to the plugins page.
	 *
	 * @since 3.2
	 * @param array $link Existing plugin action links.
	 * @return array Modified plugin action links.
	 */
	public function plugin_links( array $link ): array {
		$donate_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2VYPRGME8QELC';
		$plugin_links = array_merge(
			$link,
			array(
				'<a href="' . esc_url( $donate_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Donate', 'rysc' ) . '</a>',
			)
		);

		return $plugin_links;
	}
}

// Initialize the plugin
new RYSC();