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
		];
	];

	// register the page
	$page->init();

	// access the stored options
	$api_key $page->get_option( 'api_key' );
}
add_action( 'init', 'yourprefix_create_settings_page' );


```

Or create your own derived class:

```php
class My_Settings_Page extends WP_Options_Page {
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

	public function get_fields () {
		return [
			// a simple text input field
			[
				'id' => 'api_key',
				'title' => 'API Key',
				'type' => 'text',
			];
		];
	}
}
```

## Documentation

Learn more in our [wiki](https://github.dev/luizbills/wp-options-page/wiki).

## LICENSE

GPLv2 or later
