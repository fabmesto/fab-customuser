<?php
require_once( plugin_dir_path( __FILE__ )  . 'base_model.php');

class Table extends Base_Model
{
    public function __construct($tableName)
    {
         parent::__construct($tableName);
    }
}