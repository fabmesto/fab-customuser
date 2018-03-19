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
define('FAB_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ));
define('FAB_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ));

class Fab_Custom_User {
	public $allowed_roles = array('editor', 'administrator', 'author', 'contributor');
	public $before_content = 1;
	public $after_content = 1;
	public $before_title = 0;
	public $after_title = 0;
	public $every_n_p = 3;

	public function __construct() {
		// mostra
		add_action( 'show_user_profile', array( &$this, 'show_extra_profile_fields' ) );
		add_action( 'edit_user_profile', array( &$this, 'show_extra_profile_fields' ) );
		// salva
		add_action( 'personal_options_update', array( &$this, 'save_extra_profile_fields') );
		add_action( 'edit_user_profile_update', array( &$this, 'save_extra_profile_fields') );

		// show adsense in content
		add_filter( 'the_content', array( &$this, 'show_ads_in_content') );
		add_filter( 'the_title',  array( &$this, 'show_ads_in_title') );

		if ( is_admin() ){ // admin actions
      add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
      add_action( 'admin_init', array( &$this, 'register_settings' ) );
    }
	}

	public function add_admin_menu() {
    // add_management_page -> Strumenti
    // add_options_page -> Impostazioni
    // add_menu_page -> in ROOT
    add_menu_page(
      'Fab Custom User',
      'Fab Custom User',
      'manage_options',
      'fabcustomuser_settings',
      array( &$this, 'settings' )
      //plugins_url( 'fab-prazimark/images/icon.png' )
    );
  }

  public function settings(){
    ob_start();
    $action_file = FAB_PLUGIN_DIR_PATH.'settings.php';
    if(file_exists ( $action_file )){
      require_once( $action_file );
    }else{
      echo "Nessuna azione trovata: ".$action;
    }
    echo ob_get_clean();
  }

  public function register_settings() { // whitelist options
    register_setting( 'fabcustomuser-options', 'every_n_p' );
		register_setting( 'fabcustomuser-options', 'before_title' );
		register_setting( 'fabcustomuser-options', 'after_title' );
		register_setting( 'fabcustomuser-options', 'before_content' );
		register_setting( 'fabcustomuser-options', 'after_content' );
  }

	/* MOSTRA ADSENSE */
	public function show_extra_profile_fields( $user ) {
		if( array_intersect($this->allowed_roles, $user->roles ) ) {
			?>

			<h3>Informazioni riservate agli autori </h3>
			<table class="form-table">
				<tr>
					<th><label for="adsense">ADSENSE (in alto)</label></th>
					<td>
						<textarea name="adsense" id="adsense" class="regular-text" rows="5" cols="30"><?php echo esc_attr( get_the_author_meta( 'adsense', $user->ID ) ); ?></textarea><br />
						<span class="description">Codice ADSENSE che apparirà in alto ad ogni articolo dell'autore (TOP)</span>
					</td>
				</tr>
				<tr>
					<th><label for="adsense_bottom">ADSENSE (in basso)</label></th>
					<td>
						<textarea name="adsense_bottom" id="adsense_bottom" class="regular-text" rows="5" cols="30"><?php echo esc_attr( get_the_author_meta( 'adsense_bottom', $user->ID ) ); ?></textarea><br />
						<span class="description">Codice ADSENSE che apparirà in basso ad ogni articolo dell'autore (BOTTOM)</span>
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
		update_usermeta( $user_id, 'adsense_bottom', $_POST['adsense_bottom'] );
	}

	public function show_ads_in_title($content){
		$adsense = get_the_author_meta( 'adsense' );
		if($adsense=='') $adsense = get_the_author_meta( 'adsense', 1 );

		$code = '<!-- NO ADSENSE AUTORE -->';
		if( is_singular( 'post' ) ){
			$code = '<div class="adsense-user text-center">'.$adsense.'</div>';
		}

		$this->before_title = get_option('before_title');
		$this->after_title = get_option('after_title');

		if($this->before_title==1) $content = $code.$content;
		if($this->after_title==1) $content = $content.$code;
		return $content;
	}

	public function show_ads_in_content($content) {
		$adsense = get_the_author_meta( 'adsense' );
		if($adsense=='') $adsense = get_the_author_meta( 'adsense', 1 );

		$code = '<!-- NO ADSENSE TOP AUTORE -->';
		if( is_singular( 'post' ) ){
			$code = '<div class="adsense-user adsense-user-top text-center">'.$adsense.'</div>';
		}

		$adsense_bottom = get_the_author_meta( 'adsense_bottom' );
		if($adsense_bottom=='') $adsense_bottom = get_the_author_meta( 'adsense_bottom', 1 );

		$code_bottom = '<!-- NO ADSENSE BOTTOM AUTORE -->';
		if( is_singular( 'post' ) ){
			$code_bottom = '<div class="adsense-user adsense-user-bottom text-center">'.$adsense_bottom.'</div>';
		}

		$this->every_n_p = get_option('every_n_p');
		$this->before_content = get_option('before_content');
		$this->after_content = get_option('after_content');

		$content_p = explode("</p>", $content);
		$new_content = "";
		for ($i = 0; $i <count($content_p); $i++) {
			if($i!=0 && $i!=(count($content_p)-1) && ($i % $this->every_n_p)==0) {
				$new_content .= $content_p[$i]."</p>".$code;
			}	else{
				$new_content .= $content_p[$i]."</p>";
			}
		}

		if($this->before_content==1) $new_content = $code.$new_content;
		if($this->after_content==1) $new_content = $new_content.$code_bottom;

		return $new_content;
		/*
		if($this->before_content) $content = $code.$content;
		if($this->after_content) $content = $content.$code_bottom;
		return $content;
		*/
	}
}

$fab_custom_user = new Fab_Custom_User();
