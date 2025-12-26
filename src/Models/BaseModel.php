<?php

namespace MyPlugin\Models;

abstract class BaseModel
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function find($id)
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = %d", $id)
        );
    }

    public function all()
    {
        return $this->wpdb->get_results("SELECT * FROM {$this->table}");
    }

    public function create($data)
    {
        $filtered_data = array_intersect_key($data, array_flip($this->fillable));
        
        $result = $this->wpdb->insert($this->table, $filtered_data);
        
        if ($result !== false) {
            return $this->find($this->wpdb->insert_id);
        }
        
        return false;
    }

    public function update($id, $data)
    {
        $filtered_data = array_intersect_key($data, array_flip($this->fillable));
        
        return $this->wpdb->update(
            $this->table,
            $filtered_data,
            [$this->primaryKey => $id]
        );
    }

    public function delete($id)
    {
        return $this->wpdb->delete($this->table, [$this->primaryKey => $id]);
    }
}