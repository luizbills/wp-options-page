<?php

if ( ! defined( 'WPINC' ) ) die();

if ( class_exists( 'WP_Options_Page' ) ) return;

/**
 * WP_Options_Page class
 *
 * @package WP_Options_Page
 * @author Luiz Bills <luizbills@pm.me>
 * @version 0.3.0
 * @see https://github.com/luizbills/wp-options-page
 */
class WP_Options_Page {
	/**
	 * The ID (also the slug) of the page. Should be unique for this menu page and only include lowercase alphanumeric, dashes, and underscores characters to be compatible with `sanitize_key()`.
	 *
	 * @since 0.1.0
	 * @see https://developer.wordpress.org/reference/functions/sanitize_key/
	 * @var string
	 */
	public $id = null;

	/**
	 * The slug name for the parent menu (or the file name of a standard WordPress admin page).
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $menu_parent = null;

	/**
	 * The text to be displayed in the <title> tag of the page when the menu is selected.
	 * This text is also used in a <h1> tag at the top of the page, if the $insert_title is equal to `true`.
	 *
	 * @since 0.1.0
	 * @see WP_Options_Page::$insert_title
	 * @var string
	 */
	public $page_title = null;

	/**
	 * A text that appears immediately after the <h1> title of the page, if the $insert_title is equal to `true`.
	 *
	 * @since 0.1.0
	 * @see WP_Options_Page::$insert_title
	 * @var string
	 */
	public $page_description = null;

	/**
	 * If enabled, inserts a <h1> tag title and description at the top of the page.
	 *
	 * @since 0.1.0
	 * @see WP_Options_Page::$page_description
	 * @see WP_Options_Page::$page_title
	 * @var string
	 */
	public $insert_title = true;

	/**
	 * The text to be used for the menu.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $menu_title = null;

	/**
	 * The position in the menu order this item should appear.
	 *
	 * @since 0.1.0
	 * @see https://developer.wordpress.org/reference/functions/add_menu_page/#parameters
	 * @var int|float
	 */
	public $menu_position = null;

	/**
	 * The hook priority used if this page is a subpage.
	 *
	 * @since 0.1.0
	 * @var int|float
	 */
	public $menu_priority = 10;

	/**
	 * The URL to the icon to be used for this menu.
	 *   - Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme. This should begin with 'data:image/svg+xml;base64,'.
     *   - Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'.
     *   - Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
	 *
	 * @since 0.1.0
	 * @see https://developer.wordpress.org/reference/functions/add_menu_page/#parameters
	 * @var string
	 */
	public $menu_icon = 'dashicons-admin-generic';

	/**
	 * The capability (or user role) required for this menu to be displayed to the user.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $capability = 'manage_options';

	/**
	 * The option name where all options of the page will be stored.
	 * By default is `"{$this->id}_options"`.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $option_name = null;

	/**
	 * The prefix appended in all field name attributes.
	 * By default is `"{$this->id}_"`.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $field_prefix = null;

	/**
	 * The prefix appended in all hooks triggered by the page.
	 * By default is `"{$this->field_prefix}_"`.
	 *
	 * @since 0.1.0
	 * @see WP_Options_Page::add_action()
	 * @see WP_Options_Page::add_filter()
	 * @var string
	 */
	protected $hook_prefix = null;

	/**
	 * The page's hook_suffix returned by `add_menu_page()` or `add_submenu_page()`.
	 *
	 * @since 0.1.0
	 * @see WP_Options_Page::get_hook_suffix()
	 * @see https://developer.wordpress.org/reference/functions/add_menu_page/#return
	 * @var string
	 */
	protected $hook_suffix = null;

	/**
	 * The fields of the page.
	 *
	 * @since 0.1.0
	 * @see WP_Options_Page::init_fields()
	 * @var array
	 */
	public $fields = null;

	/**
	 * Array with some strings that are used on the page. You can overwrite them to change or make them translatable.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $strings = [];

	/**
	 * @since 0.1.0
	 * @see WP_Options_Page::add_notice()
	 * @var array
	 */
	protected $admin_notices = [];

