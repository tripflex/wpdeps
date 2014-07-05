wp-plugin-dependencies
======================

A framework to help build WordPress plugins which need to check for other plugin dependencies.

= Usage

```php
// Require this file
require_once( 'wp-plugin-dependencies/wp-plugin-dependencies.php' );

// Set Dependencies
$dependencies = new WPPluginDependencies( __FILE__, array(
	'multiple-post-thumbnails/multi-post-thumbnails.php' => array(
		'name' => 'Multiple Post Thumbnails',
		'url'  => 'http://wordpress.org/plugins/multiple-post-thumbnails/'
	)
) );
```
