<?php
/**
 * Abstract Base Class for Santa Towing Elementor Free Widgets
 *
 * Ensures consistent Content, Media, Style, Typography & Responsive controls
 * across all 17 custom widgets using strictly Elementor Free APIs.
 *
 * @package Santa_Towing_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class STWC_Widget_Base extends \Elementor\Widget_Base {

    /**
     * Assign widget to the Santa Towing category.
     */
    public function get_categories() {
        return array( 'santa-towing' );
    }

    /**
     * Declare frontend style & script dependencies.
     */
    public function get_style_depends() {
        return array( 'santa-towing-core' );
    }

    public function get_script_depends() {
        return array( 'santa-towing-core' );
    }

    /**
     * Add standardized Style + Responsive Section Controls shared by catalog/grid widgets.
     *
     * @param string $selector_prefix CSS wrapper class inside widget markup.
     * @param string $default_bg      Default section background hex.
     * @param string $default_title   Default title color hex.
     */
    protected function register_common_section_style_controls( $selector_prefix = '.stwc-widget-section', $default_bg = '#ffffff', $default_title = '#131514' ) {
        $this->start_controls_section(
            'section_style_layout',
            array(
                'label' => __( 'Section Layout & Colors', 'santa-towing-website-core' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'section_bg_color',
            array(
                'label'     => __( 'Background Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => $default_bg,
                'selectors' => array(
                    "{{WRAPPER}} {$selector_prefix}" => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'heading_color',
            array(
                'label'     => __( 'Heading Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => $default_title,
                'selectors' => array(
                    "{{WRAPPER}} {$selector_prefix} .stwc-section-heading" => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'accent_color',
            array(
                'label'     => __( 'Accent Color', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#d80000',
                'selectors' => array(
                    "{{WRAPPER}} {$selector_prefix} .stwc-accent-text" => 'color: {{VALUE}};',
                    "{{WRAPPER}} {$selector_prefix} .stwc-accent-bg"   => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'heading_typography',
                'label'    => __( 'Heading Typography', 'santa-towing-website-core' ),
                'selector' => "{{WRAPPER}} {$selector_prefix} .stwc-section-heading",
            )
        );

        $this->add_responsive_control(
            'section_padding',
            array(
                'label'      => __( 'Section Padding', 'santa-towing-website-core' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'rem', '%' ),
                'default'    => array(
                    'top'    => '64',
                    'right'  => '0',
                    'bottom' => '64',
                    'left'   => '0',
                    'unit'   => 'px',
                ),
                'selectors'  => array(
                    "{{WRAPPER}} {$selector_prefix}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'heading_align',
            array(
                'label'     => __( 'Header Alignment', 'santa-towing-website-core' ),
                'type'      => \Elementor\Controls_Manager::CHOOSE,
                'options'   => array(
                    'left'   => array( 'title' => __( 'Left', 'santa-towing-website-core' ), 'icon' => 'eicon-text-align-left' ),
                    'center' => array( 'title' => __( 'Center', 'santa-towing-website-core' ), 'icon' => 'eicon-text-align-center' ),
                    'right'  => array( 'title' => __( 'Right', 'santa-towing-website-core' ), 'icon' => 'eicon-text-align-right' ),
                ),
                'default'   => 'center',
                'selectors' => array(
                    "{{WRAPPER}} {$selector_prefix} .stwc-section-header" => 'text-align: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Map Elementor icon name string to inline Lucide SVG.
     */
    protected function render_icon( $name, $class = 'w-6 h-6' ) {
        return stwc_icon( $name, $class );
    }
}
