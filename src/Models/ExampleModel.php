<?php

namespace MyPlugin\Models;

class ExampleModel extends BaseModel
{
    protected $table;
    protected $fillable = ['name', 'email', 'status', 'created_at'];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->table = $wpdb->prefix . 'my_plugin_examples';
    }

    public function getByStatus($status)
    {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("SELECT * FROM {$this->table} WHERE status = %s", $status)
        );
    }

    public function getRecent($limit = 10)
    {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT %d", $limit)
        );
    }

    public static function createTable()
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'my_plugin_examples';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}