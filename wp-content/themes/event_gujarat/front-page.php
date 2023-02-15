<?php
if ( !is_user_logged_in()) {
  wp_redirect(home_url().'/login'); 
  exit;
}
get_header();?>

<div class="page-main">
	<div class="container">
		<div class="front-page">
			<div class="filter-event">
				<?php echo do_shortcode('[search_events]');?>
			</div>
			
		</div>		
	</div>
</div>

<?php get_footer();?>