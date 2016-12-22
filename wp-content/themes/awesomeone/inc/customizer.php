<?php
/**
 * Awesomeone Theme Customizer
 *
 * @package Awesomeone
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function awesomeone_customize_register( $wp_customize ) {

class WP_Customize_Textarea_Control extends WP_Customize_Control {
    public $type = 'textarea';

    public function render_content() {
        ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            </label>
        <?php
    }
}

function awesomeone_sanitize_checkbox( $input ) {
    if ( $input == 1 ) {
        return 1;
    } else {
        return '';
    }
}

//Add a class for titles
    class Awesomeone_Info extends WP_Customize_Control {
        public $type = 'info';
        public $label = '';
        public function render_content() {
        ?>
			<h3 style="text-decoration: underline; color: #DA4141; text-transform: uppercase;"><?php echo esc_html( $this->label ); ?></h3>
        <?php
        }
    }

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	$wp_customize->remove_control('header_textcolor');

	$wp_customize->add_section(
        'logo_sec',
        array(
            'title' => __('Logo (PRO Version)', 'awesomeone'),
            'priority' => 1,
            'description' => __('<strong>Logo settings available in</strong>','awesomeone'). '<a href="'.esc_url(pro_theme_url).'" target="_blank">PRO Version</a>.',
        )
    );
    $wp_customize->add_setting('Awesomeone_options[font-info]', array(
			'sanitize_callback' => 'sanitize_text_field',
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
        )
    );
    $wp_customize->add_control( new Awesomeone_Info( $wp_customize, 'logo_section', array(
        'section' => 'logo_sec',
        'settings' => 'Awesomeone_options[font-info]',
        'priority' => null
        ) )
    );

	$wp_customize->add_section('opacity',array(
			'title'	=> __('Background Opacity (PRO Version)','awesomeone'),
			'description'	=> __('<strong>Background opacity available in</strong>','awesomeone'). '<a href="'.esc_url(pro_theme_url).'">PRO Version</a>',
			'priority'	=> 2
	));

	$wp_customize->add_setting('bg_opacity',array(
			'sanitize_callback'	=> 'sanitize_text_field',
			'type'	=> 'info-control',
			'capability'	=> 'edit_theme_options'
	));

	$wp_customize->add_control(
		new Awesomeone_Info(
			$wp_customize,
			'bg_opacity',
			array(
				'setting'	=> 'bg_opacity',
				'section'	=> 'opacity'
			)
		)
	);

	$wp_customize->add_setting('color_scheme', array(
		'default' => '#0fa5d9',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));

	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','awesomeone'),
			'description'	=> __('<strong>More color options in</strong>','awesomeone'). '<a href="'.esc_url(pro_theme_url).'" target="_blank">PRO version</a>',
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);

	$wp_customize->add_section('social_section',array(
		'title'	=> __('Social Links','awesomeone'),
		'description'	=> 'Add your social links here. <br><strong>More social links in <a href="'.esc_url(pro_theme_url).'" target="_blank">PRO version</a>.</strong>',
		'priority'		=> null
	));


	$wp_customize->add_section('slider_section',array(
		'title'	=> __('Slider Settings','awesomeone'),
		'description'	=> __('Add slider images here. <br><strong>More slider settings available in</strong>','awesomeone'). '<a href="'.esc_url(pro_theme_url).'" target="_blank">PRO version</a>.',
		'priority'		=> null
	));

	$wp_customize->add_setting('button_hide',array(
		'sanitize_callback'	=> 'awesomeone_sanitize_checkbox'
	));

	$wp_customize->add_control('button_hide',array(
			'label'	=> __('Check this to hide purchase button','awesomeone'),
			'setting'	=> 'button_hide',
			'section'	=> 'slider_section',
			'type'	=> 'checkbox'
	));

	// Slide Image 1
	$wp_customize->add_setting('slide_image1',array(
		'default'	=> get_template_directory_uri().'/images/slides/slider1.jpg',
		'sanitize_callback'	=> 'esc_url_raw',
	));

	$wp_customize->add_control(
    new WP_Customize_Image_Control(
        $wp_customize,
        'slide_image1',
        array(
            'label' => __('Slide Image 1 (1440x700)','awesomeone'),
            'section' => 'slider_section',
            'settings' => 'slide_image1'
        )
    )
);

	$wp_customize->add_setting('slide_title1',array(
		'default'	=> 'Responsive Design',
		'sanitize_callback'	=> 'sanitize_text_field',
	));

	$wp_customize->add_control('slide_title1',array(
		'label'	=> __('Slide Title 1','awesomeone'),
		'section'	=> 'slider_section',
		'type'	=> 'text'
	));

	$wp_customize->add_setting('slide_desc1',array(
		'default'	=> 'This is description for slider one.',
		'sanitize_callback'	=> 'format_for_editor',
	));

	$wp_customize->add_control(
		new WP_Customize_Textarea_Control(
			$wp_customize,
			'slide_desc1',
			array(
				'label' => __('Slide Description 1','awesomeone'),
				'section' => 'slider_section',
				'setting'	=> 'slide_desc1'
			)
		)
	);

	$wp_customize->add_setting('slide_link1',array(
		'default'	=> '#link1',
		'sanitize_callback'	=> 'esc_url_raw',
	));

	$wp_customize->add_control('slide_link1',array(
		'label'	=> __('Slide Link 1','awesomeone'),
		'section'	=> 'slider_section',
		'type'		=> 'text'
	));

	// Slide Image 2
	$wp_customize->add_setting('slide_image2',array(
		'default'	=> get_template_directory_uri().'/images/slides/slider2.jpg',
		'sanitize_callback'	=> 'esc_url_raw',
	));

	$wp_customize->add_control(
    new WP_Customize_Image_Control(
        $wp_customize,
        'slide_image2',
        array(
            'label' => __('Slide Image 2 (1440x700)','awesomeone'),
            'section' => 'slider_section',
            'settings' => 'slide_image2'
        )
    )
);

	$wp_customize->add_setting('slide_title2',array(
		'default'	=> 'Flexible Design',
		'sanitize_callback'	=> 'sanitize_text_field',
	));

	$wp_customize->add_control('slide_title2',array(
		'label'	=> __('Slide Title 2','awesomeone'),
		'section'	=> 'slider_section',
		'type'	=> 'text'
	));

	$wp_customize->add_setting('slide_desc2',array(
		'default'	=> 'This is description for slide two',
		'sanitize_callback'	=> 'format_for_editor',
	));

	$wp_customize->add_control(
		new WP_Customize_Textarea_Control(
			$wp_customize,
			'slide_desc2',
			array(
				'label' => __('Slide Description 2','awesomeone'),
				'section' => 'slider_section',
				'setting'	=> 'slide_desc2'
			)
		)
	);

	$wp_customize->add_setting('slide_link2',array(
		'default'	=> '#link2',
		'sanitize_callback'	=> 'esc_url_raw',
	));

	$wp_customize->add_control('slide_link2',array(
		'label'	=> __('Slide Link 2','awesomeone'),
		'section'	=> 'slider_section',
		'type'		=> 'text'
	));

	// Slide Image 3
	$wp_customize->add_setting('slide_image3',array(
		'default'	=> get_template_directory_uri().'/images/slides/slider3.jpg',
		'sanitize_callback'	=> 'esc_url_raw',
	));

	$wp_customize->add_control(
    new WP_Customize_Image_Control(
        $wp_customize,
        'slide_image3',
        array(
            'label' => __('Slide Image 3 (1440x700)','awesomeone'),
            'section' => 'slider_section',
            'settings' => 'slide_image3'
        )
    )
);

	$wp_customize->add_setting('slide_title3',array(
		'default'	=> 'Awesome Features',
		'sanitize_callback'	=> 'sanitize_text_field',
	));

	$wp_customize->add_control('slide_title3',array(
		'label'	=> __('Slide Title 3','awesomeone'),
		'section'	=> 'slider_section',
		'type'	=> 'text'
	));

	$wp_customize->add_setting('slide_desc3',array(
		'default'	=> 'This is description for slide three',
		'sanitize_callback'	=> 'format_for_editor',
	));

	$wp_customize->add_control(
		new WP_Customize_Textarea_Control(
			$wp_customize,
			'slide_desc3',
			array(
				'label' => __('Slide Description 3','awesomeone'),
				'section' => 'slider_section',
				'setting'	=> 'slide_desc3'
			)
		)
	);

	$wp_customize->add_setting('slide_link3',array(
		'default'	=> '#link3',
		'sanitize_callback'	=> 'esc_url_raw',
	));

	$wp_customize->add_control('slide_link3',array(
		'label'	=> __('Slide Link 3','awesomeone'),
		'section'	=> 'slider_section',
		'type'		=> 'text'
	));

	$wp_customize->add_setting('purchaselink',array(
		'default'	=> '#purchaselink',
		'sanitize_callback'	=> 'esc_url_raw',
	));

	$wp_customize->add_control('purchaselink',array(
		'label'	=> __('Add Purchase Button Link.','awesomeone'),
		'section'	=> 'slider_section',
		'type'		=> 'text'
	));

	$wp_customize->add_section('footer_section',array(
		'title'	=> __('Footer Text','awesomeone'),
		'description'	=> __('Add some text for footer like copyright etc.','awesomeone'),
		'priority'	=> null
	));

	$wp_customize->add_setting('Awesomeone_options[credit-info]', array(
			'sanitize_callback' => 'sanitize_text_field',
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
        )
    );
    $wp_customize->add_control( new Awesomeone_Info( $wp_customize, 'cred_section', array(
        'section' => 'footer_section',
		'label'	=> __('To remove credit link upgrade to pro','awesomeone'),
        'settings' => 'Awesomeone_options[credit-info]',
        ) )
    );

	$wp_customize->add_section(
        'theme_layout_sec',
        array(
            'title' => __('Layout Settings (PRO Version)', 'awesomeone'),
            'priority' => null,
            'description' => __('<strong>Layout Settings available in</strong>','awesomeone'). '<a href="'.esc_url(pro_theme_url).'" target="_blank">PRO Version</a>.',
        )
    );
    $wp_customize->add_setting('Awesomeone_options[layout-info]', array(
			'sanitize_callback' => 'sanitize_text_field',
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
        )
    );
    $wp_customize->add_control( new Awesomeone_Info( $wp_customize, 'layout_section', array(
        'section' => 'theme_layout_sec',
        'settings' => 'Awesomeone_options[layout-info]',
        'priority' => null
        ) )
    );

	$wp_customize->add_section(
        'theme_font_sec',
        array(
            'title' => __('Fonts Settings (PRO Version)', 'awesomeone'),
            'priority' => null,
            'description' => __('<strong>Font Settings available in</strong>','awesomeone'). '<a href="'.esc_url(pro_theme_url).'" target="_blank">PRO Version</a>.',
        )
    );
    $wp_customize->add_setting('Awesomeone_options[font-info]', array(
			'sanitize_callback' => 'sanitize_text_field',
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
        )
    );
    $wp_customize->add_control( new Awesomeone_Info( $wp_customize, 'font_section', array(
        'section' => 'theme_font_sec',
        'settings' => 'Awesomeone_options[font-info]',
        'priority' => null
        ) )
    );

    $wp_customize->add_section(
        'theme_doc_sec',
        array(
            'title' => __('Documentation &amp; Support', 'awesomeone'),
            'priority' => null,
            'description' => __('For documentation and support check this link :','awesomeone'). '<a href="'.esc_url(theme_doc).'" target="_blank">Awesomeone Documentation</a>',
        )
    );
    $wp_customize->add_setting('Awesomeone_options[info]', array(
			'sanitize_callback' => 'sanitize_text_field',
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
        )
    );
    $wp_customize->add_control( new Awesomeone_Info( $wp_customize, 'doc_section', array(
        'section' => 'theme_doc_sec',
        'settings' => 'Awesomeone_options[info]',
        'priority' => 10
        ) )
    );


}
add_action( 'customize_register', 'awesomeone_customize_register' );

//Integer
function awesomeone_sanitize_integer( $input ) {
    if( is_numeric( $input ) ) {
        return intval( $input );
    }
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function awesomeone_customize_preview_js() {
	wp_enqueue_script( 'awesomeone_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'awesomeone_customize_preview_js' );

function awesomeone_css(){
		?>
        <style>
				.social_icons h5,
				.social_icons a,
				a,
				.tm_client strong,
				#footer a,
				#footer ul li:hover a,
				#footer ul li.current_page_item a,
				.postmeta a:hover,
				#sidebar ul li a:hover,
				.blog-post h3.entry-title,
				.woocommerce ul.products li.product .price{
					color:<?php echo get_theme_mod('color_scheme','#0fa5d9'); ?>;
				}
				a.read-more, a.blog-more,
				.pagination ul li .current,
				.pagination ul li a:hover,
				#commentform input#submit,
				input.search-submit{
					background-color:<?php echo get_theme_mod('color_scheme','#0fa5d9'); ?>;
				}
		</style>
	<?php }
add_action('wp_head','awesomeone_css');

function awesomeone_custom_customize_enqueue() {
	wp_enqueue_script( 'awesomeone-custom-customize', get_template_directory_uri() . '/js/custom.customize.js', array( 'jquery', 'customize-controls' ), false, true );
}
add_action( 'customize_controls_enqueue_scripts', 'awesomeone_custom_customize_enqueue' );
