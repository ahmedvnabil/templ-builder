<?php
namespace TB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Visual Layout Template Manager class.
 */
class TB_Template_Manager {

	/**
	 * Singleton instance.
	 *
	 * @var TB_Template_Manager|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return TB_Template_Manager
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
	private function __construct() {}

	/**
	 * Get all pre-defined templates config meta.
	 *
	 * @return array
	 */
	public function get_templates_config() {
		return array(
			'card' => array(
				'label'          => __( 'Single Card', 'templ-builder' ),
				'description'    => __( 'Best for showing a single simple item.', 'templ-builder' ),
				'best_for'       => 'testimonials, services, tool highlights',
				'supports_media' => true,
				'supports_cta'   => true,
				'supports_json'  => true,
				'default_columns'=> 1,
			),
			'cards-grid' => array(
				'label'          => __( 'Cards Grid', 'templ-builder' ),
				'description'    => __( 'Best for tools, apps, services, portfolios.', 'templ-builder' ),
				'best_for'       => 'directories, team cards, product grids',
				'supports_media' => true,
				'supports_cta'   => true,
				'supports_json'  => true,
				'default_columns'=> 3,
			),
			'list' => array(
				'label'          => __( 'Compact List', 'templ-builder' ),
				'description'    => __( 'Best for resources, links, simple indexes.', 'templ-builder' ),
				'best_for'       => 'changelogs, links walls, resource lists',
				'supports_media' => true,
				'supports_cta'   => true,
				'supports_json'  => false,
				'default_columns'=> 1,
			),
			'feature-section' => array(
				'label'          => __( 'Feature Section', 'templ-builder' ),
				'description'    => __( 'Best for product feature blocks.', 'templ-builder' ),
				'best_for'       => 'landing page benefit lists',
				'supports_media' => false,
				'supports_cta'   => true,
				'supports_json'  => true,
				'default_columns'=> 3,
			),
			'landing-section' => array(
				'label'          => __( 'Landing Page Section (Split)', 'templ-builder' ),
				'description'    => __( 'Best for landing page hero/content splits.', 'templ-builder' ),
				'best_for'       => 'landing page content splits',
				'supports_media' => true,
				'supports_cta'   => true,
				'supports_json'  => false,
				'default_columns'=> 2,
			),
			'testimonial' => array(
				'label'          => __( 'Testimonial Card', 'templ-builder' ),
				'description'    => __( 'Best for quotes and social proof.', 'templ-builder' ),
				'best_for'       => 'customer recommendations, testimonials',
				'supports_media' => true,
				'supports_cta'   => false,
				'supports_json'  => false,
				'default_columns'=> 1,
			),
			'faq' => array(
				'label'          => __( 'Accordion FAQ', 'templ-builder' ),
				'description'    => __( 'Best for FAQ blocks.', 'templ-builder' ),
				'best_for'       => 'frequently asked questions',
				'supports_media' => false,
				'supports_cta'   => false,
				'supports_json'  => false,
				'default_columns'=> 1,
			),
			'portfolio-card' => array(
				'label'          => __( 'Portfolio Project Card', 'templ-builder' ),
				'description'    => __( 'Best for projects/case studies.', 'templ-builder' ),
				'best_for'       => 'portfolios, project grids, works showcase',
				'supports_media' => true,
				'supports_cta'   => true,
				'supports_json'  => true,
				'default_columns'=> 3,
			),
			'app-card' => array(
				'label'          => __( 'App Directory Card', 'templ-builder' ),
				'description'    => __( 'Best for app/tool directory.', 'templ-builder' ),
				'best_for'       => 'software listings, app showcases',
				'supports_media' => true,
				'supports_cta'   => true,
				'supports_json'  => true,
				'default_columns'=> 3,
			),
			'service-card' => array(
				'label'          => __( 'Service/Pricing Card', 'templ-builder' ),
				'description'    => __( 'Best for service offerings.', 'templ-builder' ),
				'best_for'       => 'service listing, subscription plans',
				'supports_media' => false,
				'supports_cta'   => true,
				'supports_json'  => true,
				'default_columns'=> 3,
			),
			'social-proof' => array(
				'label'          => __( 'Social Proof Wall', 'templ-builder' ),
				'description'    => __( 'Best for proof walls and trust sections.', 'templ-builder' ),
				'best_for'       => 'logo walls, masonry testimonials',
				'supports_media' => true,
				'supports_cta'   => true,
				'supports_json'  => false,
				'default_columns'=> 3,
			),
			'minimal' => array(
				'label'          => __( 'Minimalistic Layout', 'templ-builder' ),
				'description'    => __( 'Best for compact layouts.', 'templ-builder' ),
				'best_for'       => 'plain text layouts, widgets',
				'supports_media' => false,
				'supports_cta'   => false,
				'supports_json'  => false,
				'default_columns'=> 1,
			),
			'single' => array(
				'label'          => __( 'Single Item Showcase', 'templ-builder' ),
				'description'    => __( 'Best for rendering one detailed item.', 'templ-builder' ),
				'best_for'       => 'individual profile displays, full detail lists',
				'supports_media' => true,
				'supports_cta'   => true,
				'supports_json'  => true,
				'default_columns'=> 1,
			),
		);
	}

	/**
	 * Validates layout key and returns fallback if invalid.
	 *
	 * @param string $key Template layout key.
	 * @return string Validated layout key.
	 */
	public function validate_template_key( $key ) {
		$configs = $this->get_templates_config();
		if ( isset( $configs[ $key ] ) ) {
			return $key;
		}

		// Fallback to settings value, then card.
		$default = tb_get_setting( 'default_template' );
		if ( isset( $configs[ $default ] ) ) {
			return $default;
		}

		return 'card';
	}
}
