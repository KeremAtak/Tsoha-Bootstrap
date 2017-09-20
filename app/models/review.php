<?php

class Review extends BaseModel{
    
    public $id, $alcoholic_id, $drink_id, $rating, $description;

    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    
    public static function find_by_drink_id($id){
        $query = DB::connection()->prepare('SELECT * FROM Review where drink_id = :id');
        $query->execute(array('id' => $id));
        
        $rows = $query->fetchAll();
        $reviews = array();
        
        foreach($rows as $row) {
            $reviews[] = new Ingredient(array(
                'id' => $row['id'],
                'alcoholic_id' => $row['alcoholic_id'],
                'drink_id' => $row['drink_id'],
                'rating' => $row['rating'],
                'description' => $row['description']
            ));
        }
        return $reviews;
    }
}