	/**
	 * A flag used during the page rendering process.
	 *
	 * @since 0.1.0
	 * @see WP_Options_Page::render_field()
	 * @see WP_Options_Page::maybe_open_or_close_table()
	 * @var bool
	 */
	protected $table_is_open = null;

	/**
	 * @since 0.1.0
	 * @see WP_Options_Page::add_script()
	 * @var array
	 */
	protected $scripts = [];

	/**
	 * @since 0.1.0
	 * @see WP_Options_Page::add_style()
	 * @var array
	 */
	protected $styles = [];

	/**
	 * The default value of each field of the page.
	 *
	 * @since 0.1.0
	 * @see WP_Options_Page::init_fields()
	 * @var array
	 */
	protected $default_values = [];

	/**
	 * List of supported features.
	 * The intention is that it will be used by other developers to choose whether or not to activate a feature.
	 *
	 * @since 0.3.0
	 * @var array
	 */
	public $supports = [];

	/**
	 * @return void
	 */
	public function init () {
		if ( ! did_action( 'init' ) ) {
			throw new \Exception( 'Please, don\'t use the ' . get_class( $this ) . ' class before "init" hook.' );
		}
		if ( ! $this->id ) {
			throw new \Exception( 'Missing $id in ' . get_class( $this ) );
		}

		$this->menu_title = $this->menu_title ?? $this->id;
		$this->page_title = $this->page_title ?? $this->menu_title;
		$this->option_name = $this->option_name ?? $this->id . '_options';
		$this->field_prefix = $this->field_prefix ?? $this->id . '_';
		$this->hook_prefix = $this->hook_prefix ?? $this->field_prefix;

		$this->strings = array_merge(
			[
				'notice_error' => '<strong>Error</strong>: %s',
				'checkbox_enable' => 'Enable',
				'options_updated' => '<strong>' . __( 'Settings saved.' ) . '</strong>',
			],
			$this->strings
		);

		$this->init_hooks();
		$this->init_fields();
		$this->handle_options();

		\do_action( 'wp_options_page_init', $this );
	}

	/**
	 * @return void
	 */
	protected function init_hooks () {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_menu', [ $this, 'add_menu_page' ], $this->menu_priority );
	}

	/**
	 * @return void
	 */
	public function init_fields () {
		$this->fields = apply_filters(
			$this->hook_prefix . 'get_fields',
			$this->fields ? $this->fields : $this->get_fields(),
			$this
		);

		foreach ( $this->fields as $field ) {
			$id = $field['id'] ?? false;
			if ( $id ) $this->default_values[ $id ] = $field['default'] ?? null;
		}

		if ( $this->insert_title ) {
			$primary_title = apply_filters(
				$this->hook_prefix . 'top_page_title',
				[
					'type' => 'title',
					'title' => $this->page_title,
					'description' => $this->page_description,
					'class' => '',
				],
				$this
			);
			if ( $primary_title ) {
				array_unshift( $this->fields, $primary_title );
			}
		}

		$has_submit = false;
		foreach ( $this->fields as $key => $field ) {
			$this->fields[ $key ] = $this->prepare_field( $field );
			if ( ! $has_submit && 'submit' === $field['type'] ) $has_submit = true;
		}

		if ( ! $has_submit ) {
			$this->fields[] = $this->prepare_field( [
				'type' => 'submit'
			] );
		}
	}

	/**
	 * @return void
	 */
	public function add_menu_page () {
		if ( ! $this->menu_parent ) {
			$this->hook_suffix = \add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->id,
				[ $this, 'render_page' ],
				$this->menu_icon,
				$this->menu_position
			);
		} else {
			$this->hook_suffix = \add_submenu_page(
				$this->menu_parent,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->id,
				[ $this, 'render_page' ],
				$this->menu_position
			);
		}

