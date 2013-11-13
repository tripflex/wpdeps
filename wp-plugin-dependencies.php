<?php

/*
WP Plugin Dependencies
Helper class for plugins that depend on other plugins being installed.

Author: Ben Huson
Version: 0.1

Usage Example:

<?php
// Require this file
require_once( 'wp-plugin-dependencies/wp-plugin-dependencies.php' );

// Set Dependencies
$dependencies = new WPPluginDependencies( __FILE__, array(
	'multiple-post-thumbnails/multi-post-thumbnails.php' => array(
		'name' => 'Multiple Post Thumbnails',
		'url'  => 'http://wordpress.org/plugins/multiple-post-thumbnails/'
	)
) );
?>

*/

if ( ! class_exists( 'WPPluginDependencies' ) ) {

	class WPPluginDependencies {

		private $plugin = '';
		private $dependencies = array();

		function __construct( $plugin, $dependencies = null ) {
			$this->plugin = plugin_basename( $plugin );
			$this->dependencies = $dependencies;

			add_action( 'admin_enqueue_scripts', array( $this, '_enqueue_styles_and_scripts' ) );
			add_action( 'after_plugin_row', array( $this, 'after_plugin_row' ) );
		}

		/**
		 * Enqueue Styles and Scripts
		 *
		 * Loads stylesheets and Javascript files on the plugins admin page.
		 *
		 * @param  string  $hook  Current page hook.
		 */
		function _enqueue_styles_and_scripts( $hook ) {
			if ( 'plugins.php' == $hook ) {
				wp_enqueue_script( 'wp-plugin-dependencies', plugins_url( '/js/wp-plugin-dependencies.js', __FILE__ ), array( 'jquery' ), '0.1' );
				wp_enqueue_style( 'wp-plugin-dependencies', plugins_url( '/css/wp-plugin-dependencies.css', __FILE__ ), null, '0.1' );
			}
		}

		/**
		 * After Plugin Row
		 *
		 * This function can be used to insert text after the WP Geo plugin row on the plugins page.
		 * Useful if you need to tell people something important before they upgrade.
		 *
		 * @param  string  $plugin  Plugin reference.
		 */
		function after_plugin_row( $plugin ) {
			if ( count( $this->dependencies ) > 0 && $this->plugin == $plugin ) {
				$plugin_data = get_plugin_data( trailingslashit( WP_PLUGIN_DIR ) . $this->plugin );
				$dependencies_active_class = $this->check_all_dependencies_active() ? ' dependencies-active' : '';
				echo '<tr id="' . sanitize_title( $plugin_data['Name'] ) . '-dependencies" class="active dependencies' . $dependencies_active_class . '">
						<th scope="row" class="check-column"></th>
						<td class="plugin-title">Plugin Dependencies:</td>
						<td class="column-description desc">
							<div class="second">';
				$counter = 0;
				foreach ( $this->dependencies as $basename => $data ) {
					$class = 'dependencies-required';
					if ( is_plugin_active( $basename ) ) {
						$class = 'dependencies-active';
					}
					if ( $counter > 0 )
						echo ' | ';
					$display = $data['name'];
					if ( ! empty( $data['url'] ) && ! is_plugin_active( $basename ) )
						$display = '<a href="' . $data['url'] . '" target="_blank">' . $display . '</a>';
					echo '<span class="' . $class . '">' . $display . '</span>';
					$counter++;
				}
				echo '		</div>
						</td>
					</tr>';
			}
		}

		function check_all_dependencies_active() {
			foreach ( $this->dependencies as $basename => $data ) {
				if ( ! is_plugin_active( $basename ) )
					return false;
			}
			return true;
		}

	}

}
