<?php

class Review extends BaseModel{
    
    public $id, $alcoholic_id, $drink_id, $reviewer, $rating, $description;

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
                'reviewer' => $row['reviewer'],
                'rating' => $row['rating'],
                'description' => $row['description']
            ));
        }
        return $reviews;
    }
    
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Review (alcoholic_id, drink_id, reviewer, rating, description)
                                    VALUES (:alcoholic_id, :drink_id, :reviewer, :rating, :description) RETURNING id');

        $query->execute(array('alcoholic_id' => $this->alcoholic_id, 'drink_id' => $this->drink_id, 'reviewer' => $this->reviewer, 
                                'rating' => $this->rating, 'description' => $this->description));
        $row = $query->fetch();

        $this->id = $row['id'];
    }
}
