# WP Options Page

A class to build options pages for your WordPress plugins and themes.

## Documentation

Learn more in our [wiki](https://github.com/luizbills/wp-options-page/wiki).

## Install

Install using composer:

```
composer require luizbills/wp-options-page
```

## Getting Started

Just create an `WP_Options_Page` class instance on `init` action hook:

```php
function yourprefix_create_settings_page () {
	$page = new WP_Options_Page();

	// give your page a ID
	$page->id = 'my_settings_page';

	// set the menu name
	$page->menu_title = 'My Settings';

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
	// example: My_Settings_Page::instance()->get_option( 'api_key' );
	private static $instance = null;
	public static function instance () {
		if ( ! self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	private function __construct () {
		add_action( 'init', [ $this, 'init' ] );
	}

	// overrides the `init` method to setup your page
	public function init () {
		// give your page a ID
		$this->id = 'my_settings_page';

		// set the menu name
		$this->menu_title = 'My Settings';

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

// start your class
My_Settings_Page::instance();
```

Preview:

![](https://user-images.githubusercontent.com/1798830/215272911-9a90f0fd-d62d-49f4-bc64-7906f513695a.png)

---

Also, you can install and study our [Demo Plugin](https://github.com/luizbills/wp-options-page-demo).

## LICENSE

GPLv2 or later
