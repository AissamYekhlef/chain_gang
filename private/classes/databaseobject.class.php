<?php

class DatabaseObject {

    static protected $database;
    static protected $table_name = "";
    static protected $db_columns = [];
    public $errors = [];

    public static function set_database($database){
        self::$database = $database;
    }
    public static function get_connection(){
        return self::$database;
    }


    public static function find_by_sql($sql){
        $result = self::$database->query($sql);
        if(!$result){
            exit("Database query failed.");
        }

        // results into objects
        $object_array = [];
        while($record = $result->fetch_assoc()){
            $object_array[] = static::instantiate($record);
        }

        $result->free();

        return $object_array; 
    }

    public static function find_by_id($id){
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE id='" . static::$database->escape_string($id) ."'";

        $obj_array = static::find_by_sql($sql);
        if(!empty($obj_array)){
            return array_shift($obj_array);
        }else {
            return FALSE;
        }
        
    }

    public static function count_all(){
        $sql = "SELECT COUNT(*) FROM ". static::$table_name;
        $result_set = self::$database->query($sql);
        $row = $result_set->fetch_array();
        return array_shift($row);
    }
    public static function find_all(){
        $sql = "SELECT * FROM ". static::$table_name;
        $result = static::find_by_sql($sql);
        return $result; 
    }

    public static function instantiate($record){
        $object = new static;

        foreach($record as $property => $value){
            if(property_exists($object, $property)){
                $object->$property = $value;
            }
        }

        return  $object;
    }

    public function create_1(){
        $sql = "INSERT INTO " . static::$table_name . " (";
        $sql .= "brand, model, year, category, color, gender, price, weight_kg, condition_id, description";
        $sql .= ") VALUES (";
        $sql .= "'" . $this->brand ."', ";
        $sql .= "'" . $this->model ."', ";
        $sql .= "'" . $this->year ."', ";
        $sql .= "'" . $this->category ."', ";
        $sql .= "'" . $this->color ."', ";
        $sql .= "'" . $this->gender ."', ";
        $sql .= "'" . $this->price ."', ";
        $sql .= "'" . $this->weight_kg ."', ";
        $sql .= "'" . $this->condition_id ."', ";
        $sql .= "'" . $this->description ."'";
        $sql .= ")";


        $result = self::$database->query($sql);
        if($result){
            $this->id = self::$database->insert_id;
        }
        return $result;
    }

    protected function validate(){
        $this->errors = [];

        //Add custom validations
        return $this->errors;
    }

    // improved
    protected function create(){
        $this->validate();
        if(!empty($this->errors)) { return false; }

        $attributes = $this->sanitized_attributes();//sanitized_attributes()
        $sql = "INSERT INTO " . static::$table_name . " (";
        $sql .= join(', ', array_keys($attributes)); //array_keys($attributes)
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";


        $result = self::$database->query($sql);
        if($result){
            $this->id = self::$database->insert_id;
        }
        return $result;
    }
    protected function update(){
        $this->validate();
        if(!empty($this->errors)) { return false; }
        
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = [];
        foreach($attributes as $key => $value){
            $attribute_pairs[] = "{$key}='{$value}'";
        }

        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= join(', ',  $attribute_pairs);
        $sql .= " WHERE id='". self::$database->escape_string($this->id) . "' ";
        $sql .= "LIMIT 1";

        $result = self::$database->query($sql);
       
        return $result;
    }
    public function save(){
        // A new record will not have an ID yet
        if(isset($this->id)){
            return $this->update();
        }else {
            return $this->create();
        }
    }
    public function delete(){
        $sql = "DELETE FROM " . static::$table_name . " ";
        $sql .= "WHERE id='" . self::$database->escape_string($this->id) . "' ";
        $sql .= "LIMIT 1";
        $result = self::$database->query($sql);
        return $result;

    }

    // Properties which have database columns, excluding ID
    public function attributes(){
        $attributes = [];
        foreach(static::$db_columns as $column){
            if($column == 'id'){ continue; }
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    } 
    
    // to sanitized the value befor input it to db like ' => \'
    public function sanitized_attributes(){
        $sanitized = [];
        foreach(static::attributes() as $key => $value){
            $sanitized[$key] = self::$database->escape_string($value);
        }
        return $sanitized;
    }

    public function merge_attributes($args = []){
        foreach($args as $key => $value){
            if(property_exists($this, $key) && !is_null($value)){
                $this->$key = $value;
            }
        }
    }
   
}

?>