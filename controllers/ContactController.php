<?php
require_once __DIR__ . '/../models/Contact.php';
class ContactController
{
    public function index(){ 
        $Contact= new Contact();
        $listeContacts= $Contact->findAll();

        header("content-type: application/json");
        http_response_code(200);
        echo json_encode([
            "message" => "GET contact success", "Total de contacts"=> count($listeContacts) ,"Contacts" => $listeContacts]);
    }



    public function show($id)
    {   $id=(int)$id;
        if (!is_numeric($id)|| !is_int($id)) { //Toujours vérifier si l'Id deemandé est bien numérique et entier
            http_response_code(400); // Mauvaise requête (Bad Request)
            echo json_encode(['error' => 'ID invalide, valeur numérique entière requise']);
            exit;
        }
        

        if (!($id>=1)) { //Vérifier si l'Id deemandé n'est pas négatif et à minima égal à 1
            http_response_code(400); // Mauvaise requête (Bad Request)
            echo json_encode(['error' => 'ID invalide, ID valeur inférieure à 1']);
            exit;
        }

        $contact= new Contact();
        $contactbyId = $contact->findById($id);
        
        header("content-type: application/json");
        if ($contactbyId) {
            http_response_code(200);
            echo json_encode(["message" => "Get Contact ID : $id", "data"=> $contactbyId]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Contact non trouvé"]);
        }
    }


    public function store()// Récupérer les données JSON POSTées
    {
        header("Content-Type: application/json");
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || 
            !isset($input['lastname']) || 
            !isset($input['firstname']) ||
            !isset($input['email']) || 
            !isset($input['phone'])) {
            
            http_response_code(400);
            echo json_encode(["error" => "Données incomplètes ou invalides"]);
            return;
        }

        $contact = new Contact();

        try {
            $newId = $contact->insert($input);

            http_response_code(201); // Création réussie
            echo json_encode([
                "message" => "Contact créé avec succès",
                "id" => $newId
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la création du contact : " . $e->getMessage()]);
        }
    }


    public function update($id)
    {
        echo json_encode(["message" => "Mise à jour complète du contact ID $id (PUT)"]);
    }

    public function patch($id)
    {
        echo json_encode(["message" => "Mise à jour partielle du contact ID $id (PATCH)"]);
    }

    public function destroy($id)
    {
        echo json_encode(["message" => "Suppression du contact ID $id"]);
    }
}
