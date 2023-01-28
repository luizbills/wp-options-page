# WP Options Page

A class to build options pages for your WordPress plugins and themes.

## Install

Install using composer:

```
composer require luizbills/wp-options-page
```

Or just copy [`src/class-wp-options-page.php`](/src/class-wp-options-page.php) to your project and include in your code:

```php
require 'path/to/your/class-wp-options-page.php';
```

## Getting Started

Just create an `WP_Options_Page` class instance on `init` action hook:

```php
function yourprefix_create_settings_page () {
	$page = new WP_Options_Page();

	// give your page a ID
	$page->id = 'my_settings_page';

	// set the menu name
	$page->menu_title = 'Settings';

	// register your options fields
	$page->fields = [
		// a simple text input field
		[
			'id' => 'api_key',
			'title' => 'API Key',
			'type' => 'text',
		]
	];

	// register the page
	$page->init();

	// access the stored options
	$api_key = $page->get_option( 'api_key' );
	
	// store this page in a global object or variable
	// So you can easily your instance class later
	// example: My_Plugin->settings = $page;
}
add_action( 'init', 'yourprefix_create_settings_page' );
```

Or create your own derived class:

```php
class My_Settings_Page extends WP_Options_Page {

	// I recommend using Singleton pattern
	// So you can easily retrieve the class later
	// example: My_Settings_Page::get_instance()->get_option( 'api_key' );
	private static $instance = null;
	public function get_instance () {
		if ( ! self::$instance ) self::$instance = new self();
		return self::$instance;
	}
	
	public function __construct () {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init () {
		// give your page a ID
		$this->id = 'my_settings_page';

		// set the menu name
		$this->menu_title = 'Settings';

		// register the page
		parent::init();
	}

	// overrides the `get_fields` method to register your fields
	public function get_fields () {
		return [
			[
				'id' => 'api_key',
				'title' => 'API Key',
				'type' => 'text',
			]
		];
	}
}
```

## Documentation

Learn more in our [wiki](https://github.dev/luizbills/wp-options-page/wiki).

## LICENSE

GPLv2 or later