		$this->do_action( 'admin_menu', $this, $this->hook_suffix );
	}

	/**
	 * @since 0.3.0
	 * @return string
	 */
	public function get_hook_suffix () {
		return $this->hook_suffix;
	}

	/**
	 * @return array
	 */
	public function get_fields () {
		return [
			[
				'title' => $this->page_title,
				'description' => 'Overrides the <code>' . __METHOD__ . '</code> to display your settings fields',
				'type' => 'title',
			],
		];
	}

	/**
	 * @param array $field
	 * @return mixed
	 */
	public function get_field_value ( $field ) {
		if ( isset( $field['value'] ) ) {
			return $field['value'];
		}
		return apply_filters(
			$this->hook_prefix . 'get_field_value',
			$this->get_option( $field['id'] ),
			$field,
			$this
		);
	}

	/**
	 * @param string $field_id
	 * @return mixed
	 */
	public function get_field_default_value ( $field_id ) {
		return apply_filters(
			$this->hook_prefix . 'get_field_default_value',
			$this->default_values[ $field_id ] ?? false,
			$field_id,
			$this
		);
	}

	/**
	 * @param string $field_id
	 * @return mixed
	 */
	public function get_option ( $field_id ) {
		$default = $this->get_field_default_value( $field_id );
		$options = get_option( $this->option_name, [] );
		return $options[ $field_id ] ?? $default;
	}

	/**
	 * @param array $field
	 * @return string
	 */
	public function get_field_name ( $field ) {
		return $this->field_prefix . $field['id'];
	}

	/**
	 * @param string $handle
	 * @param string $src
	 * @param string[] $deps
	 * @param string|bool|null $ver
	 * @param bool $in_footer
	 * @return void
	*/
	public function add_script ( $handle, $src = '', $deps = [], $ver = false, $in_footer = false ) {
		$this->scripts[] = [ $handle, $src, $deps, $ver, $in_footer ];
	}

	/**
	 * @param string $handle
	 * @param string $src
	 * @param string[] $deps
	 * @param string|bool|null $ver = false
	 * @param string $media
	 * @return void
	*/
	public function add_style ( $handle, $src = '', $deps = [], $ver = false, $media = 'all' ) {
		$this->styles[] = [ $handle, $src, $deps, $ver, $media ];
	}

	/**
	 * @return string
	 */
	public function get_nonce_action () {
		return $this->field_prefix . 'nonce';
	}

	/**
	 * @return string
	 */
	public function get_nonce_name () {
		return '_nonce';
	}

	/**
	 * @param string $message
	 * @param string $type Should be "error", "success", "warning" or "info".
	 * @param string $class
	 * @return void
	 */
	public function add_notice ( $message, $type = 'error', $class = '' ) {
		$this->admin_notices[] = [
			'message' => $message,
			'type' => $type,
			'class' => $class,
		];
	}

	/**
	 * @param array $field
	 * @return array
	 */
	protected function prepare_field ( $field ) {
		$defaults = [
			'id' => null,
			'type' => 'text',
			'title' => null,
			'title_icon' => null,
			'description' => '',
			'options' => [],
			'default' => '',
			'__sanitize' => null,
			'__validate' => null,
			'__is_input' => true,
		];
		$field = array_merge( $defaults, $field );
		$field['name'] = $this->get_field_name( $field );

		switch ( $field['type'] ) {
			case 'title':
			case 'subtitle':
			case 'submit':
				$field['__is_input'] = false;
				break;
			case 'textarea':
				$field['__sanitize'] = 'sanitize_textarea_field';
				break;
			default:
				$field['__sanitize'] = 'sanitize_text_field';
				break;
		}

		return apply_filters( $this->hook_prefix . 'prepare_field', $field );
	}

	/**
	 * @param string $hook_suffix
	 * @return void
	 */
	public function enqueue_scripts ( $hook_suffix ) {
		if ( $this->hook_suffix !== $hook_suffix ) return;
		foreach ( $this->scripts as $params ) {
			call_user_func_array( 'wp_enqueue_script', $params );
		}
		foreach ( $this->styles as $params ) {
			call_user_func_array( 'wp_enqueue_style', $params );
		}
	}

	/**
	 * @return void
	 */
	public function handle_options () {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) return;
		if ( ! is_admin() ) return;
		if ( $this->id !== ( $_REQUEST['page'] ?? '' ) ) return;

		$nonce = $_REQUEST[ $this->get_nonce_name() ] ?? '';
		$action = $this->get_nonce_action();
		$invalid_nonce = ! wp_verify_nonce( $nonce, $action );
		$invalid_user = ! current_user_can( $this->capability );
		if ( $invalid_nonce || $invalid_user ) {
			wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
		}

		$options = [];

		foreach ( $this->fields as &$field ) {
			// skip fields that it's has input
			if ( ! $field['__is_input'] ) continue;

			$name = $field['name'];
			$value = $_POST[ $name ] ?? '';

			// maybe validate
			if ( $field['__validate'] ) {
				$error = false;
				try {
					$field['__validate']( $value, $field );
					$this->do_action( 'validate_field_' . $field['type'], $field, $this );
				} catch ( \Throwable $e ) {
					$error = $e->getMessage();
					// avoid to display messages from empty exceptions
					$message = $error ? $this->format_error_message( $error, $field ) : false;
					if ( $message ) {
						$this->add_notice( $message, 'error', 'field-' . $name );
					}
					$field['error'] = $error;
					$field['value'] = $value;
					continue;
				}
			}

			// maybe sanitize
			$sanitize = $field['__sanitize'] ?? '';
			if ( $sanitize ) {
				if ( is_scalar( $value ) ) {
					$value = $sanitize( $value );
				} else {
					$value = maybe_unserialize( $sanitize( serialize( $value ) ) );
				}
			}

			$field['value'] = $value;
			$field['error'] = null;
			$options[ $name ] = [
				'id' => $field['id'],
				'value' => $value
			];
		}

		$options = apply_filters( $this->hook_prefix . 'updated_options', $options, $this );

		if ( count( $options ) > 0 ) {
			$updated = $this->update_options( $options );

			$this->add_notice(
				$this->strings['options_updated'],
				'success',
				'is-dismissible ' . ( $updated ? 'options-updated' : '' ),
			);
		}
	}

	/**
	 * @param string $error
	 * @param array $field
	 * @return string
	 */
	public function format_error_message ( $error, $field ) {
		return sprintf(
			$this->strings['notice_error'],
			$error
		);
	}

	/**
	 * @param array $options
	 * @return bool
	 */
	public function update_options ( $options ) {
		$old = get_option( $this->option_name );
		$values = is_array( $old ) ? $old : [];
		foreach ( $options as $data ) {
			$values[ $data['id'] ] = $data['value'];
		}
		return update_option( $this->option_name, $values );
	}

	/**
	 * @return string
	 */
	public function get_url () {
		$path = 'admin.php';
		// woocommerce submenu support
		if ( $this->menu_parent && 'woocommerce' !== $this->menu_parent ) {
			$path = $this->menu_parent;
		}
		return admin_url( $path . '?page=' . $this->id );
	}

	/**
	 * @param string $hook_name
	 * @param callable|string|array $callback
	 * @param integer $priority
	 * @param integer $args
	 * @return bool
	 */
	public function add_action ( $hook_name, $callback, $priority = 10, $args = 1 ) {
		return \add_filter( $this->hook_prefix . $hook_name, $callback, $priority, $args );
	}

	/**
	 * @param string $hook_name
	 * @param callable|string|array $callback
	 * @param integer $priority
	 * @param integer $args
	 * @return bool
	 */
	public function add_filter ( $hook_name, $callback, $priority = 10, $args = 1 ) {
		return \add_filter( $this->hook_prefix . $hook_name, $callback, $priority, $args );
	}

	/**
	 * @param string $action
	 * @param mixed ...$arg
	 * @return void
	 */
	public function do_action ( $action, ...$arg ) {
		\do_action( $this->hook_prefix . $action, ...$arg );
	}

	/**
	 * @return void
	 */
	public function render_page () {
		?>
		<div class="wrap">
			<?php $this->do_action( 'before_render_form', $this ); ?>

			<form method="post" action="<?= esc_attr( remove_query_arg( '_wp_http_referer' ) ) ?>" novalidate="novalidate">
				<?php $this->render_notices() ?>
				<?php $this->render_nonce() ?>
				<?php $this->render_all_fields() ?>
			</form>

			<?php $this->do_action( 'after_render_form', $this ); ?>
		</div>
		<?php
	}

	/**
	 * @return void
	 */
	protected function render_notices () {
		foreach ( $this->admin_notices as $notice ) {
			$message = $notice['message'] ?? '';
			$type = $notice['type'] ?? 'error';
			$class = $notice['class'] ?? '';
			$page_class = 'options-page-' . $this->id;
			printf(
				'<div class="%s notice notice-%s %s"><p>%s</p></div>',
				esc_attr( $page_class ),
				esc_attr( $type ),
				esc_attr( $class ),
				$message
			);
		}
	}

	/**
	 * @return void
	 */
	protected function render_nonce () {
		wp_nonce_field(
			$this->get_nonce_action(),
			$this->get_nonce_name(),
		);
	}

	/**
	 * @return void
	 */
	protected function render_all_fields () {
		$this->table_is_open = false;
		foreach ( $this->fields as $field ) {
			$this->render_field( $field );
		}
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function maybe_open_or_close_table ( $field ) {
		$is_input = $field['__is_input'];
		if ( ! $this->table_is_open ) {
			if ( $is_input ) {
				echo '<table class="form-table" role="presentation">';
				$this->table_is_open = true;
			}
		} elseif ( ! $is_input ) {
			echo '</table>';
			$this->table_is_open = false;
		}
	}

	/**
	 * @param string $icon
	 * @return string
	 */
	protected function get_icon ( $icon ) {
		$icon = esc_attr( trim( $icon ) );
		if ( 0 === strpos( $icon, 'dashicons-' ) ) {
			return " <span class=\"dashicons $icon\" aria-hidden=\"true\"></span>";
		}
		if ( 0 === strpos( $icon, 'data:image/' ) || 0 === strpos( $icon, 'https://' ) ) {
			return " <img src=\"$icon\" aria-hidden=\"true\">";
		}
		if ( $icon ) {
			return " <span class=\"$icon\" aria-hidden=\"true\"></span>";
		}
		return '';
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field ( $field ) {
		$type = $field['type'];
		$method = 'render_field_' . $type;
		$this->maybe_open_or_close_table( $field );
		$this->do_action( 'before_render_field', $field, $this );
		if ( method_exists( $this, $method ) ) {
			$this->$method( $field );
		} else {
			ob_start();
			$this->do_action( 'render_field_'  . $type, $field, $this );
			$output = ob_get_clean();
			if ( $output ) {
				echo $output;
			} else {
				throw new Exception( "Invalid field type \"{$field['type']}\" in " . get_class( $this ) );
			}
		}
		$this->do_action( 'after_render_field', $field, $this );
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function open_wrapper ( $field ) {
		$id = $field['id'];
		$name = $field['name'];
		$title = $field['title'] ?? $id;
		$icon = $this->get_icon( $field['title_icon'] );
		?>
		<tr>
			<th scope="row">
				<label for="<?= esc_attr( $name ); ?>"><?= esc_html( $title ) . $icon ?></label>
			</th>
			<td>
		<?php
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function close_wrapper ( $field ) {
		?>
			</td>
		</tr>
		<?php
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field_text ( $field ) {
		$id = $field['id'];
		$name = $field['name'];
		$value = $this->get_field_value( $field );
		$class = $field['class'] ?? 'regular-text';
		$type = $field['input_type'] ?? 'text';
		$placeholder = $field['placeholder'] ?? '';
		$desc = $field['description'];
		$describedby = $desc ? 'aria-describedby="' . esc_attr( $id ) . '-description"' : '';

		$this->open_wrapper( $field );
		?>

		<input name="<?= esc_attr( $name ); ?>" type="<?= esc_attr( $type ) ?>" id="<?= esc_attr( $name ); ?>" <?= $describedby ?> value="<?= esc_attr( $value ); ?>" class="<?= esc_attr( $class ); ?>" placeholder="<?= esc_attr( $placeholder ) ?>">

		<?php $this->do_action( 'after_field_input', $field ); ?>

		<?php if ( $desc ) : ?>
		<p class="description" id="<?= esc_attr( $name ); ?>-description"><?= $desc ?></p>
		<?php endif ?>

		<?php $this->close_wrapper( $field );
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field_textarea ( $field ) {
		$id = $field['id'];
		$name = $field['name'];
		$value = $this->get_field_value( $field );
		$class = $field['class'] ?? 'large-text';
		$placeholder = $field['placeholder'] ?? '';
		$desc = $field['description'];
		$describedby = $desc ? 'aria-describedby="' . esc_attr( $id ) . '-description"' : '';
		$rows = $field['rows'] ?? 5;

		$this->open_wrapper( $field );
		?>

		<textarea name="<?= esc_attr( $name ); ?>" id="<?= esc_attr( $name ); ?>" <?= $describedby ?> class="<?= esc_attr( $class ); ?>" placeholder="<?= esc_attr( $placeholder ) ?>" rows="<?= esc_attr( $rows ) ?>"><?= esc_html( $value ); ?></textarea>

		<?php $this->do_action( 'after_field_input', $field ); ?>

		<?php if ( $desc ) : ?>
		<p class="description" id="<?= esc_attr( $name ); ?>-description"><?= $desc ?></p>
		<?php endif; ?>

		<?php $this->close_wrapper( $field );
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field_select ( $field ) {
		$id = $field['id'];
		$name = $field['name'];
		$value = $this->get_field_value( $field );
		$class = $field['class'] ?? '';
		$desc = $field['description'] ?? false;
		$describedby = $desc ? 'aria-describedby="' . esc_attr( $id ) . '-description"' : '';

		$this->open_wrapper( $field );
		?>

		<select name="<?= esc_attr( $name ); ?>" id="<?= esc_attr( $name ); ?>" <?= $describedby ?> class="<?= esc_attr( $class ); ?>">
			<?php foreach ( $field['options'] as $opt_value => $opt_label ) : ?>
				<option value="<?= esc_attr( $opt_value ); ?>" <?php selected( $opt_value, $value ) ?>><?= esc_html( $opt_label ) ?></option>
			<?php endforeach; ?>
		</select>

		<?php $this->do_action( 'after_field_input', $field ); ?>

		<?php if ( $desc ) : ?>
		<p class="description" id="<?= esc_attr( $id ); ?>-description"><?= $desc ?></p>
		<?php endif ?>

		<?php $this->close_wrapper( $field );
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field_radio ( $field ) {
		$id = $field['id'];
		$name = $field['name'];
		$title = $field['title'] ?? $id;
		$desc = $field['description'];
		$options = $field['options'];
		$value = $this->get_field_value( $field );

		$this->open_wrapper( $field );
		?>

		<fieldset>
			<legend class="screen-reader-text"><span><?= esc_html( strip_tags( $title ) ); ?></span></legend>

			<?php foreach ( $options as $key => $label ) :
				$option_id = esc_attr( $id . '_' . $key ); ?>
				<label for="<?= $option_id ?>">
					<input name="<?= esc_attr( $name ) ?>" type="radio" id="<?= esc_attr( $option_id ) ?>" value="<?= esc_attr( $key ) ?>" <?php checked( $key, $value ); ?>>
					<?= $label ?>
				</label>
				<br>
			<?php endforeach ?>

			<?php $this->do_action( 'after_field_input', $field ); ?>

			<?php if ( $desc ) : ?>
			<p class="description"><?= $desc ?></p>
			<?php endif ?>
		</fieldset>

		<?php $this->close_wrapper( $field );
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field_checkbox ( $field ) {
		$id = $field['id'];
		$name = $field['name'];
		$title = $field['title'] ?? $id;
		$value = boolval( $this->get_field_value( $field ) );
		$desc = $field['description'];
		$label = $field['label'] ?? $this->strings['checkbox_enable'];

		$this->open_wrapper( $field );
		?>

		<fieldset>
			<legend class="screen-reader-text"><span><?= esc_html( strip_tags( $title ) ); ?></span></legend>
			<label for="<?= $name ?>">
				<input name="<?= esc_attr( $name ) ?>" type="checkbox" id="<?= esc_attr( $name ) ?>" value="1" <?php checked( $value ); ?> />
				<?= $label ?>
			</label>

			<?php $this->do_action( 'after_field_input', $field ); ?>

			<?php if ( $desc ) : ?>
			<p class="description"><?= $desc ?></p>
			<?php endif ?>
		</fieldset>

		<?php $this->close_wrapper( $field );
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field_checkboxes ( $field ) {
		$id = $field['id'];
		$name = $field['name'];
		$title = $field['title'] ?? $id;
		$desc = $field['description'];
		$options = $field['options'];
		$value = $this->get_field_value( $field );
		$value = is_array( $value ) ? $value : [ $value ];

		$this->open_wrapper( $field );
		?>

		<fieldset>
			<legend class="screen-reader-text"><span><?= esc_html( strip_tags( $title ) ); ?></span></legend>

			<?php foreach ( $options as $key => $label ) :
				$option_id = esc_attr( $id . '_' . $key );
				$checked = in_array( $key, $value ) ? 'checked="checked"' : '' ?>
				<label for="<?= $option_id ?>">
					<input name="<?= esc_attr( $name ) . '[]' ?>" type="checkbox" id="<?= esc_attr( $option_id ) ?>" value="<?= esc_attr( $key ) ?>" <?= $checked ?>>
					<?= $label ?>
				</label>
				<br>
			<?php endforeach ?>

			<?php $this->do_action( 'after_field_input', $field ); ?>

			<?php if ( $desc ) : ?>
			<p class="description"><?= $desc ?></p>
			<?php endif ?>
		</fieldset>

		<?php $this->close_wrapper( $field );
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field_title ( $field ) {
		$id = $field['id'] ? $this->field_prefix . $field['id'] : '';
		$icon = $this->get_icon( $field['title_icon'] );
		$desc = $field['description'];
		$class = $field['class'] ?? '';
		?>
		<h1 id="<?= esc_attr( $id ) ?>" class="<?= esc_attr( $class ) ?>"><?= esc_html( $field['title'] ) . $icon ?></h1>
		<?php if ( $desc ) : ?>
		<p><?= $desc ?></p>
		<?php endif ?>
		<?php
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field_subtitle ( $field ) {
		$id = $field['id'] ? $this->field_prefix . $field['id'] : '';
		$icon = $this->get_icon( $field['title_icon'] );
		$desc = $field['description'];
		$class = $field['class'] ?? '';
		?>
		<h2 id="<?= esc_attr( $id ) ?>" class="<?= esc_attr( $class ) ?>"><?= esc_html( $field['title'] ) . $icon ?></h2>
		<?php if ( $desc ) : ?>
		<p><?= $desc ?></p>
		<?php endif ?>
		<?php
	}

	/**
	 * @param array $field
	 * @return void
	 */
	protected function render_field_submit ( $field ) {
		$title = $field['title'] ?? __( 'Save Changes' );
		$class = $field['class'] ?? 'button button-primary';
		?>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="<?= esc_attr( $class ) ?>" value="<?= esc_attr( $title ) ?>">
		</p>
		<?php
	}
}
