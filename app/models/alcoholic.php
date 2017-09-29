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
    
    
    public static function authenticate($username, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Alcoholic WHERE username = :username AND password = :password LIMIT 1');
        $query->execute(array('username' => $username, 'password' => $password));
        $row = $query->fetch();
        if($row){
          $user = new Alcoholic(array(
                'id' => $row['id'],
                'username' => $row['username'],
                'password' => $row['password']
            ));
            return $user;
        }else{
          return null;
        }
    }
    
    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Alcoholic (username, password)
                                    VALUES (:username, :password) RETURNING id');

        $query->execute(array('username' => $this->username, 'password' => $this->password));
                
        $row = $query->fetch();

        $this->id = $row['id'];
    }
    
    public static function get_user_logged_in(){
    if(isset($_SESSION['user'])){
        $id = $_SESSION['user'];
        $alcoholic = Alcoholic::single($id);

        return $alcoholic;
      }
      return null;
    }
    
    public static function get_user_logged_in_id(){
    if(isset($_SESSION['user'])){
        $id = $_SESSION['user'];
        return $id;
      }
      return null;
    }
    
    public static function is_logged_in(){
    if(isset($_SESSION['user'])){
        return TRUE;
      }
      return FALSE;
    }
}
