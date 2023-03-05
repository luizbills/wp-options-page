<?php

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
}
add_action( 'init', 'yourprefix_create_settings_page' );
