<?php
namespace TB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPress Admin Dashboard Controller class.
 */
class TB_Admin {

	/**
	 * Singleton instance.
	 *
	 * @var TB_Admin|null
	 */
	private static $instance = null;

	/**
	 * Fetch active instance.
	 *
	 * @return TB_Admin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Admin hooks
		add_action( 'add_meta_boxes', array( $this, 'add_templ_item_metabox' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_action( 'admin_notices', array( $this, 'display_invalid_json_notice' ) );
		add_action( 'admin_menu', array( $this, 'register_tools_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'handle_admin_actions' ) );
	}

	/**
	 * Enqueue assets conditionally on plugin admin screens.
	 *
	 * @param string $hook Admin page hook name.
	 */
	public function enqueue_admin_assets( $hook ) {
		global $post_type;

		// Post edit page for CPT templ_item
		$is_cpt_page = ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'templ_item' === $post_type;

		// Tools page
		$is_tools_page = 'tools_page_tb-tools-page' === $hook;

		if ( $is_cpt_page || $is_tools_page ) {
			wp_enqueue_media();
			wp_enqueue_style( 'tb-admin-css' );
			wp_enqueue_script( 'tb-admin-js' );
		}
	}

	/**
	 * Register Custom Metabox for CPT templ_item.
	 */
	public function add_templ_item_metabox() {
		add_meta_box(
			'tb-meta-box',
			__( 'Templ Item Structured Fields', 'templ-builder' ),
			array( $this, 'render_meta_box' ),
			'templ_item',
			'normal',
			'high'
		);
	}

