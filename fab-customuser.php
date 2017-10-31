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

require_once( plugin_dir_path( __FILE__ )  . 'table.php');

class Fab_Custom_User {
	public $allowed_roles = array('editor', 'administrator', 'author', 'contributor');
	public $models = array('fabtest');

	public function __construct() {
		// DB
		register_activation_hook( __FILE__, array( &$this, 'installDB' ) );
		add_action( 'plugins_loaded', array( &$this, 'updateDB') );

		$this->loadModels();
		// mostra
		add_action( 'show_user_profile', array( &$this, 'show_extra_profile_fields' ) );
		add_action( 'edit_user_profile', array( &$this, 'show_extra_profile_fields' ) );
		// salva
		add_action( 'personal_options_update', array( &$this, 'save_extra_profile_fields') );
		add_action( 'edit_user_profile_update', array( &$this, 'save_extra_profile_fields') );

		// menu
		add_action( 'admin_menu', array( &$this, 'setupAdminMenus' ) );
	}

	public function loadModels(){
			foreach ($this->models as $key => $model) {
				$this->{'model_'.$model} = new Table($model);
			}
	}

	public function installDB(){
		global $wpdb;

		$tableName = $wpdb->prefix . 'fabtest';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE ".$tableName." (
			id int(11) NOT NULL AUTO_INCREMENT,
			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			name tinytext NOT NULL,
			creator int(11) NOT NULL,
			members text DEFAULT '' NOT NULL,
			UNIQUE KEY id (id)
			) ".$charset_collate.";
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			update_option( 'fab_customuser_db_version', $fab_customuser_db_version );
		}

		public function updateDB() {
			if ( get_site_option( 'fab_customuser_db_version' ) != $fab_customuser_db_version ) {
				$this->installDB();
			}
		}

		/* MOSTRA */
		public function show_extra_profile_fields( $user ) {
			if( array_intersect($this->allowed_roles, $user->roles ) ) {
				?>

				<h3>Informazioni riservate agli autori </h3>
				<table class="form-table">
					<tr>
						<th><label for="adsense">ADSENSE Code</label></th>
						<td>
							<textarea name="adsense" id="adsense" class="regular-text" rows="5" cols="30"><?php echo esc_attr( get_the_author_meta( 'adsense', $user->ID ) ); ?></textarea><br />
							<span class="description">Codice ADSENSE che apparirà in ogni articolo dell'autore</span>
						</td>
					</tr>
				</table>
			<?php }
		}

		/* SALVA */
		public function save_extra_profile_fields( $user_id ) {

			if ( !current_user_can( 'edit_user', $user_id ) )
			return false;

			update_usermeta( $user_id, 'adsense', $_POST['adsense'] );
		}

		public function setupAdminMenus() {
			add_menu_page( 'FAB Settings', 'FAB plugin', 'manage_options', 'fab_settings', array( &$this, 'settingsPage' ) );
			add_menu_page( 'FAB Test SB', 'FAB test DB', 'manage_options', 'fab_test_db', array( &$this, 'testDB' ) );
		}

		public function testDB(){
			$table = $this->model_fabtest;
			$result = $table->get_all();
			foreach ($result as $key => $value) {
				?><div><?php echo $value->name?></div><?php
			}
			?>
			<?php // Il form è stato inviato?
			if( isset( $_POST['insert_test_db'] ) ) {
				$fab_name = esc_attr( $_POST['fab_name'] ); // Valido l’input
					$data = array('name'=>$fab_name);
					$table->insert($data);
				?>
				<div id="message" class="updated">Riga aggiunta</div>
			<?php } ?>

			<form method="post" action="">
				<h3>Informazioni di test plugin </h3>
				<table class="form-table">
					<tr>
						<th><label for="adsense">Inserisci una riga di test nel DB</label></th>
						<td>
							<input type="text" name="fab_name" value="" />
							<span class="description">Descrizione campo</span>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button button-primary" value="Salva" />
					<input type="hidden" name="insert_test_db" value="Y" /></p>
				</p>
			</form>
			<?php
		}

		public function settingsPage() {
			?>
			<?php // Il form è stato inviato?
			if( isset( $_POST['update_settings'] ) ) {
				$my_test = esc_attr( $_POST['my_test'] ); // Valido l’input
				update_option( 'my_test', $my_test ); // Salvo l’opzione

				?>
				<div id="message" class="updated">Opzioni salvate</div>
			<?php } ?>

			<form method="post" action="">
				<h3>Informazioni di test plugin </h3>
				<table class="form-table">
					<tr>
						<th><label for="adsense">Opzione di test</label></th>
						<td>
							<input type="text" name="my_test" value="<?php echo get_option( 'my_test' ); ?>" />
							<span class="description">Descrizione campo</span>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button button-primary" value="Salva" />
					<input type="hidden" name="update_settings" value="Y" /></p>
				</p>
			</form>
			<?php
		}

	}

	$fab_custom_user = new Fab_Custom_User();
