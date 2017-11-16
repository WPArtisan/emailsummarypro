<tr>
	<td align="center" valign="top">
		<!-- BEGIN PREHEADER // -->
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templatePreheader">
			<tr>
				<td valign="top" class="preheaderContent" style="padding-top:10px; padding-right:20px; padding-bottom:10px; padding-left:20px;">
					<?php echo date( "l, jS F Y", strtotime( $roundup_date_from ) ); ?>
					&nbsp;&#150;&nbsp;
					<?php echo date( "l, jS F Y", strtotime( $roundup_date_to ) ); ?>
				</td>
				<!-- *|IFNOT:ARCHIVE_PAGE|* -->
				<td valign="top" width="180" class="preheaderContent" style="padding-top:10px; padding-right:20px; padding-bottom:10px; padding-left:0;">
					Email not displaying correctly?<br /><a href="*|ARCHIVE|*" target="_blank">View it in your browser</a>.
				</td>
				<!-- *|END:IF|* -->
			</tr>
		</table>
		<!-- // END PREHEADER -->
	</td>
</tr>
