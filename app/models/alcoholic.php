<?php

class Alcoholic extends BaseModel{
    
    public $id, $username, $password;

    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Alcoholic');
        $query->execute();
        
        $rows = $query->fetchAll();
        $users = array();
        
        foreach($rows as $row) {
            
            $users[] = new Alcoholic(array(
                'id' => $row['id'],
                'username' => $row['username'],
                'password' => $row['password']
            ));
        }
        return $users;
    }
    
    public static function single($id) {
        $query = DB::connection()->prepare('SELECT * FROM Alcoholic WHERE id = :id');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        
        if($row) {
            $user = new Alcoholic(array(
                'id' => $row['id'],
                'username' => $row['username'],
                'password' => $row['password']
            ));
            return $user;
        }
        return null;
    }
}
