<?php
require_once( plugin_dir_path( __FILE__ )  . 'base_controller.php');

class Controller extends Base_Controller
{
    public $models = array('fabtest');

    public function __construct()
    {
         parent::__construct();
    }

    public function index(){
      $result = $this->{'model_'.$this->models[0]}->get_all();
			foreach ($result as $key => $value) {
				?><div><?php echo $value->name?></div><?php
			}
    }
}
