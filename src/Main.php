<?php namespace mailer;

use mailer\base\Pages as Pages;
use mailer\base\AjaxController as AJAX;

class Main {
    
    public $items_table_name;
    public $items_table_name_custom;
    
    private static $instance = null;
    
    protected function __construct(){}
    
    protected function __clone(){}
    
    protected function __wakeup(){}
    
    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
            static::$instance->init_plugin();
        }

        return static::$instance;
    }
    
    public function adminInit()
    {
        new Pages();
    }


    private function init_plugin()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->items_table_name = $wpdb->prefix . "list_of_mailer";
        $this->items_table_name_custom = $wpdb->prefix . "list_of_mailer_custom";
        
        if ( is_admin() ) {
            //add_action( 'init', $this->adminInit() );
            $this->adminInit();
        }else{
            //$this->frontInit();
        }
        
        if (defined('DOING_AJAX') && DOING_AJAX) {
            
            $actionName = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
    
            if ( $actionName !== '' ) {
                add_action( 'admin_init', array( 'mailer\base\AjaxController', $actionName ) );
            }
        }
        
        register_activation_hook( __FILE__, array( $this, 'pluginActivation' ));
        register_deactivation_hook( __FILE__, array( $this, 'pluginDeactivation' ));        
    }
    
    public function pluginActivation()
    {
        if ( 'true' !== get_option('is_install_rc_mailer_plugin') ) {
            $this->installPlugin();
        }
    }
    
    public function pluginDeactivation()
    {
        delete_option('is_install_rc_mailer_plugin');
        return true;
    } 
    
    private function installPlugin()
    {   
        $table_name = $this->items_table_name;
        if($this->db->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $this->addTable();
        }
        
        $table_name2 = $this->items_table_name_custom;
        if($this->db->get_var("SHOW TABLES LIKE '$table_name2'") != $table_name2) {
            $this->addTable2();
        }
        
        add_option('is_install_rc_mailer_plugin', 'true');
    }
    
    private function addTable()
    {   
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        $table_name = $this->items_table_name;
                
        $sql_items = "CREATE TABLE " . $table_name . " (
            id int(6) NOT NULL AUTO_INCREMENT,
            addressee_id int(6) NOT NULL,
            mailer_id int(6) NOT NULL,
            post_id int(6) NOT NULL,
            type VARCHAR(255) NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            is_send boolean DEFAULT 0 NOT NULL,
            UNIQUE KEY id (id)
	);";
        
        dbDelta($sql_items);
    }
    
    private function addTable2()
    {   
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        $table_name = $this->items_table_name_custom;
                
        $sql_items = "CREATE TABLE " . $table_name . " (
            id int(6) NOT NULL AUTO_INCREMENT,
            addressee_name VARCHAR(30) NOT NULL,
            addressee_mail VARCHAR(35) NOT NULL,
            mailer_id int(6) NOT NULL,
            post_id int(6) NOT NULL,
            type VARCHAR(255) NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            is_send boolean DEFAULT 0 NOT NULL,
            UNIQUE KEY id (id)
	);";
        
        dbDelta($sql_items);
    }
    
}

