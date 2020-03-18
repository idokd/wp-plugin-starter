<?php
/**
 * CONF Plugin Name Admin Metaboxes.
 *
 * @package   Plugin_Name
 * @author    CONF_Plugin_Author
 * @license   GPL-2.0+
 * @link      CONF_Author_Link
 * @copyright CONF_Plugin_Copyright
 */

/**
 * -----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 * -----------------------------------------
 */
defined( 'ABSPATH' ) || exit;
/*-----------------------------------------*/

class Plugin_Name_Admin_Metaboxes {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Sreens to display the custom metabox.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $screens = array(
		'entries',
	);

	/**
	 * Metabox fields
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $fields = array();

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->fields = $this->metabox_fields();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'admin_footer' ) );
		add_action( 'save_post', array( $this, 'save_plugin_name_data' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Generate metabox fields.
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function metabox_fields() {
		$meta_fields = array(
			array(
				'id'          => '_text_meta',
				'label'       => esc_html__( 'Text Metabox', 'plugin-name' ),
				'placeholder' => esc_html__( 'This is a placeholder inside the text box', 'plugin-name' ),
				'type'        => 'text',
				'description' => esc_html__( 'This is the description below the text box', 'plugin-name' ),
			),
			array(
				'id'    => '_checkbox',
				'label' => esc_html__( 'Checkbox', 'plugin-name' ),
				'type'  => 'checkbox',
				'std'   => 1,
			),
			array(
				'id'      => '_select_meta',
				'label'   => esc_html__( 'Select Any Value', 'plugin-name' ),
				'type'    => 'select',
				'options' => array(
					'yes'  => esc_html__( 'Enable', 'plugin-name' ),
					'no'   => esc_html__( 'Disable', 'plugin-name' ),
					'none' => esc_html__( 'Do Nothing', 'plugin-name' ),
				),
			),
			array(
				'id'          => '_file_meta',
				'label'       => esc_html__( 'File Upload', 'plugin-name' ),
				'type'        => 'media',
				'placeholder' => esc_html__( 'Upload a file', 'plugin-name' ),
			),
			array(
				'id'          => 'textarea',
				'label'       => esc_html__( 'Textarea Input', 'plugin-name' ),
				'type'        => 'textarea',
				'placeholder' => esc_html__( 'Textarea placeholder', 'plugin-name' ),
			),
			array(
				'id'          => '_color-picker',
				'label'       => esc_html__( 'Color Picker', 'plugin-name' ),
				'type'        => 'color',
				'description' => esc_html__( 'This is the description below the color selection box.', 'plugin-name' ),
			),
			array(
				'id'          => '_number',
				'label'       => esc_html__( 'Number', 'plugin-name' ),
				'type'        => 'number',
				'description' => esc_html__( 'This is the description below the number box', 'plugin-name' ),
			),
			array(
				'id'      => '_radio',
				'label'   => esc_html__( 'Radio', 'plugin-name' ),
				'type'    => 'radio',
				'options' => array(
					'main'  => esc_html__( 'Main option', 'plugin-name' ),
					'other' => esc_html__( 'Other option', 'plugin-name' ),
				),
			),
		);
		return apply_filters( 'plugin_name_meta_fields', $meta_fields );
	}

	/**
	 * Add Meta Boxes
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_meta_boxes() {
		foreach ( $this->screens as $screen ) {
			add_meta_box(
				'plugin_name_data',
				esc_html__( 'Plugin Name Metaboxes', 'plugin-name' ),
				array( $this, 'plugin_name_meta_box_callback' ),
				$screen,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Generates the HTML for the meta box
	 *
	 * @param object $post WordPress post object.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function plugin_name_meta_box_callback( $post ) {
		wp_nonce_field( 'plugin_name_data', 'plugin_name_nonce' );
		$this->generate_fields( $post );
	}

	/**
	 * Adds scripts for media uploader.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function admin_footer() {
		?><script>
			jQuery( document ).ready( function( $ ){
				var file_frame;
				var file_target_input;

				$('.plugin-name-media').live('click', function(e){

					e.preventDefault();

					file_target_input = $( this ).closest('.form-table').find('.file_url');

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.file_frame = wp.media({
						title: $( this ).data( 'uploader_title' ),
						button: {
							text: $( this ).data( 'uploader_button_text' ),
						},
						multiple: false  // Set to true to allow multiple files to be selected.
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						// We set multiple to false so only get one image from the uploader.
						attachment = file_frame.state().get('selection').first().toJSON();

						$( file_target_input ).val( attachment.url );
					});

					// Finally, open the modal.
					file_frame.open();
				});
			});
		</script>
		<?php
	}

	/**
	 * Generates the field's HTML for the meta box.
	 *
	 * @param object $post The post object.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function generate_fields( $post ) {
		$output = '';

		foreach ( $this->fields as $field ) {
			$label       = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
			$db_value    = get_post_meta( $post->ID, 'plugin_name_' . $field['id'], true );
			$description = isset( $field['description'] ) ? esc_html( $field['description'] ) : '';

			switch ( $field['type'] ) {
				case 'checkbox':
					$input = sprintf(
						'<input %s id="%s" name="%s" type="checkbox" value="1">',
						'1' === $db_value ? 'checked' : '',
						$field['id'],
						$field['id']
					);
					break;
				case 'media':
					$input = sprintf(
						'<input class="regular-text file_url" id="%s" name="%s" type="text" placeholder="%s" value="%s"> <input class="button plugin-name-media" id="%s_button" name="%s_button" data-uploader_button_text="Upload an image" type="button" value="Upload" />',
						$field['id'],
						$field['id'],
						$field['placeholder'],
						$db_value,
						$field['id'],
						$field['id']
					);
					break;
				case 'radio':
					$input  = '<fieldset>';
					$input .= '<legend class="screen-reader-text">' . $field['label'] . '</legend>';
					$i      = 0;
					foreach ( $field['options'] as $key => $value ) {
						$field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<label><input %s id="%s" name="%s" type="radio" value="%s"> %s</label>%s',
							$db_value === $field_value ? 'checked' : '',
							$field['id'],
							$field['id'],
							$field_value,
							$value,
							$i < count( $field['options'] ) - 1 ? '<br>' : ''
						);
						$i++;
					}
					$input .= '</fieldset>';
					break;
				case 'select':
					$input = sprintf(
						'<select id="%s" name="%s">',
						$field['id'],
						$field['id']
					);
					foreach ( $field['options'] as $key => $value ) {
						$field_value = ! is_numeric( $key ) ? $key : $value;
						$input      .= sprintf(
							'<option %s value="%s">%s</option>',
							$db_value === $field_value ? 'selected' : '',
							$field_value,
							$value
						);
					}
					$input .= '</select>';
					break;
				case 'textarea':
					$input = sprintf(
						'<textarea class="large-text" id="%s" name="%s" placeholder="%s" rows="5">%s</textarea>',
						$field['id'],
						$field['id'],
						$field['placeholder'],
						$db_value
					);
					break;
				case 'number':
					$input = sprintf(
						'<input class="small-text" id="%s" name="%s" type="%s" value="%s" size="30"><div class="description">%s</div>',
						$field['id'],
						$field['id'],
						$field['type'],
						$db_value,
						$description
					);
					break;
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s"><div class="description">%s</div>',
						'color' !== $field['type'] ? 'class="regular-text"' : '',
						$field['id'],
						$field['id'],
						$field['type'],
						$db_value,
						$description
					);
			}
			$output .= sprintf(
				'<tr><th scope="row">%s</th><td>%s</td></tr>',
				$label,
				$input
			);
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}

	/**
	 * Hooks into WordPress' save_post function.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function save_plugin_name_data( $post_id ) {
		// Validate nonce.
		if ( ! isset( $_POST['plugin_name_nonce'] ) ) {
			return $post_id;
		}

		$nonce = wp_unslash( $_POST['plugin_name_nonce'] );
		if ( ! wp_verify_nonce( $nonce, 'plugin_name_data' ) ) {
			return $post_id;
		}

		// Bail if doing autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Bail if user is not authorized.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		foreach ( $this->fields as $field ) {
			if ( isset( $_POST[ $field['id'] ] ) ) {
				switch ( $field['type'] ) {
					case 'email':
						$_POST[ $field['id'] ] = sanitize_email( $_POST[ $field['id'] ] );
						break;
					case 'text':
						$_POST[ $field['id'] ] = sanitize_text_field( $_POST[ $field['id'] ] );
						break;
				}
				update_post_meta( $post_id, 'plugin_name_' . $field['id'], $_POST[ $field['id'] ] );
			} else if ( 'checkbox' === $field['type'] ) {
				update_post_meta( $post_id, 'plugin_name_' . $field['id'], '0' );
			}
		}
	}
}
