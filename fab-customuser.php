<?php
/*
Plugin Name: Fab Custom User
Plugin URI: https://www.netedit.it/
Description: Aggiunge il campo ADSENSE ad ogni utente
Author: Fabrizio MESTO
Version: 0.0.1
Author URI: https://www.netedit.it/
Text Domain: fabcustomuser
Domain Path: lang
*/
$fab_customuser_db_version = '1.0';

class Fab_Custom_User {
	public $allowed_roles = array('editor', 'administrator', 'author', 'contributor');

	public function __construct() {
		// mostra
		add_action( 'show_user_profile', array( &$this, 'show_extra_profile_fields' ) );
		add_action( 'edit_user_profile', array( &$this, 'show_extra_profile_fields' ) );
		// salva
		add_action( 'personal_options_update', array( &$this, 'save_extra_profile_fields') );
		add_action( 'edit_user_profile_update', array( &$this, 'save_extra_profile_fields') );

		// menu
		add_action( 'admin_menu', array( &$this, 'setupAdminMenus' ) );

		// show adsense in content
		add_filter( 'the_content', array( &$this, 'show_ads_in_content') );
	}

	/* MOSTRA ADSENSE */
	public function show_extra_profile_fields( $user ) {
		if( array_intersect($this->allowed_roles, $user->roles ) ) {
			?>

			<h3>Informazioni riservate agli autori </h3>
			<table class="form-table">
				<tr>
					<th><label for="adsense">ADSENSE Code</label></th>
					<td>
						<textarea name="adsense" id="adsense" class="regular-text" rows="5" cols="30"><?php echo esc_attr( get_the_author_meta( 'adsense', $user->ID ) ); ?></textarea><br />
						<span class="description">Codice ADSENSE che apparirà in ogni articolo dell'autore (TOP and BOTTOM)</span>
					</td>
				</tr>
			</table>
		<?php }
	}

	/* SALVA ADSENSE */
	public function save_extra_profile_fields( $user_id ) {

		if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

		update_usermeta( $user_id, 'adsense', $_POST['adsense'] );
	}

	public function setupAdminMenus() {
		//add_menu_page( 'FAB Settings', 'FAB plugin', 'manage_options', 'fab_settings', array( &$this, 'settingsPage' ) );
		//add_menu_page( 'FAB Test SB', 'FAB test DB', 'manage_options', 'fab_test_db', array( &$this, 'testDB' ) );
	}

	public function show_ads_in_content($content) {
		$adsense = get_the_author_meta( 'adsense' );
		if($adsense=='') $adsense = get_the_author_meta( 'adsense', 1 );

		$code = '<!-- NO ADSENSE AUTORE -->';
		if( is_singular( 'post' ) ){
			$code = '<div class="adsense-user text-center">'.$adsense.'</div>';
		}
		return $code.$content.$code;
	}

}

$fab_custom_user = new Fab_Custom_User();
