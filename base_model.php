<?php
/**
* Abstract class which has helper functions to get data from the database
*/
abstract class Base_Model
{
  /**
  * The current table name
  *
  * @var boolean
  */
  private $tableName = false;
  protected $initSql = false;
  /**
  * Constructor for the database class to inject the table name
  *
  * @param String $tableName - The current table name
  */
  public function __construct($tableName){
    global $wpdb;
    $this->tableName = $wpdb->prefix . $tableName;
  }

  public function update_init(){
    global $fab_customuser_db_version;
    if ( get_site_option( 'fab_customuser_db_version' ) != $fab_customuser_db_version ) {
      $this->init();
    }
  }

  public function init(){
    if($this->initSql){
      global $wpdb;
      global $fab_customuser_db_version;
      $charset_collate = $wpdb->get_charset_collate();
      /*
  		$this->initSql = "CREATE TABLE ".$this->tableName." (
  			id int(11) NOT NULL AUTO_INCREMENT,
  			created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  			name tinytext NOT NULL,
  			creator int(11) NOT NULL,
  			members text DEFAULT '' NOT NULL,
  			UNIQUE KEY id (id)
  			) ".$charset_collate.";
  			";
      */
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $this->initSql );
      update_option( 'fab_customuser_db_version', $fab_customuser_db_version );
    }
  }

  /**
  * Insert data into the current data
  *
  * @param  array  $data - Data to enter into the database table
  *
  * @return InsertQuery Object
  */
  /*
  $table = new Table( 'tablename' );
  $table->insert( array('id' => 1, 'name' => 'John') );
  */
  public function insert(array $data)
  {
    global $wpdb;

    if(empty($data))
    {
      return false;
    }

    $wpdb->insert($this->tableName, $data);

    return $wpdb->insert_id;
  }

  /**
  * Get all from the selected table
  *
  * @param  String $orderBy - Order by column name
  *
  * @return Table result
  */
  /*
  $table = new Table( 'tablename' );
  $all_rows = $table->get_all();
  */
  public function get_all( $orderBy = NULL )
  {
    global $wpdb;

    $sql = 'SELECT * FROM `'.$this->tableName.'`';

    if(!empty($orderBy))
    {
      $sql .= ' ORDER BY ' . $orderBy;
    }

    $all = $wpdb->get_results($sql);

    return $all;
  }

  /**
  * Get a value by a condition
  *
  * @param  Array $conditionValue - A key value pair of the conditions you want to search on
  * @param  String $condition - A string value for the condition of the query default to equals
  *
  * @return Table result
  */
  /*
  $table = new Table( 'tablename' );
  $john_record = $table->get_by( array('name' => 'John') );
  */
  public function get_by(array $conditionValue, $condition = '=', $returnSingleRow = FALSE)
  {
    global $wpdb;

    try
    {
      $sql = 'SELECT * FROM `'.$this->tableName.'` WHERE ';

      $conditionCounter = 1;
      foreach ($conditionValue as $field => $value)
      {
        if($conditionCounter > 1)
        {
          $sql .= ' AND ';
        }

        switch(strtolower($condition))
        {
          case 'in':
          if(!is_array($value))
          {
            throw new Exception("Values for IN query must be an array.", 1);
          }

          $sql .= $wpdb->prepare('`%s` IN (%s)', $field, implode(',', $value));
          break;

          default:
          $sql .= $wpdb->prepare('`'.$field.'` '.$condition.' %s', $value);
          break;
        }

        $conditionCounter++;
      }

      $result = $wpdb->get_results($sql);

      // As this will always return an array of results if you only want to return one record make $returnSingleRow TRUE
      if(count($result) == 1 && $returnSingleRow)
      {
        $result = $result[0];
      }

      return $result;
    }
    catch(Exception $ex)
    {
      return false;
    }
  }

  /**
  * Update a table record in the database
  *
  * @param  array  $data           - Array of data to be updated
  * @param  array  $conditionValue - Key value pair for the where clause of the query
  *
  * @return Updated object
  */
  /*
  $table = new Table( 'tablename' );
  $updated = $table->update( array('name' => 'Fred'), array('name' => 'John') );
  */
  public function update(array $data, array $conditionValue)
  {
    global $wpdb;

    if(empty($data))
    {
      return false;
    }

    $updated = $wpdb->update( $this->tableName, $data, $conditionValue);

    return $updated;
  }

  /**
  * Delete row on the database table
  *
  * @param  array  $conditionValue - Key value pair for the where clause of the query
  *
  * @return Int - Num rows deleted
  */
  /*
  $table = new Table( 'tablename' );
  $deleted = $table->delete( array('name' => 'John') );
  */
  public function delete(array $conditionValue)
  {
    global $wpdb;

    $deleted = $wpdb->delete( $this->tableName, $conditionValue );

    return $deleted;
  }
}
