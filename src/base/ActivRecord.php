<?php namespace mailer\base;

use mailer\base\BaseActivRecord as BaseActivRecord;

class ActivRecord extends BaseActivRecord {
    
    public function __construct() {                                                                                 
        
    }
    
    public function prymaryKey() {
        return false;
    }
    
    protected function delete() {
        global $wpdb;
        $pk = $this->prymaryKey();
        $table = App::instance()->pictures_table_name;
        $query = $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) ); 
        
        return $query;
    }
    
    protected function update() {
        ;
    }
    
    protected function insert() {
        ;
    }
    
    protected function select() {
        ;
    }
    
    
}

