<?php

class Contact {
    private $id;
    private $lastname;
    private $firstname;
    private $email;
    private $phone;
    private $avatar;
    private $db;
    public function __construct(){
        try{

        $dns = "mysql:host=localhost;dbname=api-contacts;charset=utf8";

        $this->db = new PDO($dns, "root", "root", [

            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC

    ]);

    }catch(PDOException $e){

        echo "Erreur de connexion à la base de données: " . $e->getMessage();

        exit;

    }
    }
    
    // methodes en lien la bdd 
    public function findAll() {
        $sql = "SELECT * FROM contacts";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }
    public function findById($id) {
        $sql = "SELECT * FROM contacts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
        
    }


    public function insert($data) {
        $sql = "INSERT INTO contacts (lastname, firstname, email, phone, avatar) 
                VALUES (:lastname, :firstname, :email, :phone, :avatar)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'lastname' => $data['lastname'],
            'firstname' => $data['firstname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'avatar' => $data['avatar'] ?? null
        ]);
        return $this->db->lastInsertId(); // retourne l’id inséré
    }


    public function updateContact() {
       
    }


  public function getId() {
    return $this->id;
  }
  public function setId($id) {
    $this->id = $id;
  }

  public function getLastname() {
        return $this->lastname;
    }
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function getFirstname() {
        return $this->firstname;
    }
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

  public function getEmail() {
    return $this->email;
  }

  public function setEmail($email) {
    $this->email = $email;
  }

  public function getPhone() {
    return $this->phone;
  }

  public function setPhone($phone) {
    $this->phone = $phone;
  }

    /**
     * Get the value of avatar
     */ 
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set the value of avatar
     *
     * @return  self
     */ 
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }
}