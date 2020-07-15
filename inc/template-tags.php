<?php
if ( ! defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
}

/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package dostart
 */

function dostart_body_classes( $classes ) {
    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    return $classes;
}
add_filter('body_class', 'dostart_body_classes');

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function dostart_pingback_header() {
    if ( is_singular() && pings_open() ) {
        echo '<link rel="pingback" href="', esc_url(get_bloginfo('pingback_url')), '">';
    }
}
add_action('wp_head', 'dostart_pingback_header');

if ( ! function_exists('dostart_posted_on') ) :
    /**
     * Prints HTML with meta information for the current post-date/time and author.
     */
    function dostart_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time('U') !== get_the_modified_time('U') ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
        }

        $time_string = sprintf($time_string,
            esc_attr(get_the_date('c')),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date('c')),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x('Posted on %s', 'post date', 'dostart'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
        );

        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x('by %s', 'post author', 'dostart'),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

        echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

    }
endif;

if ( ! function_exists('dostart_entry_footer') ) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function dostart_entry_footer() {
        // Hide category and tag text for pages.
        if ( 'post' === get_post_type() ) {

            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(esc_html__(', ', 'dostart'));
            if ( $categories_list ) {
                /* translators: 1: list of categories. */
                printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'dostart') . '</span>', $categories_list); // WPCS: XSS OK.
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'dostart'));
            if ( $tags_list ) {
                /* translators: 1: list of tags. */
                printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'dostart') . '</span>', $tags_list); // WPCS: XSS OK.
            }
        }

        if ( ! is_single() && ! post_password_required() && (comments_open() || get_comments_number()) ) {
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'dostart'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                )
            );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('Edit <span class="screen-reader-text">%s</span>', 'dostart'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                get_the_title()
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
endif;

/*
 * theme style
 */
if ( ! function_exists('dostart_dynamic_styles') ) {
    function dostart_dynamic_styles() {
        global $dostart_option;
        // parimary color
        $dostart_primary_color = dostart_theme_option('primary_color');

        // footer widget background
        $footer_widget_bg = empty($dostart_option['footer_widget_bg']) ? '' : $dostart_option['footer_widget_bg'];

        // footer background color
        $footer_background = empty($dostart_option['footer-background']) ? '' : $dostart_option['footer-background'];

        // footer top background color
        $back_to_top_bg = empty($dostart_option['backtotop-button-bg']) ? '' : $dostart_option['backtotop-button-bg'];

        // back to top hover color
        $back_to_top_hover = empty($dostart_option['backtotop-button-hover-bg']) ? '' : $dostart_option['backtotop-button-hover-bg'];

        ob_start();?>

        .dostart-breadcrumb-area,
        .dostart-breadcrumb-bg,
        article a.dostart-btn,
        .widget-title:after,
        .widgettitle:after,
        .search-form:after,
        .dostart-single-blog-breadcrumb:before,
        .comment-form p > input[type="submit"]
            {
             background-color: <?php echo esc_attr($dostart_primary_color); ?>
            }
            article.post a{
             color: <?php echo esc_attr($dostart_primary_color); ?>
            }

        article a.dostart-btn {
            color: #fff;
           }
         header{
            background: url('<?php echo esc_url(header_image()); ?>');
        }
        .footer-top-widgets{
            background: <?php echo esc_attr($footer_widget_bg); ?>;
        }
        .dostart-footer-area{
            background-color: <?php echo esc_attr($footer_background); ?>;
        }
        div.back-to-top{
            background: <?php echo esc_attr($back_to_top_bg); ?>
         }
        div.back-to-top:hover{
            background: <?php echo esc_attr($back_to_top_hover); ?>
        }

    <?php
$output = ob_get_clean();
        return $output;
    } //end  dostart_dynamic_styles
} //endif

function dostart_style_method() {

    $custom_css = dostart_dynamic_styles();
    wp_add_inline_style('dostart-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'dostart_style_method');