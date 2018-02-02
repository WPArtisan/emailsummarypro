<?php foreach ( $template_parts as $template_part ) : ?>
	<?php
	if ( file_exists( esp_locate_template( $template_part ) ) )
		include esp_locate_template( $template_part );
	?>
<?php endforeach; ?>
