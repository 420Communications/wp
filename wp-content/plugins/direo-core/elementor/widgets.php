<?php
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

//Heading Pro
class Direo_Heading extends Widget_Base
{

    public function get_name()
    {
        return 'heading_pro section-title';
    }

    public function get_title()
    {
        return __('Heading Pro', 'direo-core');
    }

    public function get_icon()
    {
        return 'eicon-t-letter';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['heading', 'pro', 'Heading Pro'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'heading_pro',
            [
                'label' => __('Title & Subtitle', 'direo-core'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label'       => __('Title', 'direo-core'),
                'type'        => Controls_Manager::TEXTAREA,
                'placeholder' => __('Enter your title', 'direo-core'),
                'default'     => __('Add Your Heading Text Here', 'direo-core'),
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __('Link', 'direo-core'),
                'type'  => Controls_Manager::URL,
            ]
        );

        $this->add_control(
            'header_size',
            [
                'label'   => __('HTML Tag', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'h1'   => 'H1',
                    'h2'   => 'H2',
                    'h3'   => 'H3',
                    'h4'   => 'H4',
                    'h5'   => 'H5',
                    'h6'   => 'H6',
                    'div'  => 'div',
                    'span' => 'span',
                    'p'    => 'p',
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label'       => __('Subtitle', 'direo-core'),
                'type'        => Controls_Manager::TEXTAREA,
                'placeholder' => __('Enter your subtitle', 'direo-core'),
                'default'     => __('Add Your subtitle Text Here', 'direo-core'),
            ]
        );

        $this->add_control(
            'align',
            [
                'label'   => __('Alignment', 'direo-core'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'direo-core'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'direo-core'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'direo-core'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'direo-core'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .section-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'  => __('Title  Color', 'direo-core'),
                'type'   => Controls_Manager::COLOR,
                'scheme' => [
                    'type'  => Schemes\Color::get_type(),
                    'value' => Schemes\Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} h1, {{WRAPPER}} h2, {{WRAPPER}} h3, {{WRAPPER}} h4, {{WRAPPER}} h5, {{WRAPPER}} h6' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label'  => __('Subtitle  Color', 'direo-core'),
                'type'   => Controls_Manager::COLOR,
                'scheme' => [
                    'type'  => Schemes\Color::get_type(),
                    'value' => Schemes\Color::COLOR_4,
                ],
                'selectors' => [
                    '{{WRAPPER}} p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings   = $this->get_settings_for_display();
        $title      = $settings['title'];
        $subtitle   = $settings['subtitle'] ? '<p>' . $settings['subtitle'] . '</p>' : '';
        $header     = $settings['header_size'];
        $link       = $settings['link']['url'];
        $target     = $settings['link']['is_external'] ? ' target="_blank"' : '';
        $nofollow   = $settings['link']['nofollow'] ? 'rel="nofollow"' : '';
        $title_attr = $settings['link']['custom_attributes'];

        if ($link) {
            $title = sprintf('<a href="%s" %s %s title="%s" >%s</a>', $link, $target, $nofollow, $title_attr, $title);
        }

        echo sprintf('<div class="section-title"> <%1$s> %2$s </%1$s> %3$s</div>', $header, $title, $subtitle);
    }
}

//Accordion
class Direo_Accordion extends Widget_Base
{

    public function get_name()
    {
        return 'accordion';
    }

    public function get_title()
    {
        return __('Faq', 'direo-core');
    }

    public function get_icon()
    {
        return 'eicon-accordion';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['accordion', 'tabs', 'faq'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_title',
            [
                'label' => __('Faq', 'direo-core'),
            ]
        );

        $this->add_control(
            'element_title',
            [
                'label'       => __('Element title', 'direo-core'),
                'type'        => Controls_Manager::TEXTAREA,
                'placeholder' => __('Enter element title', 'direo-core'),
                'default'     => __("Listing FAQ's", 'direo-core'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'tab_title',
            [
                'label'       => __('Title & Content', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'default'     => __('Accordion Title', 'direo-core'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'tab_content',
            [
                'label'      => __('Content', 'direo-core'),
                'type'       => Controls_Manager::TEXTAREA,
                'default'    => __('Accordion Content', 'direo-core'),
                'show_label' => false,
            ]
        );

        $this->add_control(
            'tabs',
            [
                'label'   => __('Accordion Items', 'direo-core'),
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'tab_title'   => __('Title', 'direo-core'),
                        'tab_content' => __('Content description', 'direo-core'),
                    ],
                ],
                'title_field' => '{{{ tab_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_title',
            [
                'label' => __('Title', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => __('Color', 'direo-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '.atbdp-accordion .dacc_single h3 a' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type'  => Schemes\Color::get_type(),
                    'value' => Schemes\Color::COLOR_3,
                ],
            ]
        );

        $this->add_control(
            'tab_active_color',
            [
                'label'     => __('Active Color', 'direo-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '.atbdp-accordion .dacc_single h3 a.active' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type'  => Schemes\Color::get_type(),
                    'value' => Schemes\Color::COLOR_2,
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_content',
            [
                'label' => __('Content', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label'     => __('Color', 'direo-core'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '.atbdp-accordion .dacc_single .dac_body' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type'  => Schemes\Color::get_type(),
                    'value' => Schemes\Color::COLOR_3,
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings      = $this->get_settings_for_display();
        $accordions    = $settings['tabs'];
        $section_title = $settings['element_title']; ?>
        <div class="faq-contents">
            <div class="atbd_content_module atbd_faqs_module">
                <?php if ($section_title) { ?>
                    <div class="atbd_content_module__tittle_area">
                        <div class="atbd_area_title">
                            <h4>
                                <span class="la la-question-circle"></span>
                                <?php echo esc_attr($section_title); ?>
                            </h4>
                        </div>
                    </div>
                <?php
                } ?>
                <div class="atbdb_content_module_contents">
                    <div class="atbdp-accordion direo_accordion">
                        <?php
                        if ($accordions) {
                            foreach ($accordions as $accordion) {
                                $title = $accordion['tab_title'];
                                $desc  = $accordion['tab_content']; ?>
                                <div class="dacc_single">
                                    <h3 class="faq-title">
                                        <a href="#"><?php echo esc_attr($title); ?></a>
                                    </h3>
                                    <p class="dac_body"><?php echo esc_attr($desc); ?></p>
                                </div>
                                <?php
                            }
                            wp_reset_postdata();
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}

//Add listing form
class Direo_AddListing_Form extends Widget_Base
{
    public function get_name()
    {
        return 'add_listing_form';
    }

    public function get_title()
    {
        return __('Add Listing Form', 'direo-core');
    }

    public function get_icon()
    {
        return 'eicon-post-excerpt';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['Listing form', 'form', 'add listing'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'add_listing_form',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'form_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        echo do_shortcode('[directorist_add_listing]');
    }
}

//Author Profile
class Direo_Profile extends Widget_Base
{
    public function get_name()
    {
        return 'author_profile';
    }

    public function get_title()
    {
        return __('Author Profile', 'direo-core');
    }

    public function get_icon()
    {
        return 'eicon-site-identity';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['profile', 'author', 'author profile'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'author_profile',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'profile_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'profile_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    { ?>
        <div class="direo-author">
            <?php echo do_shortcode('[directorist_author_profile]'); ?>
        </div>
    <?php
    }
}

//Blog Posts
class Direo_Blogs extends Widget_Base
{
    public function get_name()
    {
        return 'blog_posts';
    }

    public function get_title()
    {
        return __('Blogs', 'direo-core');
    }

    public function get_icon()
    {
        return 'eicon-post';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['blog', 'post', 'blog post'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'blog_posts',
            [
                'label' => __('Blog Posts', 'direo-core'),
            ]
        );

        $this->add_control(
            'excerpt',
            [
                'label'   => __('Show Excerpt?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'date',
            [
                'label'   => __('Show Date?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'time',
            [
                'label'   => __('Show Reading Time?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'categories',
            [
                'label'   => __('Show Categories?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'post_count',
            [
                'label'   => __('Number of Posts to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'step'    => 1,
                'default' => 3,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'ID'            => esc_html__(' Post ID', 'direo-core'),
                    'author'        => esc_html__(' Author', 'direo-core'),
                    'title'         => esc_html__(' Title', 'direo-core'),
                    'name'          => esc_html__(' Post name (post slug)', 'direo-core'),
                    'type'          => esc_html__(' Post type (available since Version 4.0)', 'direo-core'),
                    'date'          => esc_html__(' Date', 'direo-core'),
                    'modified'      => esc_html__(' Last modified date', 'direo-core'),
                    'rand'          => esc_html__(' Random order', 'direo-core'),
                    'comment_count' => esc_html__(' Number of comments', 'direo-core')
                ],
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Order post', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC'  => esc_html__(' ASC', 'direo-core'),
                    'DESC' => esc_html__(' DESC', 'direo-core'),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings   = $this->get_settings_for_display();
        $excerpt = $settings['excerpt'];
        $date = $settings['date'];
        $time = $settings['time'];
        $categories = $settings['categories'];
        $post_count = $settings['post_count'];
        $order_by   = $settings['order_by'];
        $order_list = $settings['order_list'];

        $id = get_the_id();
        $content = reading_time(get_post_field('post_content', $id));
        $time_text = (1 == $content) ? $content . ' min read' : $content . ' mins read';

        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => esc_attr($post_count),
            'order'          => esc_attr($order_list),
            'orderby '       => esc_attr($order_by)
        );

        $posts = new WP_Query($args); ?>
        <div class="blog-posts row" data-uk-grid>
            <?php while ($posts->have_posts()) {
                $posts->the_post(); ?>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="blog-posts__single">
                        <?php the_post_thumbnail('direo_blog_grid'); ?>
                        <div class="blog-posts__single__contents">
                            <?php the_title(sprintf('<h4><a href="%s">', get_the_permalink()), '</a></h4>'); ?>
                            <ul>
                                <li><?php echo $date && function_exists('direo_time_link') ? direo_time_link() : ''; ?></li>
                                <li><?php echo $time ? $time_text : ''; ?></li>
                                <?php $categories && function_exists('direo_post_cats') ? direo_post_cats() : ''; ?>
                            </ul>
                            <?php $excerpt ? the_excerpt() : ''; ?>
                        </div>
                    </div>
                </div>
            <?php
            }
            wp_reset_postdata(); ?>
        </div>
    <?php
    }
}

//Categories
class Direo_Categories extends Widget_Base
{
    public function get_name()
    {
        return 'categories';
    }

    public function get_title()
    {
        return __('Listing Categories', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-theme-builder';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['categories', 'all categories', 'listing categories'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'categories',
            [
                'label' => __('Listing categories', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
                'condition' => [
                    'cat_type!' => ['style3'],
                ]
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
                'condition' => [
                    'cat_type!' => ['style3'],
                ]
            ]
        );

        $this->add_control(
            'cat_type',
            [
                'label'   => __('Style', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'category-style1',
                'options' => [
                    'category-style1'    => esc_html__('Style 1', 'direo-core'),
                    'category-style-two' => esc_html__('Style 2', 'direo-core'),
                    'style3'             => esc_html__('Style 3', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'row',
            [
                'label'   => esc_html__('Categories Per Row', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '6' => esc_html__('6 Items / Row', 'direo-core'),
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
                'condition' => [
                    'cat_type!' => 'style3'
                ]
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of categories to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 1000,
                'step'    => 1,
                'default' => 6,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => esc_html__('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'id',
                'options' => [
                    'id'    => esc_html__(' Cat ID', 'direo-core'),
                    'count' => esc_html__('Listing Count', 'direo-core'),
                    'name'  => esc_html__('Category name (A-Z)', 'direo-core'),
                    'slug'  => esc_html__('Select Category', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'slug',
            [
                'label'     => esc_html__('Select Categories', 'direo-core'),
                'type'      => Controls_Manager::SELECT2,
                'multiple'  => true,
                'options'   => function_exists('direo_listing_category') ? direo_listing_category() : [],
                'condition' => [
                    'order_by' => 'slug'
                ]
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => esc_html__('Categories Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => array(
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        if (!class_exists('Directorist_Base')) {
            return;
        }
        $settings   = $this->get_settings_for_display();
        $cat_type   = $settings['cat_type'];

        if ('style3' === $cat_type) {
            az_template('/elementor/cat/view2', $settings);
        } else {
            az_template('/elementor/cat/view1', $settings);
        }
    }
}

//Locations
class Direo_Locations extends Widget_Base
{
    public function get_name()
    {
        return 'locations';
    }

    public function get_title()
    {
        return __('Listing Locations', 'direo-core');
    }

    public function get_icon()
    {
        return 'eicon-map-pin';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['locations', 'all location', 'listing locations'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'locations',
            [
                'label' => __('Listing Locations', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
                'condition' => [
                    'layout' => ['grid','list'],
                ]
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
                'condition' => [
                    'layout' => ['grid','list'],
                ]
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('Style', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid View', 'direo-core'),
                    'list' => esc_html__('List View', 'direo-core'),
                    'masonry' => esc_html__('Masonry View', 'direo-core'),
                    'carousel' => esc_html__('Carousel View', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'row',
            [
                'label'   => __('Locations Per Row', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => ['carousel','masonry'],
                ]
            ]
        );

        $this->add_control(
            'number_loc',
            [
                'label'   => __('Number of Locations to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 1000,
                'step'    => 1,
                'default' => 4,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'id',
                'options' => [
                    'id'    => esc_html__('Location ID', 'direo-core'),
                    'count' => esc_html__('Listing Count', 'direo-core'),
                    'name'  => esc_html__(' Location name (A-Z)', 'direo-core'),
                    'slug'  => esc_html__('Select Location', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'slug',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : [],
                'condition' => [
                    'order_by' => 'slug',
                ]
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Locations Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings   = $this->get_settings_for_display();
        $layout     = $settings['layout'];

        if ('masonry' === $layout) {
            az_template('/elementor/location/view2', $settings);
        } elseif ('carousel' === $layout) {
            az_template('/elementor/location/view3', $settings);
        } else {
            az_template('/elementor/location/view1', $settings);
        }
    }
}

//Checkout
class Direo_Checkout extends Widget_Base
{
    public function get_name()
    {
        return 'checkout';
    }

    public function get_title()
    {
        return __('Checkout', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-product-price';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['checkout', 'payment', 'checkout payment'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'checkout',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'checkout_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'checkout_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    { ?>
        <div class="direo-directorist_checkout">
            <?php echo do_shortcode('[directorist_checkout]'); ?>
        </div>
        <?php
    }
}

//Contact form 7
class Direo_ContactForm extends Widget_Base
{
    public function get_name()
    {
        return 'contact_form';
    }

    public function get_title()
    {
        return __('Contact Form', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-form-horizontal';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['contact', 'form', 'contact form'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'contact_form',
            [
                'label' => __('Contact Form', 'direo-core'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label'       => __('Form Title', 'direo-core'),
                'type'        => Controls_Manager::TEXTAREA,
                'placeholder' => __('Contact Form', 'direo-core'),
            ]
        );

        $this->add_control(
            'contact_form_id',
            [
                'label'   => __('Select Contact Form', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'options' => function_exists('mp_get_cf7_names') ? mp_get_cf7_names() : [],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $title           = $settings['title'];
        $contact_form_id = $settings['contact_form_id'];

        if ($contact_form_id) { ?>
            <div class="widget atbd_widget widget-card contact-block">
                <?php if ($title) { ?>
                    <div class="atbd_widget_title">
                        <h4><span class="la la-envelope"></span><?php echo esc_attr($title); ?></h4>
                    </div>
                <?php
                } ?>
                <div class="atbdp-widget-listing-contact contact-form">
                    <?php echo do_shortcode('[contact-form-7 id="' . intval(esc_attr($contact_form_id)) . '" ]'); ?>
                </div>
            </div>

        <?php
        }
    }
}

//Contact items
class Direo_ContactItems extends Widget_Base
{
    public function get_name()
    {
        return 'contact_items';
    }

    public function get_title()
    {
        return __('Contact Items', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-bullet-list';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['address', 'list', 'item', 'contact items'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'contact_items',
            [
                'label' => __('Contact Items', 'direo-core'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label'       => __('Element Title', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __('Enter element title', 'direo-core'),
                'default'     => 'Contact Info'
            ]
        );

        $this->add_control(
            'items',
            [
                'label'       => __('Add New Items', 'direo-core'),
                'type'        => Controls_Manager::REPEATER,
                'title_field' => '{{{ title }}}',
                'fields'      => [
                    [
                        'name'    => 'icon',
                        'label'   => __('Font-Awesome', 'direo-core'),
                        'type'    => Controls_Manager::ICON,
                        'default' => 'fa fa-map-marker',
                    ],
                    [
                        'name'    => 'title',
                        'label'   => __('Content', 'direo-core'),
                        'type'    => Controls_Manager::TEXTAREA,
                        'default' => __('Enter your address', 'direo-core'),
                    ],

                ],
            ]
        );

        $this->add_control(
            'socials',
            [
                'label'       => __('Add New Items', 'direo-core'),
                'type'        => Controls_Manager::REPEATER,
                'title_field' => '{{{ icon }}}',
                'fields'      => [
                    [
                        'name'    => 'icon',
                        'label'   => __('Font-Awesome', 'direo-core'),
                        'type'    => Controls_Manager::ICON,
                        'default' => 'fa fa-map-marker',
                    ],
                    [
                        'name'        => 'url',
                        'type'        => Controls_Manager::URL,
                        'description' => __('Enter your social profile url', 'direo-core'),
                    ],

                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        az_template('/elementor/contact/view', $settings);
    }
}

//Counter
class Direo_Counter extends Widget_Base
{
    public function get_name()
    {
        return 'counter';
    }

    public function get_title()
    {
        return __('Counter', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-counter';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['count', 'counter', 'count down'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_counter',
            [
                'label' => __('Counter', 'direo-core'),
            ]
        );

        $this->add_control(
            'number',
            [
                'label'   => __('Number', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 0,
            ]
        );

        $this->add_control(
            'suffix',
            [
                'label' => __('Number Suffix', 'direo-core'),
                'type'  => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'label',
            [
                'label' => __('Title', 'direo-core'),
                'type'  => Controls_Manager::TEXT,
            ]
        );


        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $number   = $settings['number'];
        $suffix   = $settings['suffix'];
        $title    = $settings['label']; ?>
        <div class="list-unstyled counter-items">
            <div>
                <p>
                    <span class="count_up"><?php echo esc_attr($number); ?></span>
                    <?php echo esc_attr($suffix); ?>
                </p>
                <span><?php echo esc_attr($title); ?></span>
            </div>
        </div>
        <?php
    }
}

//Dashboard
class Direo_Dashboard extends Widget_Base
{
    public function get_name()
    {
        return 'dashboard';
    }

    public function get_title()
    {
        return __('Author Dashboard', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-dashboard';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['dashboard', 'author dashboard'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'dashboard',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'dashboard_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dashboard_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        echo do_shortcode('[directorist_user_dashboard]');
    }
}

//Feature Box
class Direo_FeatureBox extends Widget_Base
{
    public function get_name()
    {
        return 'feature_box';
    }

    public function get_title()
    {
        return __('Feature List', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-post-list';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['feature', 'feature list', 'feature box'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'feature_box',
            [
                'label' => __('Feature List', 'direo-core'),
            ]
        );

        $this->add_control(
            'type',
            [
                'label'   => __('Type', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'icon',
                'options' => [
                    'icon'   => esc_html__('Icon Type', 'direo-core'),
                    'number' => esc_html__('Number Type', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'feature_style',
            [
                'label'   => __('Chose Style', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'kcel-feature-boxes feature-list-wrapper',
                'options' => [
                    'kcel-feature-boxes feature-list-wrapper' => esc_html__('Style 1', 'direo-core'),
                    'feature-box-wrapper'                     => esc_html__('Style 2', 'direo-core'),
                ],
                'condition' => [
                    'type' => 'icon'
                ]
            ]
        );

        $this->add_control(
            'icon',
            [
                'label'     => __('Font-Awesome', 'direo-core'),
                'type'      => Controls_Manager::ICON,
                'default'   => 'la la-check-circle',
                'condition' => [
                    'type' => 'icon'
                ]
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Title', 'direo-core'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Feature title'
            ]
        );

        $this->add_control(
            'desc',
            [
                'label'   => __('Description', 'direo-core'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => 'Feature description',
            ]
        );

        $this->add_control(
            'number',
            [
                'label'     => __('Feature Number', 'direo-core'),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 1,
                'max'       => 10,
                'step'      => 1,
                'default'   => 1,
                'condition' => [
                    'type' => 'number'
                ]
            ]
        );

        $this->end_controls_section();

        //Style section
        $this->start_controls_section(
            'feature_box_style',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'  => __('Icon  Color', 'direo-core'),
                'type'   => Controls_Manager::COLOR,
                'scheme' => [
                    'type'  => Schemes\Color::get_type(),
                    'value' => Schemes\Color::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .list-unstyled .circle-secondary, {{WRAPPER}} .list-unstyled .list-count span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_bg_color',
            [
                'label'  => __('Icon Background  Color', 'direo-core'),
                'type'   => Controls_Manager::COLOR,
                'scheme' => [
                    'type'  => Schemes\Color::get_type(),
                    'value' => Schemes\Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .kcel-feature-boxes .circle-secondary, {{WRAPPER}} .feature-box-wrapper .icon' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings      = $this->get_settings_for_display();
        $type          = $settings['type'];
        $feature_style = 'list-unstyled ' . $settings['feature_style'];
        $icon          = $settings['icon'];
        $title         = $settings['title'];
        $desc          = $settings['desc'];
        $number        = $settings['number'];

        if ('icon' == $type) { ?>
            <ul class="<?php echo esc_attr($feature_style); ?>">
                <li>
                    <div class="icon">
                        <span class="circle-secondary">
                            <i class="la <?php echo esc_attr($icon); ?>"></i>
                        </span>
                    </div>
                    <div class="list-content">
                        <h4><?php echo esc_attr($title) ?></h4>
                        <p><?php echo esc_attr($desc) ?></p>
                    </div>
                </li>
            </ul>
        <?php
        } else { ?>
            <ul class="<?php echo esc_attr($feature_style); ?> list-features p-top-15">
                <li>
                    <div class="list-count">
                        <span><?php echo esc_attr($number); ?></span>
                    </div>
                    <div class="list-content">
                        <h4><?php echo esc_attr($title) ?></h4>
                        <p><?php echo esc_attr($desc) ?></p>
                    </div>
                </li>
            </ul>
        <?php
        }
    }
}

//Listings
class Direo_Listings extends Widget_Base
{
    public function get_name()
    {
        return 'listings';
    }

    public function get_title()
    {
        return __('All Listings', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['listings', 'all listings'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'listings',
            [
                'label' => __('Listings', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid View', 'direo-core'),
                    'list' => esc_html__('List View', 'direo-core'),
                    'map'  => esc_html__('Map View', 'direo-core'),
                    'carousel'  => esc_html__('Carousel View', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'zoom_level',
            [
                'label'   => __('Map Zoom Level', 'direo-core'),
                'type'    => Controls_Manager::SLIDER ,
                'range' => [
					'px' => [
						'min' => 1,
						'max' => 18,
						'step' => 1,
					],
                ],
                'default' => [
					'unit' => 'px',
					'size' => 10,
				],
                'condition' => [
                    'layout' => 'map'
                ]
            ]
        );
        
        $this->add_control(
            'header',
            [
                'label'   => __('Show Header?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
                'condition' => [
                    'layout!' => 'carousel'
                ]
            ]
        );

        $this->add_control(
            'title',
            [
                'label'       => __('Listings Found Text', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'default'     => __('Listings Found', 'direo-core'),
                'label_block' => true,
                'condition'   => [
                    'header' => 'yes',
                    'layout!' => 'carousel'
                ]
            ]
        );

        $this->add_control(
            'section_title',
            [
                'label'       => __('Section Title', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'condition'   => [
                    'layout' => 'carousel'
                ]

            ]
        );

        $this->add_control(
            'view_more_label',
            [
                'label'       => __('View More Label', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'condition'   => [
                    'layout' => 'carousel'
                ]
            ]
        );

        $this->add_control(
            'view_more_url',
            [
                'label'       => __('View More URL', 'direo-core'),
                'type'        => Controls_Manager::URL,
                'label_block' => true,
                'condition'   => [
                    'layout' => 'carousel'
                ]
            ]
        );

        $this->add_control(
            'filter',
            [
                'label'     => __('Show Filter Button?', 'direo-core'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no',
                'condition' => [
                    'header' => 'yes',
                    'layout!' => 'carousel'
                ]
            ]
        );

        $this->add_control(
            'sidebar',
            [
                'label'     => __('Show sidebar?', 'direo-core'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no',
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'row',
            [
                'label'   => __('Listings Per Row', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
                'condition' => [
                    'layout' => 'grid',
                ]
            ]
        );

        $this->add_control(
            'map_height',
            [
                'label'     => __('Map Height', 'direo-core'),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 300,
                'max'       => 1980,
                'default'   => 500,
                'condition' => [
                    'layout' => 'map'
                ]
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 6,
            ]
        );

        $this->add_control(
            'cat',
            [
                'label'    => __('Specify Categories', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_category') ? direo_listing_category() : [],
            ]
        );

        $this->add_control(
            'tag',
            [
                'label'    => __('Specify Tags', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_tags') ? direo_listing_tags() : []
            ]
        );

        $this->add_control(
            'location',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );

        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
                'condition' => [
                    'layout!' => 'carousel'
                ]
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings   = $this->get_settings_for_display();
        az_template('/elementor/listings/view', $settings);
    }
}

//Listings Carousel
class Direo_ListingsCarousel extends Widget_Base
{
    public function get_name()
    {
        return 'listings_carousel';
    }

    public function get_title()
    {
        return __('Listings Carousel', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-posts-carousel';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['carousel', 'listing carousel'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'listings_carousel',
            [
                'label' => __('Listings Carousel', 'direo-core'),
            ]
        );
        
        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
            ]
        );
        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Listing Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Listing Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        $this->add_control(
            'list_num',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 6,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        az_template('/elementor/listing/view', $settings);
    }
}

//Listings with map
class Direo_ListingsMap extends Widget_Base
{
    public function get_name()
    {
        return 'listings_map';
    }

    public function get_title()
    {
        return __('Listings With Map', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-google-maps';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['map', 'listings map', 'listing map'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'listings_map',
            [
                'label' => __('Listings With Map', 'direo-core'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Listings Found Text', 'direo-core'),
                'type'    => Controls_Manager::TEXT,
                'default' => __('Listings Found', 'direo-core'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '2' => esc_html__('2 Column', 'direo-core'),
                    '3' => esc_html__('3 Column', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 4,
            ]
        );

        $this->add_control(
            'cat',
            [
                'label'    => __('Specify Categories', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_category') ? direo_listing_category() : [],
            ]
        );

        $this->add_control(
            'tag',
            [
                'label'    => __('Specify Tags', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_tags') ? direo_listing_tags() : []
            ]
        );

        $this->add_control(
            'location',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );

        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $show_pagination = $settings['show_pagination'];
        $title           = $settings['title'];
        $layout          = $settings['layout'];
        $number_cat      = $settings['number_cat'];
        $cat             = $settings['cat'] ? implode(',', $settings['cat']) : '';
        $location        = $settings['location'] ? implode(',', $settings['location']) : '';
        $tag             = $settings['tag'] ? implode(',', $settings['tag']) : '';
        $featured        = $settings['featured'];
        $popular         = $settings['popular'];
        $order_by        = $settings['order_by'];
        $order_list      = $settings['order_list'];
        $user            = $settings['user'];
        $web             = 'yes' == $user ? $settings['link']['url'] : ''; ?>

        <input type="hidden" id="listing-listings_with_map">

    <?php echo do_shortcode('[directorist_all_listing view="listings_with_map" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" listings_per_page="' . esc_attr($number_cat) . '" category="' . esc_attr($cat) . '" tag="' . esc_attr($tag) . '" location="' . esc_attr($location) . '" featured_only="' . esc_attr($featured) . '" action_before_after_loop="no" popular_only="' . esc_attr($popular) . '" header="yes" header_title ="' . esc_attr($title) . '" show_pagination="' . esc_attr($show_pagination) . '" display_preview_image="yes" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" listings_with_map_columns="' . esc_attr($layout) . '"]');
    }
}

//Registration
class Direo_Registration extends Widget_Base
{
    public function get_name()
    {
        return 'registration';
    }

    public function get_title()
    {
        return __('Registration Form', 'direo-core');
    }

    public function get_icon()
    {
        return ' fas fa-user-plus';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'Registration',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'Registration_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'Registration_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    { ?>
        <div class="direo-directorist_custom_registration">
            <?php echo do_shortcode('[directorist_custom_registration]'); ?>
        </div>
    <?php
    }
}

//Login
class Direo_Login extends Widget_Base
{
    public function get_name()
    {
        return 'login';
    }

    public function get_title()
    {
        return __('Login Form', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-lock-user';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'login',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'login_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'login_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    { ?>
        <div class="direo-directorist_user_login">
            <?php echo do_shortcode('[directorist_user_login]'); ?>
        </div>
    <?php
    }
}

//Transaction
class Direo_Transaction extends Widget_Base
{
    public function get_name()
    {
        return 'transaction';
    }

    public function get_title()
    {
        return __('Transaction', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-sync';
    }

    public function get_keywords()
    {
        return ['transaction'];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }


    protected function register_controls()
    {
        $this->start_controls_section(
            'transaction',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'transaction_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'transaction_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    { ?>
        <div class="direo-directorist_transaction_failure">
            <?php echo do_shortcode('[directorist_transaction_failure]'); ?>
        </div>
    <?php
    }
}

//Logos
class Direo_Logos extends Widget_Base
{
    public function get_name()
    {
        return 'logos';
    }

    public function get_title()
    {
        return __('Logos Carousel', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-carousel';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['logo', 'logos', 'carousel',];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'logos',
            [
                'label' => __('Logos', 'direo-core'),
            ]
        );

        $this->add_control(
            'clients_logo',
            [
                'label'   => __('Add Logos', 'direo-core'),
                'type'    => Controls_Manager::GALLERY,
                'default' => [],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $logos    = $settings['clients_logo']; ?>
        <div class="logo-carousel owl-carousel">
            <?php
            if ($logos) {
                foreach ($logos as $logo) { ?>
                    <div class="carousel-single">
                        <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo function_exists('direo_get_image_alt') ? direo_get_image_alt($logo['id']) : ''; ?>">
                    </div>
            <?php
                }
                wp_reset_postdata();
            } ?>
        </div>
        <?php
    }
}

//Payment
class Direo_Payment extends Widget_Base
{
    public function get_name()
    {
        return 'payment';
    }

    public function get_title()
    {
        return __('Payment', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-product-breadcrumbs';
    }

    public function get_keywords()
    {
        return ['payment',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'payment',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'payment_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'payment_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    { ?>
        <div class="direo-payment-receipt">
            <?php echo do_shortcode('[directorist_payment_receipt]'); ?>
        </div>
        <?php
    }
}

//Pricing plan
class Direo_PricingPlan extends Widget_Base
{
    public function get_name()
    {
        return 'pricing_plan';
    }

    public function get_title()
    {
        return __('Pricing Plan', 'direo-core');
    }

    public function get_icon()
    {
        return ' fas fa-dollar-sign';
    }

    public function get_keywords()
    {
        return ['pricing', 'price',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'pricing_plan',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'pp_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pp_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        echo do_shortcode('[directorist_pricing_plans]');
    }
}

//Hero area
class Direo_SearchForm extends Widget_Base
{

    public function get_name()
    {
        return 'search_form';
    }

    public function get_title()
    {
        return __('Listing Search Form', 'direo-core');
    }

    public function get_icon()
    {
        return 'eicon-site-search';
    }

    public function get_keywords()
    {
        return ['search', 'form', 'listing form'];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'hero_area',
            [
                'label' => __('General', 'direo-core'),
            ]
        );

        $this->add_control(
            'style',
            [
                'label'   => __('Style', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'style1' => __('Style 1', 'direo-core'),
                    'style2' => __('Style 2', 'direo-core'),
                    'style3' => __('Style 3', 'direo-core'),
                ],
                'default' => 'style1',
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
            ]
        );

        $this->add_control(
            'search',
            [
                'label'       => __('Search Button Text', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Search'
            ]
        );

        $this->add_control(
            'more_btn',
            [
                'label'   => __('More Filter Button', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Category?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'hero_style',
            [ 
                'label' => __('Style', 'direo-core'),
            ]
        );

        $this->add_control(
            'popular_cat_color',
            [
                'label'  => __('Color', 'direo-core'),
                'type'   => Controls_Manager::COLOR,
                'scheme' => [
                    'type'  => Schemes\Color::get_type(),
                    'value' => Schemes\Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .directorist-search-contents .directorist-listing-category-top ul li a p,
                    {{WRAPPER}} .directorist-listing-type-selection__link,
                    {{WRAPPER}} .directorist-listing-type-selection__link span.fa' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings      = $this->get_settings_for_display();
        $style         = $settings['style'];
        $search        = $settings['search'];
        $popular_cat       = $settings['popular'];
        $more_btn      = $settings['more_btn'];
        $default_types = $settings['default_types'];
        $types         = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        
        if ('style2' === $style) {
            $class = 'col-md-8 offset-md-2';
        } elseif ('style3' === $style) {
            $class = 'col-lg-6 offset-lg-3';
        } else {
            $class = 'col-md-10 offset-md-1';
        }

        if ( !class_exists('Directorist_Base') ) {
            return;
        } ?>
        <div id="directorist" class="atbd_wrapper directory_search_area ads-advaced--wrapper search-home-<?php echo esc_attr($style); ?>">
            <div class="row">
                <div class="<?php echo esc_attr($class); ?>">
                    <?php echo do_shortcode( '[directorist_search_listing show_title_subtitle="no" search_button="yes" search_button_text="'.$search.'" more_filters_button="'.$more_btn.'" more_filters_text="" more_filters_display="overlapping" directory_type="'.$types.'" default_directory_type="'.$default_types.'" show_popular_category="'.$popular_cat.'"]' );?>
                </div>
            </div>
        </div>
        <?php
    }
}

//Search result
class Direo_SearchResult extends Widget_Base
{
    public function get_name()
    {
        return 'search_result';
    }

    public function get_title()
    {
        return __('Search Result', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-search-results';
    }

    public function get_keywords()
    {
        return ['result', 'search'];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'search_result',
            [
                'label' => __('Search Result', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
            ]
        );
        
        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
            ]
        );

        $this->add_control(
            'header',
            [
                'label'   => __('Show Header?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid View', 'direo-core'),
                    'list' => esc_html__('List View', 'direo-core'),
                    'map'  => esc_html__('Map View', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'row',
            [
                'label'   => __('Listings Per Row', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
                'condition' => [
                    'layout' => 'grid'
                ]
            ]
        );

        $this->add_control(
            'map_height',
            [
                'label'     => __('Map Height', 'direo-core'),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 300,
                'max'       => 1980,
                'default'   => 500,
                'condition' => [
                    'layout' => 'map'
                ]
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $header          = $settings['header'];
        $show_pagination = $settings['show_pagination'];
        $layout          = $settings['layout'];
        $number_cat      = $settings['number_cat'];
        $row             = $settings['row'];
        $order_by        = $settings['order_by'];
        $order_list      = $settings['order_list'];
        $map_height      = $settings['map_height'];
        $user            = $settings['user'];
        $web             = 'yes' == $user ? $settings['link']['url'] : '';

        echo do_shortcode('[directorist_search_result view="' . esc_attr($layout) . '" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" listings_per_page="' . esc_attr($number_cat) . '" header="' . esc_attr($header) . '" columns="' . esc_attr($row) . '" show_pagination="' . esc_attr($show_pagination) . '" map_height="' . $map_height . '" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]');
    }
}

//Listings with map
class Direo_SearchResultMap extends Widget_Base
{
    public function get_name()
    {
        return 'search_result_map';
    }

    public function get_title()
    {
        return __('Search Result Map View', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-google-maps';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['search result map', 'search', 'result map'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'listings_map',
            [
                'label' => __('Search Result Map View', 'direo-core'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '2' => esc_html__('2 Column', 'direo-core'),
                    '3' => esc_html__('3 Column', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 4,
            ]
        );

        $this->add_control(
            'cat',
            [
                'label'    => __('Specify Categories', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_category') ? direo_listing_category() : [],
            ]
        );

        $this->add_control(
            'tag',
            [
                'label'    => __('Specify Tags', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_tags') ? direo_listing_tags() : []
            ]
        );

        $this->add_control(
            'location',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );

        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $show_pagination = $settings['show_pagination'];
        $layout          = $settings['layout'];
        $number_cat      = $settings['number_cat'];
        $cat             = $settings['cat'] ? implode(',', $settings['cat']) : '';
        $location        = $settings['location'] ? implode(',', $settings['location']) : '';
        $tag             = $settings['tag'] ? implode(',', $settings['tag']) : '';
        $featured        = $settings['featured'];
        $popular         = $settings['popular'];
        $order_by        = $settings['order_by'];
        $order_list      = $settings['order_list'];
        $user            = $settings['user'];
        $web             = 'yes' == $user ? $settings['link']['url'] : ''; ?>

        <input type="hidden" id="listing-listings_with_map">

    <?php echo do_shortcode('[directorist_search_result view="listings_with_map" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" listings_per_page="' . esc_attr($number_cat) . '" category="' . esc_attr($cat) . '" tag="' . esc_attr($tag) . '" location="' . esc_attr($location) . '" featured_only="' . esc_attr($featured) . '" action_before_after_loop="no" popular_only="' . esc_attr($popular) . '" header="yes" show_pagination="' . esc_attr($show_pagination) . '" display_preview_image="yes" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" listings_with_map_columns="' . esc_attr($layout) . '"]');
    }
}

//Single category
class Direo_SingleCat extends Widget_Base
{
    public function get_name()
    {
        return 'single_cat';
    }

    public function get_title()
    {
        return __('Single Category', 'direo-core');
    }

    public function get_icon()
    {
        return 'eicon-theme-builder';
    }

    public function get_keywords()
    {
        return ['single category', 'single listing category', 'category',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'single_cat',
            [
                'label' => __('Single Listing Category', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
            ]
        );
        
        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
            ]
        );

        $this->add_control(
            'header',
            [
                'label'   => __('Show Header?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title',
            [
                'label'       => __('Listings Found Text', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'default'     => __('Listings Found', 'direo-core'),
                'label_block' => true,
                'condition'   => [
                    'header' => 'yes'
                ]

            ]
        );

        $this->add_control(
            'filter',
            [
                'label'   => __('Show More Filter?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid View', 'direo-core'),
                    'list' => esc_html__('List View', 'direo-core'),
                    'map'  => esc_html__('Map View', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'sidebar',
            [
                'label'     => __('Show sidebar?', 'direo-core'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'row',
            [
                'label'   => __('Listings Per Row', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
                'condition' => [
                    'layout' => 'grid'
                ]
            ]
        );

        $this->add_control(
            'map_height',
            [
                'label'     => __('Map Height', 'direo-core'),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 300,
                'max'       => 1980,
                'default'   => 500,
                'condition' => [
                    'layout' => 'map'
                ]
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'cat',
            [
                'label'    => __('Specify Categories', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_category') ? direo_listing_category() : [],
            ]
        );

        $this->add_control(
            'tag',
            [
                'label'    => __('Specify Tags', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_tags') ? direo_listing_tags() : []
            ]
        );

        $this->add_control(
            'location',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );

        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $header          = $settings['header'];
        $filter          = 'yes' == $settings['filter'] ? $settings['filter'] : 'no';
        $sidebar         = $settings['sidebar'];
        $show_pagination = $settings['show_pagination'];
        $title           = $settings['title'];
        $layout          = $settings['layout'];
        $number_cat      = $settings['number_cat'];
        $row             = $settings['row'];
        $cat             = $settings['cat'] ? implode(',', $settings['cat']) : '';
        $location        = $settings['location'] ? implode(',', $settings['location']) : '';
        $tag             = $settings['tag'] ? implode(',', $settings['tag']) : '';
        $featured        = $settings['featured'];
        $popular         = $settings['popular'];
        $order_by        = $settings['order_by'];
        $order_list      = $settings['order_list'];
        $map_height      = $settings['map_height'];
        $user            = $settings['user'];
        $web             = 'yes' == $user ? $settings['link']['url'] : '';

        echo do_shortcode('[directorist_category view="' . esc_attr($layout) . '" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" listings_per_page="' . esc_attr($number_cat) . '" category="' . esc_attr($cat) . '" tag="' . esc_attr($tag) . '" location="' . esc_attr($location) . '" featured_only="' . esc_attr($featured) . '" popular_only="' . esc_attr($popular) . '" header="' . esc_attr($header) . '" header_title ="' . esc_attr($title) . '" columns="' . esc_attr($row) . '" action_before_after_loop="' . esc_attr($sidebar) . '" show_pagination="' . esc_attr($show_pagination) . '" advanced_filter="' . esc_attr($filter) . '" map_height="' . $map_height . '" display_preview_image="yes" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]');
    }
}

//Single category map
class Direo_SingleCatMap extends Widget_Base
{
    public function get_name()
    {
        return 'single_cat_map';
    }

    public function get_title()
    {
        return __('Single Category Map View', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-google-maps';
    }

    public function get_keywords()
    {
        return ['map', 'single category'];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'single_cat',
            [
                'label' => __('Single Category Map View', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
            ]
        );
        
        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Listings Found Text', 'direo-core'),
                'type'    => Controls_Manager::TEXT,
                'default' => __('Listings Found', 'direo-core'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '2' => esc_html__('2 Column', 'direo-core'),
                    '3' => esc_html__('3 Column', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'cat',
            [
                'label'    => __('Specify Categories', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_category') ? direo_listing_category() : [],
            ]
        );
        $this->add_control(
            'loc',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );
        $this->add_control(
            'tag',
            [
                'label'    => __('Specify Tags', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_tags') ? direo_listing_tags() : []
            ]
        );

        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $default_types   = $settings['default_types'];
        $types           = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $show_pagination = $settings['show_pagination'];
        $title           = $settings['title'];
        $cat             = $settings['cat'] ? implode(',', $settings['cat']) : '';
        $location        = $settings['loc'] ? implode(',', $settings['loc']) : '';
        $tag             = $settings['tag'] ? implode(',', $settings['tag']) : '';
        $popular         = $settings['popular'];
        $featured        = $settings['featured'];
        $layout          = $settings['layout'];
        $number_cat      = $settings['number_cat'];
        $order_by        = $settings['order_by'];
        $order_list      = $settings['order_list'];
        $user            = $settings['user'];
        $web             = 'yes' == $user ? $settings['link']['url'] : ''; ?>

    <input type="hidden" id="listing-listings_with_map">

    <?php echo do_shortcode('[directorist_category view="listings_with_map" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" listings_per_page="' . esc_attr($number_cat) . '" category="' . esc_attr($cat) . '" tag="' . esc_attr($tag) . '" location="' . esc_attr($location) . '" featured_only="' . esc_attr($featured) . '" action_before_after_loop="no" popular_only="' . esc_attr($popular) . '" header="yes" header_title ="' . esc_attr($title) . '" show_pagination="' . esc_attr($show_pagination) . '" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" listings_with_map_columns="' . esc_attr($layout) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]');
    }
}

//Single location
class Direo_SingleLoc extends Widget_Base
{
    public function get_name()
    {
        return 'single_loc';
    }

    public function get_title()
    {
        return __('Single Location', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-map-pin';
    }

    public function get_keywords()
    {
        return ['single location', 'need location', 'location',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'single_loc',
            [
                'label' => __('Single Listing Location', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
            ]
        );
        
        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
            ]
        );

        $this->add_control(
            'header',
            [
                'label'   => __('Show Header?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title',
            [
                'label'       => __('Listings Found Text', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'default'     => __('Listings Found', 'direo-core'),
                'label_block' => true,
                'condition'   => [
                    'header' => 'yes'
                ]

            ]
        );

        $this->add_control(
            'filter',
            [
                'label'   => __('Show More Filter?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid View', 'direo-core'),
                    'list' => esc_html__('List View', 'direo-core'),
                    'map'  => esc_html__('Map View', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'sidebar',
            [
                'label'     => __('Show sidebar?', 'direo-core'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'no',
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'row',
            [
                'label'   => __('Listings Per Row', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
                'condition' => [
                    'layout' => 'grid'
                ]
            ]
        );

        $this->add_control(
            'map_height',
            [
                'label'     => __('Map Height', 'direo-core'),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 300,
                'max'       => 1980,
                'default'   => 500,
                'condition' => [
                    'layout' => 'map'
                ]
            ]

        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'cat',
            [
                'label'    => __('Specify Categories', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_category') ? direo_listing_category() : [],
            ]
        );

        $this->add_control(
            'tag',
            [
                'label'    => __('Specify Tags', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_tags') ? direo_listing_tags() : []
            ]
        );

        $this->add_control(
            'location',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );

        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $header          = $settings['header'];
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $filter          = 'yes' == $settings['filter'] ? $settings['filter'] : 'no';
        $sidebar         = $settings['sidebar'];
        $show_pagination = $settings['show_pagination'];
        $title           = $settings['title'];
        $layout          = $settings['layout'];
        $number_cat      = $settings['number_cat'];
        $row             = $settings['row'];
        $cat             = $settings['cat'] ? implode(',', $settings['cat']) : '';
        $location        = $settings['location'] ? implode(',', $settings['location']) : '';
        $tag             = $settings['tag'] ? implode(',', $settings['tag']) : '';
        $featured        = $settings['featured'];
        $popular         = $settings['popular'];
        $order_by        = $settings['order_by'];
        $order_list      = $settings['order_list'];
        $map_height      = $settings['map_height'];
        $user            = $settings['user'];
        $web             = 'yes' == $user ? $settings['link']['url'] : '';

        echo do_shortcode('[directorist_location view="' . esc_attr($layout) . '" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" listings_per_page="' . esc_attr($number_cat) . '" category="' . esc_attr($cat) . '" tag="' . esc_attr($tag) . '" location="' . esc_attr($location) . '" featured_only="' . esc_attr($featured) . '" popular_only="' . esc_attr($popular) . '" header="' . esc_attr($header) . '" header_title ="' . esc_attr($title) . '" columns="' . esc_attr($row) . '" action_before_after_loop="' . esc_attr($sidebar) . '" show_pagination="' . esc_attr($show_pagination) . '" advanced_filter="' . esc_attr($filter) . '" map_height="' . $map_height . '" display_preview_image="yes" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]');
    }
}

//Single category map
class Direo_SingleLocMap extends Widget_Base
{
    public function get_name()
    {
        return 'single_loc_map';
    }

    public function get_title()
    {
        return __('Single Location Map View', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-google-maps';
    }

    public function get_keywords()
    {
        return ['single location', 'need location', 'location',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'single_cat',
            [
                'label' => __('Single Category Map View', 'direo-core'),
            ]
        );
        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
            ]
        );
        
        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
            ]
        );
        $this->add_control(
            'title',
            [
                'label'   => __('Listings Found Text', 'direo-core'),
                'type'    => Controls_Manager::TEXT,
                'default' => __('Listings Found', 'direo-core'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '2' => esc_html__('2 Column', 'direo-core'),
                    '3' => esc_html__('3 Column', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'cat',
            [
                'label'    => __('Specify Categories', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_category') ? direo_listing_category() : [],
            ]
        );
        $this->add_control(
            'loc',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );
        $this->add_control(
            'tag',
            [
                'label'    => __('Specify Tags', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_tags') ? direo_listing_tags() : []
            ]
        );

        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $show_pagination = $settings['show_pagination'];
        $title           = $settings['title'];
        $cat             = $settings['cat'] ? implode(',', $settings['cat']) : '';
        $location        = $settings['loc'] ? implode(',', $settings['loc']) : '';
        $tag             = $settings['tag'] ? implode(',', $settings['tag']) : '';
        $popular         = $settings['popular'];
        $featured        = $settings['featured'];
        $layout          = $settings['layout'];
        $number_cat      = $settings['number_cat'];
        $order_by        = $settings['order_by'];
        $order_list      = $settings['order_list'];
        $user            = $settings['user'];
        $web             = 'yes' == $user ? $settings['link']['url'] : '';
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : ''; ?>

        <input type="hidden" id="listing-listings_with_map">

    <?php echo do_shortcode('[directorist_location view="listings_with_map" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" listings_per_page="' . esc_attr($number_cat) . '" category="' . esc_attr($cat) . '" tag="' . esc_attr($tag) . '" location="' . esc_attr($location) . '" featured_only="' . esc_attr($featured) . '" action_before_after_loop="no" popular_only="' . esc_attr($popular) . '" header="yes" header_title ="' . esc_attr($title) . '" show_pagination="' . esc_attr($show_pagination) . '" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" listings_with_map_columns="' . esc_attr($layout) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]');
    }
}

//Single tag
class Direo_SingleTag extends Widget_Base
{
    public function get_name()
    {
        return 'single_tag';
    }

    public function get_title()
    {
        return __('Single Tag', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-tags';
    }

    public function get_keywords()
    {
        return ['single tag', 'need tag', 'tag',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'single_cat',
            [
                'label' => __('Single Listing Category', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
            ]
        );
        
        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
            ]
        );

        $this->add_control(
            'header',
            [
                'label'   => __('Show Header?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title',
            [
                'label'       => __('Listings Found Text', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'default'     => __('Listings Found', 'direo-core'),
                'label_block' => true,
                'condition'   => [
                    'header' => 'yes'
                ]

            ]
        );
        $this->add_control(
            'filter',
            [
                'label'   => __('Show More Filter?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid View', 'direo-core'),
                    'list' => esc_html__('List View', 'direo-core'),
                    'map'  => esc_html__('Map View', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'sidebar',
            [
                'label'     => __('Show sidebar?', 'direo-core'),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'row',
            [
                'label'   => __('Listings Per Row', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
                'condition' => [
                    'layout' => 'grid'
                ]
            ]
        );

        $this->add_control(
            'map_height',
            [
                'label'     => __('Map Height', 'direo-core'),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 300,
                'max'       => 1980,
                'default'   => 500,
                'condition' => [
                    'layout' => 'map'
                ]
            ]

        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'cat',
            [
                'label'    => __('Specify Categories', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_category') ? direo_listing_category() : [],
            ]
        );

        $this->add_control(
            'tag',
            [
                'label'    => __('Specify Tags', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_tags') ? direo_listing_tags() : []
            ]
        );

        $this->add_control(
            'location',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );

        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
                'condition' => [
                    'layout!' => 'map'
                ]
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $header          = $settings['header'];
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $filter          = 'yes' == $settings['filter'] ? $settings['filter'] : 'no';
        $sidebar         = $settings['sidebar'];
        $show_pagination = $settings['show_pagination'];
        $title           = $settings['title'];
        $layout          = $settings['layout'];
        $number_cat      = $settings['number_cat'];
        $row             = $settings['row'];
        $cat             = $settings['cat'] ? implode(',', $settings['cat']) : '';
        $location        = $settings['location'] ? implode(',', $settings['location']) : '';
        $tag             = $settings['tag'] ? implode(',', $settings['tag']) : '';
        $featured        = $settings['featured'];
        $popular         = $settings['popular'];
        $order_by        = $settings['order_by'];
        $order_list      = $settings['order_list'];
        $map_height      = $settings['map_height'];
        $user            = $settings['user'];
        $web             = 'yes' == $user ? $settings['link']['url'] : '';

        echo do_shortcode('[directorist_tag view="' . esc_attr($layout) . '" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" listings_per_page="' . esc_attr($number_cat) . '" category="' . esc_attr($cat) . '" tag="' . esc_attr($tag) . '" location="' . esc_attr($location) . '" featured_only="' . esc_attr($featured) . '" popular_only="' . esc_attr($popular) . '" header="' . esc_attr($header) . '" header_title ="' . esc_attr($title) . '" columns="' . esc_attr($row) . '" action_before_after_loop="' . esc_attr($sidebar) . '" show_pagination="' . esc_attr($show_pagination) . '" advanced_filter="' . esc_attr($filter) . '" map_height="' . $map_height . '" display_preview_image="yes" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]');
    }
}

//Single category tag
class Direo_SingleTagMap extends Widget_Base
{
    public function get_name()
    {
        return 'single_tag_map';
    }

    public function get_title()
    {
        return __('Single Tag Map View', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-google-maps';
    }

    public function get_keywords()
    {
        return ['single tag', 'need tag', 'tag',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'single_cat',
            [
                'label' => __('Single Category Map View', 'direo-core'),
            ]
        );
        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['general'],
            ]
        );
        
        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'general',
            ]
        );
        $this->add_control(
            'title',
            [
                'label'   => __('Listings Found Text', 'direo-core'),
                'type'    => Controls_Manager::TEXT,
                'default' => __('Listings Found', 'direo-core'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('View As', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '2' => esc_html__('2 Column', 'direo-core'),
                    '3' => esc_html__('3 Column', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'user',
            [
                'label'   => __('Only For Logged In User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label'   => __('Redirect User?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label'     => __('Redirect Link', 'direo-core'),
                'type'      => Controls_Manager::URL,
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label'   => __('Number of Listings to Show:', 'direo-core'),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'cat',
            [
                'label'    => __('Specify Categories', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_category') ? direo_listing_category() : [],
            ]
        );
        $this->add_control(
            'loc',
            [
                'label'    => __('Specify Locations', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );
        $this->add_control(
            'tag',
            [
                'label'    => __('Specify Tags', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('direo_listing_tags') ? direo_listing_tags() : []
            ]
        );

        $this->add_control(
            'featured',
            [
                'label'   => __('Show Featured Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'popular',
            [
                'label'   => __('Show Popular Only?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label'   => __('Order by', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'title' => esc_html__(' Title', 'direo-core'),
                    'date'  => esc_html__(' Date', 'direo-core'),
                    'price' => esc_html__(' Price', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label'   => __('Listings Order', 'direo-core'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => __('Show Pagination?', 'direo-core'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings        = $this->get_settings_for_display();
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $show_pagination = $settings['show_pagination'];
        $title           = $settings['title'];
        $cat             = $settings['cat'] ? implode(',', $settings['cat']) : '';
        $location        = $settings['loc'] ? implode(',', $settings['loc']) : '';
        $tag             = $settings['tag'] ? implode(',', $settings['tag']) : '';
        $popular         = $settings['popular'];
        $featured        = $settings['featured'];
        $layout          = $settings['layout'];
        $number_cat      = $settings['number_cat'];
        $order_by        = $settings['order_by'];
        $order_list      = $settings['order_list'];
        $user            = $settings['user'];
        $web             = 'yes' == $user ? $settings['link']['url'] : ''; ?>

        <input type="hidden" id="listing-listings_with_map">

        <?php echo do_shortcode('[directorist_tag view="listings_with_map" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" listings_per_page="' . esc_attr($number_cat) . '" category="' . esc_attr($cat) . '" tag="' . esc_attr($tag) . '" location="' . esc_attr($location) . '" featured_only="' . esc_attr($featured) . '" action_before_after_loop="no" popular_only="' . esc_attr($popular) . '" header="yes" header_title ="' . esc_attr($title) . '" show_pagination="' . esc_attr($show_pagination) . '" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" listings_with_map_columns="' . esc_attr($layout) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]');
    }
}

//Testimonial
class Direo_Testimonial extends Widget_Base
{
    public function get_name()
    {
        return 'testimonials';
    }

    public function get_title()
    {
        return __('Testimonials', 'direo-core');
    }

    public function get_icon()
    {
        return 'eicon-testimonial-carousel';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['testimonial', 'client', 'testi'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'Testimonials',
            [
                'label' => __('Testimonials', 'direo-core'),
            ]
        );

        $this->add_control(
            'testimonials',
            [
                'label'       => __('Testimonials', 'direo-core'),
                'type'        => Controls_Manager::REPEATER,
                'title_field' => '{{{ name }}}',
                'fields'      => [
                    [
                        'name'        => 'name',
                        'label'       => __('Name', 'direo-core'),
                        'type'        => Controls_Manager::TEXT,
                        'label_block' => true,
                        'default'     => 'Mark Tony'
                    ],
                    [
                        'name'        => 'designation',
                        'label'       => __('Designation', 'direo-core'),
                        'type'        => Controls_Manager::TEXT,
                        'label_block' => true,
                        'default'     => 'Software Developer'
                    ],
                    [
                        'name'  => 'desc',
                        'label' => __('Testimonial Text', 'direo-core'),
                        'type'  => Controls_Manager::TEXTAREA,
                    ],
                    [
                        'name'  => 'image',
                        'label' => __('Author Image', 'direo-core'),
                        'type'  => Controls_Manager::MEDIA,
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings     = $this->get_settings_for_display();
        $testimonials = $settings['testimonials'];

        if ($testimonials) {
            echo wp_kses_post('<div class="testimonial-carousel owl-carousel">');
            foreach ($testimonials as $test) {
                $id          = $test['image']['id'];
                $image       = wp_get_attachment_image_src($id, array(80, 80))[0];
                $name        = $test['name'] ? $test['name'] : '';
                $designation = $test['designation'] ? $test['designation'] : '';
                $desc        = $test['desc'] ? $test['desc'] : '';
                $image_alt   = function_exists('direo_get_image_alt') ? direo_get_image_alt($id) : ''; ?>

                <div class="carousel-single">
                    <?php echo !empty($image) ? sprintf('<div class="author-thumb"><img src="%s" alt="%s" class="rounded-circle"></div>', esc_url($image), $image_alt) : ''; ?>
                    <div class="author-info">
                        <?php echo sprintf('<h4>%s</h4>', esc_attr($name));
                        echo sprintf('<span>%s</span>', esc_attr($designation)); ?>
                    </div>
                    <?php echo sprintf('<p class="author-comment">%s</p>', esc_attr($desc)); ?>
                </div>

        <?php
            }
            echo wp_kses_post('</div>');
        }
    }
}

//Subscribe
class Direo_Subscribe extends Widget_Base
{

    public function get_name()
    {
        return 'subscribe';
    }

    public function get_title()
    {
        return __('Subscribe', 'direo-core');
    }

    public function get_icon()
    {
        return 'fa fa-paper-plane';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['Subscribe', 'like', 'mailchimp'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'subscribe',
            [
                'label' => __('Subscribe', 'direo-core'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Title', 'direo-core'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => __('Subscribe to Newsletter', 'direo-core'),
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label'       => __('Subtitle', 'direo-core'),
                'type'        => Controls_Manager::TEXTAREA,
                'placeholder' => __('Enter your subtitle', 'direo-core'),
                'default'     => __('Add Your subtitle Text Here', 'direo-core'),
            ]
        );

        $this->add_control(
            'btn',
            [
                'label' => __('Subscribe Button Text', 'direo-core'),
                'type'  => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'action',
            [
                'label'       => __('Mailchimp Form Action Url', 'direo-core'),
                'type'        => Controls_Manager::URL,
                'description' => function_exists('mail_desc') ? mail_desc() : '',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $title    = $settings['title'];
        $subtitle = $settings['subtitle'];
        $btn      = $settings['btn'];
        $action   = $settings['action']['url']; ?>

        <section class="subscribe-wrapper" style="background-image: url('<?php echo get_template_directory_uri() . '/img/svg/sb-shape.svg';?>')">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <?php echo sprintf('<h1>%s</h1>', esc_attr($title));
                        echo sprintf('<p>%s</p>', esc_attr($subtitle)) ?>
                        <div class="row">
                            <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-sm-8 offset-sm-2">
                                <form action="<?php echo esc_url($action); ?>" method="post" class="subscribe-form m-top-40">
                                    <div class="form-group">
                                        <span class="la la-envelope-o"></span>
                                        <input type="email" placeholder="<?php echo esc_attr_x('Enter your email', 'placeholder', 'direo-core'); ?>" value="" name="EMAIL" class="required email" id="mce-EMAIL" required>
                                    </div>
                                    <input type="submit" value="<?php echo esc_attr($btn); ?>" class="btn btn-gradient btn-gradient-one">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php
    }
}

//Booking
class Direo_Booking_Confirmation extends Widget_Base
{
    public function get_name()
    {
        return 'booking';
    }

    public function get_title()
    {
        return __('Booking Confirmation', 'direo-core');
    }

    public function get_icon()
    {
        return ' fas fa-calendar-check';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'Booking',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'Booking_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'Booking_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    { ?>
        <div class="direo-booking-confirmation">
            <?php echo do_shortcode('[directorist_booking_confirmation]'); ?>
        </div>
<?php
    }
}

//Call to action
class CTA extends Widget_Base
{
    public function get_name()
    {
        return 'call_to_action';
    }

    public function get_title()
    {
        return __('Call To Action', 'direo-core');
    }

    public function get_icon()
    {
        return ' eicon-call-to-action';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['call_to_action', 'cta', 'call to action'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'categories',
            [
                'label' => __('General', 'direo-core'),
            ]
        );
        $this->add_control(
            'title1',
            [
                'label' => __('Title First Part', 'direo-core'),
                'type'  => Controls_Manager::TEXT,
                'default' => 'Find'
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title2',
            [
                'label'       => __('Title', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'default'     => __('Shopping', 'direo-core'),
            ]
        );

        $this->add_control(
            'texts',
            [
                'label'   => __('Changeable Text', 'direo-core'),
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'title2'   => __('Shopping', 'direo-core'),
                    ],
                ],
                'title_field' => '{{{ title2 }}}',
            ]
        );

        $this->add_control(
            'title3',
            [
                'label' => __('Title Last Part', 'direo-core'),
                'type'  => Controls_Manager::TEXT,
                'default' => 'for your next trip'
            ]
        );
        $this->add_control(
            'btn',
            [
                'label'       => __('Button Label', 'direo-core'),
                'type'        => Controls_Manager::TEXT,
                'default' => 'Explore Now'
            ]
        );
        $this->add_control(
            'btn_url',
            [
                'label'       => __('Button URL', 'direo-core'),
                'type'        => Controls_Manager::URL,
                'label_block' => true,
            ]
        );
        $this->add_control(
            'img',
            [
                'label' => __( 'Right Side Image', 'dcar' ),
                'type'  => Controls_Manager::MEDIA,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style',
            [
                'label' => __('Style', 'direo-core'),
            ]
        );
        $this->add_control(
            'section_bg',
            [
                'label'  => __('Section BG  Color', 'direo-core'),
                'type'   => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cta-wrapper' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings   = $this->get_settings_for_display();
        az_template('/elementor/cta/view', $settings);
    }
}

//Need categories
class direo_NeedCategories extends Widget_Base
{
    public function get_name()
    {
        return 'need_categories';
    }

    public function get_title()
    {
        return __('Need Categories', 'direo-core');
    }

    public function get_icon()
    {
        return ' far fa-question-circle';
    }

    public function get_keywords()
    {
        return ['need', 'categories', 'need categories',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'need_categories',
            [
                'label' => __('Need Categories', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['need-listings'],
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'need-listings',
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => __('Category Layout', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid View', 'direo-core'),
                    'list' => esc_html__('List View', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'cat_style',
            [
                'label' => __('Category Style', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'category-style1',
                'options' => [
                    'category-style1' => esc_html__('Style 1', 'direo-core'),
                    'category-style-two' => esc_html__('Style 2', 'direo-core'),
                ],
                'condition' => [
                    'layout' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'row',
            [
                'label' => __('Categories Per Row', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'number_cat',
            [
                'label' => __('Number of categories to Show:', 'direo-core'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 1000,
                'step' => 1,
                'default' => 6,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => __('Order by', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'id',
                'options' => [
                    'id' => esc_html__(' Cat ID', 'direo-core'),
                    'count' => esc_html__('Needs Count', 'direo-core'),
                    'name' => esc_html__(' Category name (A-Z)', 'direo-core'),
                    'slug' => esc_html__('Select Category', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'slug',
            [
                'label' => __('Specify Locations', 'direo-core'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => function_exists('direo_listing_category') ? direo_listing_category() : []
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label' => __('Locations Order', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc' => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'user',
            [
                'label' => __('Only For Logged In User?', 'direo-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label' => __('Redirect User?', 'direo-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __('Redirect Link', 'direo-core'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '',
                ],
                'separator' => 'before',
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $number_cat = $settings['number_cat'];
        $order_by = $settings['order_by'];
        $order_list = $settings['order_list'];
        $row = $settings['row'];
        $slug = $settings['slug'] ? implode($settings['slug'], []) : '';
        $cat_style = $settings['cat_style'];
        ?>

        <div id="<?php echo esc_attr($cat_style); ?>">
            <?php echo do_shortcode( '[directorist_all_categories view="layout" orderby="' . esc_attr( $order_by ) . '" order="' . esc_attr( $order_list ) . '" cat_per_page="' . esc_attr( $number_cat ) . '" columns="' . esc_attr( $row ) . '" slug="' . esc_attr( $slug ) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]' );
            ?>
        </div>
    <?php
    }
}

//Need locations
class direo_NeedLocations extends Widget_Base
{
    public function get_name()
    {
        return 'need_locations';
    }

    public function get_title()
    {
        return __('Need Locations', 'direo-core');
    }

    public function get_icon()
    {
        return ' far fa-question-circle';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    public function get_keywords()
    {
        return ['locations', 'need locations',];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'need_locations',
            [
                'label' => __('Need Locations', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['need-listings'],
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'need-listings',
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => __('Locations Layout', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid View', 'direo-core'),
                    'list' => esc_html__('List View', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'row',
            [
                'label' => __('Location Per Row', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'number_loc',
            [
                'label' => __('Number of locations to Show:', 'direo-core'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 1000,
                'step' => 1,
                'default' => 4,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => __('Order by', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'id',
                'options' => [
                    'id' => esc_html__(' Cat ID', 'direo-core'),
                    'count' => esc_html__('Needs Count', 'direo-core'),
                    'name' => esc_html__(' Category name (A-Z)', 'direo-core'),
                    'slug' => esc_html__('Select Category', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'slug',
            [
                'label' => __('Specify Locations', 'direo-core'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => function_exists('direo_listing_locations') ? direo_listing_locations() : []
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label' => __('Locations Order', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc' => esc_html__(' ASC', 'direo-core'),
                    'desc' => esc_html__(' DESC', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'user',
            [
                'label' => __('Only For Logged In User?', 'direo-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'redirect',
            [
                'label' => __('Redirect User?', 'direo-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __('Redirect Link', 'direo-core'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '',
                ],
                'separator' => 'before',
                'condition' => [
                    'redirect' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $number_loc = $settings['number_loc'];
        $order_by = $settings['order_by'];
        $order_list = $settings['order_list'];
        $row = $settings['row'];
        $slug = $settings['slug'] ? implode($settings['slug'], []) : '';
        $layout = $settings['layout'];
        $user = $settings['user'];
        $web = 'yes' == $user ? $settings['link']['url'] : '';

        echo do_shortcode('[directorist_all_locations view="' . esc_attr($layout) . '" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" loc_per_page="' . esc_attr($number_loc) . '" columns="' . esc_attr($row) . '" slug="' . esc_attr($slug) . '" logged_in_user_only="' . esc_attr($user) . '" redirect_page_url="' . esc_attr($web) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '"]');
    }
}

//Need single category
class direo_NeedSingleCat extends Widget_Base
{
    public function get_name()
    {
        return 'need_single_category';
    }

    public function get_title()
    {
        return __('Need Single Category', 'direo-core');
    }

    public function get_icon()
    {
        return ' far fa-question-circle';
    }

    public function get_keywords()
    {
        return ['single category', 'need category', 'category',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'need_single_category',
            [
                'label' => __('Need Single Category', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['need-listings'],
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'need-listings',
            ]
        );

        $this->add_control(
            'number',
            [
                'label' => __('Number of Needs to Show:', 'direo-core'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'pagination',
            [
                'label' => __('Show Pagination?', 'direo-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $number = $settings['number'];
        $pagination = $settings['pagination'];

        echo do_shortcode('[directorist_category listings_per_page="' . $number . '" show_pagination="' . $pagination . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '" header="no" action_before_after_loop="no" display_preview_image="no"]');
    }
}

//Need single location
class direo_NeedSingleLoc extends Widget_Base
{
    public function get_name()
    {
        return 'need_single_location';
    }

    public function get_title()
    {
        return __('Need Single Location', 'direo-core');
    }

    public function get_icon()
    {
        return ' far fa-question-circle';
    }

    public function get_keywords()
    {
        return ['single location', 'need location', 'location',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'need_single_location',
            [
                'label' => __('Need Single Location', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['need-listings'],
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'need-listings',
            ]
        );

        $this->add_control(
            'number',
            [
                'label' => __('Number of Needs to Show:', 'direo-core'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'pagination',
            [
                'label' => __('Show Pagination?', 'direo-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $number = $settings['number'];
        $pagination = $settings['pagination'];

        echo do_shortcode('[directorist_location listings_per_page="' . $number . '" show_pagination="' . $pagination . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '" action_before_after_loop="no"]');
    }
}

//Needs
class direo_Needs extends Widget_Base
{
    public function get_name()
    {
        return 'needs';
    }

    public function get_title()
    {
        return __('All Needs', 'direo-core');
    }

    public function get_icon()
    {
        return ' far fa-question-circle';
    }

    public function get_keywords()
    {
        return ['need',];
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'needs',
            [
                'label' => __('All Needs', 'direo-core'),
            ]
        );

        $this->add_control(
            'types',
            [
                'label'    => __('Specify Listing Types', 'direo-core'),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => ['need-listings'],
            ]
        );

        $this->add_control(
            'default_types',
            [
                'label'    => __('Set Default Listing Type', 'direo-core'),
                'type'     => Controls_Manager::SELECT,
                'multiple' => true,
                'options'  => function_exists('directorist_listing_types') ? directorist_listing_types() : [],
                'default'  => 'need-listings',
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Needs Per Row', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '5' => esc_html__('5 Items / Row', 'direo-core'),
                    '4' => esc_html__('4 Items / Row', 'direo-core'),
                    '3' => esc_html__('3 Items / Row', 'direo-core'),
                    '2' => esc_html__('2 Items / Row', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => __('Order by', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'ID' => esc_html__(' Post ID', 'direo-core'),
                    'author' => esc_html__(' Author', 'direo-core'),
                    'title' => esc_html__(' Title', 'direo-core'),
                    'name' => esc_html__(' Post name (post slug)', 'direo-core'),
                    'type' => esc_html__(' Post type (available since Version 4.0)', 'direo-core'),
                    'date' => esc_html__(' Date', 'direo-core'),
                    'modified' => esc_html__(' Last modified date', 'direo-core'),
                    'rand' => esc_html__(' Random order', 'direo-core'),
                    'comment_count' => esc_html__(' Number of comments', 'direo-core')
                ],
            ]
        );

        $this->add_control(
            'order_list',
            [
                'label' => __('Order post', 'direo-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => esc_html__(' ASC', 'direo-core'),
                    'DESC' => esc_html__(' DESC', 'direo-core'),
                ],
            ]
        );

        $this->add_control(
            'number',
            [
                'label' => __('Number of Needs to Show:', 'direo-core'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 100,
                'default' => 3,
            ]
        );

        $this->add_control(
            'pagination',
            [
                'label' => __('Show Pagination?', 'direo-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $default_types = $settings['default_types'];
        $types = $settings['types'] ? implode( ',', $settings['types'] ) : '';
        $columns = $settings['columns'];
        $number = $settings['number'];
        $order = $settings['order_by'];
        $order_list = $settings['order_list'];
        $pagination = $settings['pagination'];
        
        echo do_shortcode('[directorist_all_listing view="grid" listings_per_page="' . esc_attr($number) . '" columns="' . esc_attr($columns) . '" show_pagination="' . esc_attr($pagination) . '" display_preview_image="no" action_before_after_loop="no" order_by="' . esc_attr($order) . '" sort_by="' . esc_attr($order_list) . '" directory_type="' . $types . '" default_directory_type="' . $default_types . '" header="no"]');

    }
}

//Listing Compare
class Direo_Compare extends Widget_Base
{
    public function get_name()
    {
        return 'compare';
    }

    public function get_title()
    {
        return __('Listing Compare', 'direo-core');
    }

    public function get_icon()
    {
        return ' fa fa-exchange';
    }

    public function get_categories()
    {
        return ['direo_category'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'compare',
            [
                'label' => __('Styling', 'direo-core'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'compare_margin',
            [
                'label'      => __('margin', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'compare_padding',
            [
                'label'      => __('Padding', 'direo-core'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    { ?>
        <div class="direo-listing-compare">
            <?php echo do_shortcode('[directorist_listing_compare]'); ?>
        </div>
        <?php
    }
}
