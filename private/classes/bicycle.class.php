<?php

class Bicycle {

    // ------ START OF ACTIVE RECORD CODE ------
    static public $database;
    static public $db_columns = ['id', 'brand', 'model', 'year', 'category', 'color', 'gender', 'price', 'weight_kg', 'condition_id', 'description'];
    public $errors = [];

    static public function set_database($database){
        self::$database = $database;
    }

    public static function find_by_sql($sql){
        $result = self::$database->query($sql);
        if(!$result){
            exit("Database query failed.");
        }

        // results into objects
        $object_array = [];
        while($record = $result->fetch_assoc()){
            $object_array[] = self::instantiate($record);
        }

        $result->free();

        return $object_array; 
    }

    public static function find_by_id($id){
        $sql = "SELECT * FROM bicycles ";
        $sql .= "WHERE id='" . self::$database->escape_string($id) ."'";

        $obj_array = self::find_by_sql($sql);
        if(!empty($obj_array)){
            return array_shift($obj_array);
        }else {
            return FALSE;
        }
        
    }

    public static function instantiate($record){
        $object = new self;

        foreach($record as $property => $value){
            if(property_exists($object, $property)){
                $object->$property = $value;
            }
        }

        return  $object;
    }

    public static function find_all(){
        $sql = "SELECT * FROM bicycles";
        $result = self::find_by_sql($sql);
        return $result; 
    }

    public function create_1(){
        $sql = "INSERT INTO bicycles (";
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

        if(is_blank($this->brand)) {
            $this->errors[] = "Brand cannot be blank.";
        }
        if(is_blank($this->model)) {
            $this->errors[] = "Model cannot be blank.";
        }
        
        return $this->errors;
    }

    // improved
    protected function create(){
        $this->validate();
        if(!empty($this->errors)) { return false; }

        $attributes = $this->sanitized_attributes();//sanitized_attributes()
        $sql = "INSERT INTO bicycles (";
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

        $sql = "UPDATE bicycles SET ";
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
        $sql = "DELETE FROM bicycles ";
        $sql .= "WHERE id='" . self::$database->escape_string($this->id) . "' ";
        $sql .= "LIMIT 1";
        $result = self::$database->query($sql);
        return $result;

    }

    // Properties which have database columns, excluding ID
    public function attributes(){
        $attributes = [];
        foreach(self::$db_columns as $column){
            if($column == 'id'){ continue; }
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    } 
    
    // to sanitized the value befor input it to db like ' => \'
    public function sanitized_attributes(){
        $sanitized = [];
        foreach(self::attributes() as $key => $value){
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

    // ------ END OF ACTIVE RECORD CODE ------


    public const CATEGORIES = ['road', 'mountain', 'hybird', 'cruiser', 'city', 'BMX'];

    public const GENDERS = ['mens', 'womems', 'unisex'];
    public const CONDITIONS = [
        '1' => 'Beat up', 
        '2' => 'Decent', 
        '3' => 'Good', 
        '4' => 'Great', 
        '5' => 'Like New'
    ];

    public $id;
    public $brand;
    public $model;
    public $year;
    public $category;
    public $color;
    public $description;
    public $gender;
    public $price;

    public $weight_kg;

    public $condition_id;

    public function __construct($args = []){
        $this->brand = $args['brand'] ?? NULL;
        $this->model = $args['model'] ?? NULL;
        $this->year = $args['year'] ?? NULL;
        $this->category = $args['category'] ?? NULL;
        $this->color = $args['color'] ?? NULL;
        $this->description = $args['description'] ?? NULL;
        $this->gender = $args['gender'] ?? NULL;
        $this->price = $args['price'] ?? 0;
        $this->weight_kg = $args['weight_kg'] ?? 0.0;
        $this->condition_id = $args['condition_id'] ?? 3;
    }

    public function name(){
        return "{$this->brand} {$this->model} {$this->year}";
    }

    public function weight_kg(){
        return $this->weight_kg . ' kg';
    }
    public function set_weight_kg($value){
        $this->weight_kg = $value;
    }
    public function weight_lbs(){
        return $this->weight_kg * 2 . ' lbs';
    }
    public function set_weight_lbs($value){
        $this->weight_kg = ($value / 2);
    }

    public function condition(){
        return self::CONDITIONS[$this->condition_id];
    }



}

?>