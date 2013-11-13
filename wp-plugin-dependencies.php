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

			add_action( 'admin_head', array( $this, 'admin_head' ) );
			add_action( 'after_plugin_row', array( $this, 'after_plugin_row' ) );
		}

		function admin_head() {
			echo '<style>
				.plugins tr.dependencies .dependencies-active, .plugins tr.dependencies .active a { color: #669900; }
				.plugins tr.dependencies .dependencies-required, .plugins tr.dependencies .required a { color: #C00; }
				.js .plugins tr.dependencies .required, .plugins tr.dependencies.dependencies-active { display: none; }
				</style>';
			echo "<script>
				jQuery(function($) {
					$('.plugins tr.dependencies-active').each(function(){
						var id = $(this).attr('id');
						$(this).prev().find('.row-actions').append(' | <a href=\"#' + id + '\" class=\"dependencies-toggle\" data-toggle-text=\"Hide Dependencies\">Show Dependencies</a>');
					});
					$('a.dependencies-toggle').on('click', function(e){
						var href = $(this).attr('href');
						var text = $(this).text();
						var toggle_text = $(this).attr('data-toggle-text');
						$(href).toggle();
						$(this).attr('data-toggle-text', text).text(toggle_text);
						e.preventDefault();
					});
				});
				</script>";
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
