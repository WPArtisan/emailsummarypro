<tr>
	<td align="center" valign="top">
		<!-- BEGIN PREHEADER // -->
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templatePreheader">
			<tr>
				<td valign="top" class="preheaderContent" style="padding-top:10px; padding-right:20px; padding-bottom:10px; padding-left:20px;">
					<?php echo date( "l, jS F Y", strtotime( $summary_date_from ) ); ?>
					&nbsp;-&nbsp;
					<?php echo date( "l, jS F Y", strtotime( $summary_date_to ) ); ?>
				</td>
				<!-- *|IFNOT:ARCHIVE_PAGE|* -->
				<td valign="top" width="180" class="preheaderContent" style="padding-top:10px; padding-right:20px; padding-bottom:10px; padding-left:0;">
					<?php esc_html_e( 'Email not displaying correctly?', 'email-summary-pro' ); ?>
					<br />
					<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'email_summary_pro', 'esp-action' => 'preview_summary', 'date' => $summary_date ), admin_url( 'options-general.php' ) ) ); ?>" target="_blank"><?php esc_html_e( 'View it in your browser', 'email-summary-pro' ); ?></a>.
				</td>
				<!-- *|END:IF|* -->
			</tr>
		</table>
		<!-- // END PREHEADER -->
	</td>
</tr>
