<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2>CONfu Aktiviteter</h2>
	
	<table class="wp-list-table widefat fixed" cellspacing="0">
		<thead>
			<tr>
				<th>Titel</th>
				<th>Dag og Tid</th>
				<th>Antal deltagere</th>
				<th>Forfatter</th>
				<th>Type</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Titel</th>
				<th>Dag og Tid</th>
				<th>Antal deltagere</th>
				<th>Forfatter</th>
				<th>Type</th>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$activities_args = array(
			'post_type' => 'aktiviteter',
			'orderby' => ' name',
			'order' => 'ASC',
			'posts_per_page' => -1
		);
		$activities = new WP_Query( $activities_args );
		if ( $activities->have_posts() ) : while ( $activities->have_posts() ) : $activities->the_post();
			$types = get_the_terms( get_the_ID(), 'aktivitetstype' );
			foreach($types as $type){} ?>
			<tr>
				<th><?php the_title(); ?></th>
				<td><?php echo date('H:i', get_post_meta(get_the_ID(), 'hc_aktivitet_tidspunkt', true)); ?></td>
				<td><?php echo countActivityAttendees(get_the_ID()); ?> / <?php echo get_post_meta(get_the_ID(), 'hc_aktivitet_deltagertal', true); ?></td>
				<td><?php echo get_post_meta(get_the_ID(), 'hc_aktivitet_forfatter', true); ?></td>
				<td><?php echo $type->name; ?></td>
			</tr>
		<?php endwhile; endif; ?>
		</tbody>
	</table>
	
</div>