<?php
/**
 * Template Name: Blog Post Page 
 * The template for displaying home page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_twenty
 * @since Twenty Sixteen 1.0
 */
get_header(); 

global $post;  
$args = array( 'posts_per_page' => 2, 'orderby' => 'post_date' );
$postslist = get_posts( $args );


?>
   <div class="main-content-wrapper">
        <div class="blog-section">
            <div class="container-wrapper">
                <div class="row-grid">
                    <?php  foreach ( $postslist as $post ): ?>
                        <div class="col-6">
                        <?php $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'thumbnail' ); ?>
                        <div class="newsBox">
                            <?php if(isset($url) && !empty($url)) { ?>
                                <div class="news-img">
                                    <a href="<?php echo get_permalink(); ?>">
                                        <img src="<?php echo $url ?>" alt="<?php echo $post->post_title; ?>" >
                                    </a>
                                </div>
                            <?php } ?>
                            <div class="news-info">
                                <h2><a href="<?php echo get_permalink(); ?>"><?php echo $post->post_title; ?></a></h2>
                                <?php echo $post->post_content; ?>
                            </div>
                        </div>
                        </div>
                    <?php endforeach;  ?>
                    <?php wp_reset_postdata(); ?>
                </div>
                <div class="more-btn"><a href="#" class="btn">Load More</a></div>
            </div>
        </div>
    </div>
<?php get_footer(); ?>
