<?php
/**
* Abstract class which has helper functions to get data from the database
*/
abstract class Base_Controller
{
  public $models = array();

  public function __construct() {
    $this->loadModels();
  }

  public function loadModels(){
      require_once( plugin_dir_path( __FILE__ )  . 'model.php');

			foreach ($this->models as $key => $model) {
				$this->{'model_'.$model} = new Model($model);

        // DB
    		register_activation_hook( __FILE__, array( &$this->{'model_'.$model}, 'init' ) );
    		add_action( 'plugins_loaded', array( &$this->{'model_'.$model}, 'update_init') );

			}
	}
}
