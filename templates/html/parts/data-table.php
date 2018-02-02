<?php
	$post_stats = esp_get_post_stats( $summary_date_from, $summary_date_to );

	// Just exit if no stats
	if ( ! $post_stats )
		return;
?>
<tr>
	<td align="center" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
			<tr>
				<td style="width: 5%;"></td>
				<td valign="top" align="center" class="bodyContent">

					<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">

						<tr>

							<td style="height:200px;padding:0px 0px;background: lightgrey;">
							</td>

							<?php foreach ( $post_stats->breakdown as $date => $stats ) : ?>

								<?php
									// Work it out as a percentage
									$publish_percentage = round( ( $stats['publish_count'] / $post_stats->publish_count ) * 100 );
									$pending_percentage = round( ( $stats['pending_count'] / $post_stats->pending_count ) * 100 );
								?>

								<td valign="bottom" align="center">
									<?php esp_bar_line( $publish_percentage, 'ff8080' ); ?>
									<?php esp_bar_line( $pending_percentage, '000000' ); ?>
								</td>

							<?php endforeach; ?>
						</tr>
						<tr>
							<td></td>
							<td align="center" style="font-size:12px;border-top: 1px solid lightgray;"><?php _e( 'Mon', 'email-summary-pro' ); ?></td>
							<td align="center" style="font-size:12px;border-top: 1px solid lightgray;"><?php _e( 'Tues', 'email-summary-pro' ); ?></td>
							<td align="center" style="font-size:12px;border-top: 1px solid lightgray;"><?php _e( 'Wed', 'email-summary-pro' ); ?></td>
							<td align="center" style="font-size:12px;border-top: 1px solid lightgray;"><?php _e( 'Thurs', 'email-summary-pro' ); ?></td>
							<td align="center" style="font-size:12px;border-top: 1px solid lightgray;"><?php _e( 'Fri', 'email-summary-pro' ); ?></td>
							<td align="center" style="font-size:12px;border-top: 1px solid lightgray;"><?php _e( 'Sat', 'email-summary-pro' ); ?></td>
							<td align="center" style="font-size:12px;border-top: 1px solid lightgray;"><?php _e( 'Sun', 'email-summary-pro' ); ?></td>
						</tr>

						<tr>
							<td colspan="7" align="center" style="font-size: 10px;">
								<span style="color: #EB4102;"><?php _e( 'Published Posts', 'email-summary-pro' ); ?></span>&nbsp;-&nbsp;<span style="color:#000000;"><?php _e( 'Pending Posts', 'email-summary-pro' ); ?></span>
							</td>
						</tr>

					</table>


				</td>
				<td style="width: 5%;"></td>
			</tr>
		</table>
	</td>
</tr>
