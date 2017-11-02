<?php
add_action( 'admin_menu', 'fab_add_admin_menu' );
add_action( 'admin_init', 'fab_settings_init' );


function fab_add_admin_menu(  ) {

	add_options_page( 'fab-customuser', 'fab-customuser', 'manage_options', 'fab-customuser', 'fab_options_page' );

}


function fab_settings_init(  ) {

	register_setting( 'pluginPage', 'fab_settings' );

	add_settings_section(
		'fab_pluginPage_section',
		__( 'Your section description', 'wordpress' ),
		'fab_settings_section_callback',
		'pluginPage'
	);

	add_settings_field(
		'fab_text_field_0',
		__( 'Settings field description', 'wordpress' ),
		'fab_text_field_0_render',
		'pluginPage',
		'fab_pluginPage_section'
	);


}


function fab_text_field_0_render(  ) {

	$options = get_option( 'fab_settings' );
	?>
	<input type='text' name='fab_settings[fab_text_field_0]' value='<?php echo $options['fab_text_field_0']; ?>'>
	<?php

}


function fab_settings_section_callback(  ) {

	echo __( 'This section description', 'wordpress' );

}


function fab_options_page(  ) {

	?>
	<form action='options.php' method='post'>

		<h2>fab-customuser</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}

?>
