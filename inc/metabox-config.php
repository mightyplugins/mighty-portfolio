<?php
add_action( 'ctf_add_metabox', 'mp_metaboxes_config' );
function mp_metaboxes_config() {

	if (class_exists('CantoMetabox')) {
		$ct_portfolio_meta = array(
            'id' => 'ct_portfolio_meta',
            'title' => __('Portfolio Info', 'mighty-portfolio'),
            'post_type' => array('mp-portfolio'),
            'options' => array(
            	array(
                    'id' => 'gallery',
                    'label'    => __( 'Gallery', 'mighty-portfolio' ),
                    'subtitle'    => __( 'Lorem ipsum dolor sit amet', 'mighty-portfolio' ),
                    'type'     => 'image_multi',
                    'default' => '',
                ),
                array(
                    'id' => 'short_desc',
                    'label'    => __( 'Short Description', 'mighty-portfolio' ),
                    'subtitle'    => __( 'Lorem ipsum dolor sit amet', 'mighty-portfolio' ),
                    'type'     => 'editor',
                    'default' => '',
                ),
                array(
					'id' => 'start_date',
					'label'    => __( 'Start Date', 'mighty-portfolio' ),
					'subtitle'    => __( 'Lorem ipsum dolor sit amet', 'mighty-portfolio' ),
					'type'     => 'date',
					'default' => '',
					'choices' => array(
						'dateFormat' => 'd MM, yy'
					)
                ),
                array(
					'id' => 'end_date',
					'label'    => __( 'End Date', 'mighty-portfolio' ),
					'subtitle'    => __( 'Lorem ipsum dolor sit amet', 'mighty-portfolio' ),
					'type'     => 'date',
					'default' => '',
					'choices' => array(
						'dateFormat' => 'd MM, yy'
					)
                ),
                array(
                    'id' => 'client',
                    'label'    => __( 'Client', 'mighty-portfolio' ),
                    'subtitle'    => __( 'Lorem ipsum dolor sit amet', 'mighty-portfolio' ),
                    'type'     => 'text',
                    'default' => '',
                ),
                array(
                    'id' => 'client_url',
                    'label'    => __( 'Client URL', 'mighty-portfolio' ),
                    'subtitle'    => __( 'Lorem ipsum dolor sit amet', 'mighty-portfolio' ),
                    'type'     => 'text',
                    'default' => '',
                ),
            )
        );

        CantoMetabox::add_metabox($ct_portfolio_meta);
	}
}