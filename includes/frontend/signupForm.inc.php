<h2>Tilmelding</h2>
					
<?php
$days_args = array(
	'hide_empty' => false,
	'orderby' 	 => 'id',
	'order' 	 => 'ASC'
);
$days = get_terms('days', $days_args);
foreach($days as $day) { ?>
	<h3><?php echo $day->name; ?></h3>
	<div id="activities">
	<?php
	$daily_activities_args = array(
		'posts_per_page' => -1,
		'post_type' => 'aktiviteter',
		'meta_key' => 'hc_aktivitet_tidspunkt',
		'orderby' => 'meta_value',
		'order' => 'ASC',
		'tax_query' => array( array(
			'taxonomy' => 'days',
			'field'    => 'slug',
			'terms'    => $day->slug
		) )
	);
	$daily_activities = new WP_Query($daily_activities_args);
	if ( $daily_activities->have_posts() ) : while ( $daily_activities->have_posts() ) : $daily_activities->the_post();
		$term_list = wp_get_post_terms($post->ID, 'confu_activity_type', array('fields' => 'all'));
	?>
		<article <?php post_class($term_list[0]->slug); ?>>
			<h4>
				<a href="#activity-<?php the_ID(); ?>">
					<span class="time"><?php echo date('H:i', get_post_meta($post->ID, 'hc_aktivitet_tidspunkt', true)); ?></span> / 
					<?php the_title(); ?>
				</a>
			</h4>
			<section id="activity-<?php the_ID(); ?>" class="post-body">
				<?php the_content(); ?>
				<section class="metadata">
					<dl>
						<dt>Pladser</dt>
						<dd><?php the_field('hc_aktivitet_deltagertal'); ?></dd>
						<dt>Pris</dt>
						<dd><?php the_field('hc_aktivitet_pris'); ?>,- / <?php the_field('hc_aktivitet_medlemspris'); ?>,-</dd>
						<?php 
						$activityAuthor = get_field('hc_aktivitet_forfatter');
						if( strlen($activityAuthor)>0 ) {?>
						<dt>Forfatter</dt>
						<dd><?php echo $activityAuthor; ?></dd>
						<?php }
						$activityOrganizer = get_field('hc_aktivitet_ansvarlig');
						if( strlen($activityOrganizer)>0 ) {?>
						<dt>Ansvarlig</dt>
						<dd><?php echo $activityOrganizer; ?></dd>
						<?php } ?>
					</dl>
				</section>
			</section>
		</article>
	<?php endwhile; else : ?>
		<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
	<?php endif; ?>
	</div>
<?php } ?>