	/**
	 * Render the metabox HTML layout file.
	 *
	 * @param \WP_Post $post Current post object.
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'tb_save_meta_box', 'tb_meta_box_nonce' );

		$fields_config = tb_get_meta_fields_config();
		$meta_values   = array();

		foreach ( $fields_config as $key => $config ) {
			if ( 'custom_json_fields' === $key ) {
				$invalid_val = get_post_meta( $post->ID, '_tb_custom_json_fields_invalid', true );
				if ( ! empty( $invalid_val ) ) {
					$meta_values[ $key ] = $invalid_val;
				} else {
					$val = get_post_meta( $post->ID, '_tb_custom_json_fields', true );
					if ( ! empty( $val ) ) {
						// Format nicely for editing
						$decoded = json_decode( $val, true );
						$meta_values[ $key ] = is_array( $decoded ) ? wp_json_encode( $decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) : $val;
					} else {
						$meta_values[ $key ] = '';
					}
				}
			} else {
				$val = get_post_meta( $post->ID, '_tb_' . $key, true );
				if ( '' === $val && isset( $config['default'] ) ) {
					$val = $config['default'];
				}
				$meta_values[ $key ] = $val;
			}
		}

		include TB_PATH . 'templates/admin-meta-box.php';
	}

	/**
	 * Save metabox structured fields.
	 *
	 * @param int $post_id Post identifier.
	 */
	public function save_meta_box( $post_id ) {
		// Nonce check
		if ( ! isset( $_POST['tb_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['tb_meta_box_nonce'], 'tb_save_meta_box' ) ) {
			return;
		}

		// Autosave check
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Permission check
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Post type check
		if ( 'templ_item' !== get_post_type( $post_id ) ) {
			return;
		}

		$fields_config = tb_get_meta_fields_config();

		foreach ( $fields_config as $key => $config ) {
			$post_key = 'tb_' . $key;

			if ( 'custom_json_fields' === $key ) {
				if ( isset( $_POST[ $post_key ] ) ) {
					$json_val = wp_unslash( $_POST[ $post_key ] );
					$trimmed  = trim( $json_val );
					if ( ! empty( $trimmed ) ) {
						$decoded = json_decode( $trimmed, true );
						if ( null === $decoded ) {
							// Invalid JSON: Save temporary copy and flag alert
							update_post_meta( $post_id, '_tb_invalid_json_flag', '1' );
							update_post_meta( $post_id, '_tb_custom_json_fields_invalid', $trimmed );
						} else {
							// Valid JSON: clean flags and save
							delete_post_meta( $post_id, '_tb_invalid_json_flag' );
							delete_post_meta( $post_id, '_tb_custom_json_fields_invalid' );
							update_post_meta( $post_id, '_tb_custom_json_fields', wp_json_encode( $decoded ) );
						}
					} else {
						delete_post_meta( $post_id, '_tb_invalid_json_flag' );
						delete_post_meta( $post_id, '_tb_custom_json_fields_invalid' );
						update_post_meta( $post_id, '_tb_custom_json_fields', '' );
					}
				}
				continue;
			}

			// Handle regular fields
			if ( 'checkbox' === $config['type'] ) {
				$val = isset( $_POST[ $post_key ] ) ? '1' : '0';
			} else {
				if ( ! isset( $_POST[ $post_key ] ) ) {
					continue;
				}
				$val = wp_unslash( $_POST[ $post_key ] );
			}

			$sanitized = tb_sanitize_meta_value( $key, $val );
			update_post_meta( $post_id, '_tb_' . $key, $sanitized );
		}
	}

	/**
	 * Display warning notification if save payload contained invalid JSON structure.
	 */
	public function display_invalid_json_notice() {
		global $post;
		if ( $post && 'templ_item' === $post->post_type ) {
			$flag = get_post_meta( $post->ID, '_tb_invalid_json_flag', true );
			if ( $flag ) {
				printf(
					'<div class="notice notice-error is-dismissible"><p><strong>%s</strong> %s</p></div>',
					esc_html__( 'Templ Builder Notice:', 'templ-builder' ),
					esc_html__( 'The custom JSON fields contained malformed formatting syntax. The valid parameters were preserved but your invalid adjustments were not applied on the frontend layout.', 'templ-builder' )
				);
				delete_post_meta( $post->ID, '_tb_invalid_json_flag' );
			}
		}
	}

	/**
	 * Register Admin Tools Page under Tools.
	 */
	public function register_tools_page() {
		add_management_page(
			__( 'Templ Builder Tools', 'templ-builder' ),
			__( 'Templ Builder', 'templ-builder' ),
			'manage_options',
			'tb-tools-page',
			array( $this, 'render_tools_page' )
		);
	}

	/**
	 * Render Tools page HTML template.
	 */
	public function render_tools_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		include TB_PATH . 'templates/admin-tools-page.php';
	}

	/**
	 * Processes admin Tools Actions.
	 */
	public function handle_admin_actions() {
		if ( ! is_admin() ) {
			return;
		}

		$action = isset( $_REQUEST['tb_action'] ) ? sanitize_key( $_REQUEST['tb_action'] ) : '';
		if ( empty( $action ) ) {
			return;
		}

		// Nonce checks
		if ( in_array( $action, array( 'generate_demo', 'delete_demo', 'import_json', 'export_json', 'static_html_export' ), true ) ) {
			if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'tb_tools_nonce' ) ) {
				wp_die( esc_html__( 'Security check failed. Please refresh the page.', 'templ-builder' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access.', 'templ-builder' ) );
			}
		} else {
			return;
		}

		switch ( $action ) {
			case 'generate_demo':
				$result = $this->generate_demo_items();
				wp_safe_redirect( add_query_arg( array( 'page' => 'tb-tools-page', 'tb_notice' => 'demo_generated', 'count' => $result ), admin_url( 'tools.php' ) ) );
				exit;

			case 'delete_demo':
				$result = $this->delete_demo_items();
				wp_safe_redirect( add_query_arg( array( 'page' => 'tb-tools-page', 'tb_notice' => 'demo_deleted', 'count' => $result ), admin_url( 'tools.php' ) ) );
				exit;

			case 'export_json':
				TB_Exporter::get_instance()->export_to_json();
				exit;

			case 'import_json':
				$publish = isset( $_POST['tb_publish_imported'] );
				$result  = TB_Exporter::get_instance()->import_from_json( $_FILES['tb_import_file'], $publish );
				if ( $result < 0 ) {
					$error_map = array(
						-1 => 'unauthorized',
						-2 => 'upload_error',
						-3 => 'invalid_format',
						-4 => 'empty_data',
					);
					$err_code = isset( $error_map[ $result ] ) ? $error_map[ $result ] : 'error';
					wp_safe_redirect( add_query_arg( array( 'page' => 'tb-tools-page', 'tb_error' => $err_code ), admin_url( 'tools.php' ) ) );
				} else {
					wp_safe_redirect( add_query_arg( array( 'page' => 'tb-tools-page', 'tb_notice' => 'imported', 'count' => $result ), admin_url( 'tools.php' ) ) );
				}
				exit;

			case 'static_html_export':
				$this->process_static_html_export();
				exit;
		}
	}

	/**
	 * Generates the 9 pre-configured demo items.
	 *
	 * @return int Number of items successfully created.
	 */
	private function generate_demo_items() {
		// Stop if demo items already exist
		$demo_query = new \WP_Query(
			array(
				'post_type'      => 'templ_item',
				'posts_per_page' => 1,
				'post_status'    => 'any',
				'meta_query'     => array(
					array(
						'key'     => '_tb_demo_item',
						'value'   => '1',
						'compare' => '=',
					),
				),
			)
		);
		$exists = $demo_query->have_posts();
		wp_reset_postdata();

		if ( $exists ) {
			return 0;
		}

		$demos = array(
			array(
				'title'       => 'Aegis Security Scanner',
				'content'     => 'A robust WordPress security auditing tool that scans for vulnerabilities, malware, and outdated plugins.',
				'excerpt'     => 'Secure your WordPress site with Aegis Scanner.',
				'types'       => array( 'tool' ),
				'collections' => array( 'app-directory' ),
				'meta'        => array(
					'template_key'      => 'app-card',
					'status'            => 'active',
					'badge'             => 'Top Pick',
					'icon'              => 'dashicons-shield',
					'accent_color'      => '#10b981',
					'eyebrow'           => 'Security Audit',
					'subtitle'          => 'Ultimate Protection',
					'short_description' => 'Scan and secure your WordPress installation.',
					'external_url'      => 'https://example.com/aegis',
					'button_label'      => 'View Tool',
					'price'             => '$29/mo',
					'rating'            => '4.9',
					'custom_json_fields'=> '{"difficulty": "Intermediate", "features": ["Vulnerability Scan", "Malware Detection", "Outdated plugin checks"], "tech_stack": ["PHP", "WordPress API"]}',
				),
			),
			array(
				'title'       => 'MailFlow Automation',
				'content'     => 'Email marketing automation built directly for WordPress. Segment lists, automate campaigns, and track conversions easily.',
				'excerpt'     => 'Automate your email marketing campaigns.',
				'types'       => array( 'app' ),
				'collections' => array( 'app-directory' ),
				'meta'        => array(
					'template_key'      => 'app-card',
					'status'            => 'active',
					'badge'             => 'New',
					'icon'              => 'dashicons-email-alt',
					'accent_color'      => '#ec4899',
					'eyebrow'           => 'Email Marketing',
					'subtitle'          => 'Smart automation workflows',
					'short_description' => 'Connect and automate your marketing processes.',
					'external_url'      => 'https://example.com/mailflow',
					'button_label'      => 'Get App',
					'custom_json_fields'=> '{"price_plan": "Free Trial", "status": "Available", "stack": ["PHP", "Vanilla JS", "Tailwind"]}',
				),
			),
			array(
				'title'       => 'Premium Custom Web Development',
				'content'     => 'Tailored WordPress development services for businesses. We build fast, high-converting custom themes and robust plugins.',
				'excerpt'     => 'Custom development services tailored for your business.',
				'types'       => array( 'service' ),
				'collections' => array( 'services-page' ),
				'meta'        => array(
					'template_key'      => 'service-card',
					'status'            => 'active',
					'badge'             => 'Popular',
					'icon'              => 'dashicons-admin-customizer',
					'accent_color'      => '#2563eb',
					'eyebrow'           => 'Development',
					'subtitle'          => 'Professional Code',
					'short_description' => 'We design and build bespoke WordPress projects.',
					'external_url'      => 'https://example.com/services',
					'button_label'      => 'Book Service',
					'price'             => '$999',
					'custom_json_fields'=> '{"audience": "Agencies & Enterprises", "benefits": ["Speed Optimized", "Mobile First", "SEO Ready"]}',
				),
			),
			array(
				'title'       => 'Build Templates Faster Than Ever',
				'content'     => 'Templ Builder empowers you to build clean, lightweight content modules in WordPress. Ditch the slow page builders and write structured data that renders beautifully.',
				'excerpt'     => 'Modern content template management for WordPress.',
				'types'       => array( 'landing' ),
				'collections' => array( 'landing-page' ),
				'meta'        => array(
					'template_key'           => 'landing-section',
					'status'                 => 'active',
					'icon'                   => 'dashicons-editor-expand',
					'accent_color'           => '#4f46e5',
					'eyebrow'                => 'Meet Templ Builder',
					'subtitle'               => 'The 11ty equivalent inside WordPress',
					'short_description'      => 'Modern structured layouts. No dependencies. Lightweight PHP visual rendering.',
					'external_url'           => 'https://example.com/docs',
					'button_label'           => 'Documentation',
					'secondary_url'          => 'https://example.com/download',
					'secondary_button_label' => 'Download Free',
				),
			),
			array(
				'title'       => 'Sarah Jenkins',
				'content'     => 'Using Templ Builder changed our entire backend layout. We built a campaign landing page in minutes using structural JSON fields.',
				'excerpt'     => "Sarah's feedback on Templ Builder.",
				'types'       => array( 'testimonial' ),
				'collections' => array( 'social-proof' ),
				'meta'        => array(
					'template_key' => 'testimonial',
					'status'       => 'active',
					'badge'        => 'Verified Buyer',
					'icon'         => 'dashicons-testimonial',
					'accent_color' => '#f59e0b',
					'role_label'   => 'Digital Marketer',
					'organization' => 'Aura Media',
					'location_label'=> 'London, UK',
					'rating'       => '5',
				),
			),
			array(
				'title'       => 'Is there any theme dependency?',
				'content'     => 'No. Templ Builder is engineered to be 100% theme-independent. It injects scoped clean styles that adapt naturally to both LTR and RTL WordPress settings.',
				'excerpt'     => 'Theme dependency and compatibility.',
				'types'       => array( 'faq' ),
				'collections' => array( 'homepage' ),
				'meta'        => array(
					'template_key'      => 'faq',
					'status'            => 'active',
					'icon'              => 'dashicons-editor-help',
					'accent_color'      => '#3b82f6',
					'short_description' => 'Our frequently asked questions.',
				),
			),
			array(
				'title'       => 'Rebrand NGO Campaign',
				'content'     => 'Complete branding and site redesign for the Hope Foundation NGO. Increased donation conversions by 45% using structured custom campaign elements.',
				'excerpt'     => 'Redesign work for Hope Foundation.',
				'types'       => array( 'portfolio' ),
				'collections' => array( 'portfolio-grid' ),
				'meta'        => array(
					'template_key'      => 'portfolio-card',
					'status'            => 'active',
					'badge'             => 'Award Winner',
					'icon'              => 'dashicons-portfolio',
					'accent_color'      => '#06b6d4',
					'eyebrow'           => 'Case Study',
					'subtitle'          => 'Conversion Lift',
					'short_description' => 'Redesigned NGO campaign portal.',
					'external_url'      => 'https://example.com/ngo',
					'button_label'      => 'View Project',
					'custom_json_fields'=> '{"tech_stack": ["Next.js", "WordPress REST API"], "result": "45% conversion increase"}',
				),
			),
			array(
				'title'       => 'Lightweight Architecture',
				'content'     => 'Built using modular template components. Loads clean CSS and optional progressive JS only on pages that contain the shortcode.',
				'excerpt'     => 'Extremely fast loading times and zero bloat.',
				'types'       => array( 'feature' ),
				'collections' => array( 'product-launch' ),
				'meta'        => array(
					'template_key'      => 'feature-section',
					'status'            => 'active',
					'icon'              => 'dashicons-performance',
					'accent_color'      => '#f43f5e',
					'short_description' => 'Zero bloat, vanilla JS framework.',
					'custom_json_fields'=> '{"benefit": "Sub-millisecond page speed render contribution"}',
				),
			),
			array(
				'title'       => 'Best plugin of the year!',
				'content'     => 'Clean interface, structured field layout is beautiful.',
				'excerpt'     => 'Customer testimonial excerpt.',
				'types'       => array( 'social-proof' ),
				'collections' => array( 'social-proof' ),
				'meta'        => array(
					'template_key' => 'social-proof',
					'status'       => 'active',
					'icon'         => 'dashicons-heart',
					'accent_color' => '#8b5cf6',
					'role_label'   => 'WordPress Admin',
					'organization' => 'Kozmos Agency',
				),
			),
		);

		$count = 0;
		$fields_config = tb_get_meta_fields_config();

		foreach ( $demos as $demo_data ) {
			$post_id = wp_insert_post(
				array(
					'post_title'   => $demo_data['title'],
					'post_content' => $demo_data['content'],
					'post_excerpt' => $demo_data['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'templ_item',
				)
			);

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				// Mark as demo
				update_post_meta( $post_id, '_tb_demo_item', '1' );

				// Assign taxonomies
				if ( ! empty( $demo_data['types'] ) ) {
					foreach ( $demo_data['types'] as $type ) {
						wp_insert_term( $type, 'templ_type' );
						wp_set_object_terms( $post_id, $type, 'templ_type', true );
					}
				}
				if ( ! empty( $demo_data['collections'] ) ) {
					foreach ( $demo_data['collections'] as $collection ) {
						wp_insert_term( $collection, 'templ_collection' );
						wp_set_object_terms( $post_id, $collection, 'templ_collection', true );
					}
				}

				// Assign metas
				foreach ( $fields_config as $key => $config ) {
					if ( isset( $demo_data['meta'][ $key ] ) ) {
						$meta_val = $demo_data['meta'][ $key ];
						if ( 'custom_json_fields' === $key ) {
							$sanitized = tb_sanitize_custom_json( $meta_val );
						} else {
							$sanitized = tb_sanitize_meta_value( $key, $meta_val );
						}
						update_post_meta( $post_id, '_tb_' . $key, $sanitized );
					}
				}

				$count++;
			}
		}

		return $count;
	}

	/**
	 * Deletes all demo posts.
	 *
	 * @return int Number of deleted posts.
	 */
	private function delete_demo_items() {
		$demo_query = new \WP_Query(
			array(
				'post_type'      => 'templ_item',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'meta_query'     => array(
					array(
						'key'     => '_tb_demo_item',
						'value'   => '1',
						'compare' => '=',
					),
				),
			)
		);

		$count = 0;
		if ( $demo_query->have_posts() ) {
			while ( $demo_query->have_posts() ) {
				$demo_query->the_post();
				$post_id = get_the_ID();
				wp_delete_post( $post_id, true ); // Bypass trash
				$count++;
			}
			wp_reset_postdata();
		}

		return $count;
	}

	/**
	 * Build static HTML snippet file download.
	 */
	private function process_static_html_export() {
		$template   = isset( $_POST['tb_export_template'] ) ? sanitize_key( $_POST['tb_export_template'] ) : 'all';
		$type       = isset( $_POST['tb_export_type'] ) ? sanitize_key( $_POST['tb_export_type'] ) : 'all';
		$collection = isset( $_POST['tb_export_collection'] ) ? sanitize_key( $_POST['tb_export_collection'] ) : 'all';
		$limit      = isset( $_POST['tb_export_limit'] ) ? absint( $_POST['tb_export_limit'] ) : 6;

		$shortcode_atts = array(
			'limit'      => $limit,
			'show_empty' => 'true',
		);

		if ( 'all' !== $template ) {
			$shortcode_atts['template'] = $template;
		}
		if ( 'all' !== $type ) {
			$shortcode_atts['type'] = $type;
		}
		if ( 'all' !== $collection ) {
			$shortcode_atts['collection'] = $collection;
		}

		// Call the shortcode handler directly
		$rendered_html = TB_Shortcode::get_instance()->handle_shortcode( $shortcode_atts );

		$header  = "<!--\n";
		$header .= "  Templ Builder Static HTML Snippet\n";
		$header .= "  Generated on: " . current_time( 'Y-m-d H:i:s' ) . "\n";
		$header .= "  Plugin Version: " . TB_VERSION . "\n";
		$header .= "  Template Style Filter: " . esc_html( $template ) . "\n";
		$header .= "  Type Filter: " . esc_html( $type ) . "\n";
		$header .= "  Collection Filter: " . esc_html( $collection ) . "\n";
		$header .= "  Query Limit: " . esc_html( $limit ) . "\n";
		$header .= "-->\n\n";

		$output = $header . $rendered_html;
		$filename = 'templ-builder-static-export-' . current_time( 'Y-m-d-His' ) . '.html';

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: text/html; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . strlen( $output ) );

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}
}
