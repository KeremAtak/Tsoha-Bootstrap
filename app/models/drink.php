<?php

class Drink extends BaseModel{
    
    public $id, $alcoholic_id, $name, $volume, $alcohol_percentage, $description;

    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Drink');
        $query->execute();
        
        $rows = $query->fetchAll();
        $drinks = array();
        
        foreach($rows as $row) {
            
            $drinks[] = new Drink(array(
                'id' => $row['id'],
                'alcoholic_id' => $row['alcoholic_id'],
                'name' => $row['name'],
                'volume' => $row['volume'],
                'alcohol_percentage' => $row['alcohol_percentage'],
                'description' => $row['description']
            ));
        }
        return $drinks;
    }
    
    public static function single($id) {
        $query = DB::connection()->prepare('SELECT * FROM Drink WHERE id = :id');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        
        if($row) {
            $drink = new Drink(array(
                'id' => $row['id'],
                'alcoholic_id' => $row['alcoholic_id'],
                'name' => $row['name'],
                'volume' => $row['volume'],
                'alcohol_percentage' => $row['alcohol_percentage'],
                'description' => $row['description']
            ));
            return $drink;
        }
        return null;
    }
    
    public static function find_by_user_id($id) {
        $query = DB::connection()->prepare('SELECT * FROM Drink WHERE alcoholic_id = :id');
        $query->execute(array('id' => $id));
        
        $rows = $query->fetchAll();
        $drinks = array();
        
        foreach($rows as $row) {
            
            $drinks[] = new Drink(array(
                'id' => $row['id'],
                'alcoholic_id' => $row['alcoholic_id'],
                'name' => $row['name'],
                'volume' => $row['volume'],
                'alcohol_percentage' => $row['alcohol_percentage'],
                'description' => $row['description']
            ));
        }
        return $drinks;
    }
    
    public static function find_alcoholic_id($id) {
        $query = DB::connection()->prepare('SELECT alcoholic_id FROM Drink WHERE id = :id');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        
        if($row) {
            return $row['alcoholic_id'];
        }
        return null;
    }
    
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Drink (alcoholic_id, name, volume, alcohol_percentage, description)
                                    VALUES (:alcoholic_id, :name, :volume, :alcohol_percentage, :description) RETURNING id');

        $query->execute(array('alcoholic_id' => $this->alcoholic_id, 'name' => $this->name, 'volume' => $this->volume, 
                                'alcohol_percentage' => $this->alcohol_percentage, 'description' => $this->description));
        $row = $query->fetch();

        $this->id = $row['id'];
    }
    
    public function update() {
        $query = DB::connection()->prepare('UPDATE Drink SET rating = :rating WHERE id = :id');

        $query->execute(array('rating' => $this->rating, 'id' => $this->id));
    }
}
