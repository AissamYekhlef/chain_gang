<?php

class Bicycle extends DatabaseObject {

     
    static public $table_name = 'bicycles';
    static public $db_columns = ['id', 'brand', 'model', 'year', 'category', 'color', 'gender', 'price', 'weight_kg', 'condition_id', 'description'];

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

    public static function table_name(){
        return self::$table_name;
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
        return $this->weight_kg * 2.2046226218 . ' lbs';
    }
    public function set_weight_lbs($value){
        $this->weight_kg = ($value / 2.2046226218);
    }

    public function condition(){
        return self::CONDITIONS[$this->condition_id];
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

}

?>