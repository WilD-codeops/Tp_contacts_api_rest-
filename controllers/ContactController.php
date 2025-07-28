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

        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode(['error'=> 'email non valide ']);
            return;
        }

         //Validation du numéro de téléphone (France : 10 chiffres, commence par 0 ou format international)
         //Je me demandais où placer cette fonction car pas adapté pour le modèle et je latrouve génante pour le controlleur 
         // Il semblerait que par convention il faut la mettre dasn un fichier helper/utilitaire ou service validation . Je le ferai plus tard 
        function estNumTelephoneValide($numero) {
            $num = preg_replace('/[\s\-.]/', '', $numero);
            // 0XXXXXXXXX ou +33XXXXXXXXX
            if (preg_match('/^0[1-9][0-9]{8}$/', $num)) return true; //regex
            if (preg_match('/^\+33[1-9][0-9]{8}$/', $num)) return true;
            return false;
        }

        if (!estNumTelephoneValide($input['phone'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Numéro de téléphone invalide (format attendu : 06XXXXXXXX ou +336XXXXXXXX)']);
            return;
        }

        $contact = new Contact();
        $contactsList= $contact->findAll();

        //J'utilise un foreach mais je sais que je pourrais trés bien creer une fonction findbyemail dans le modèle et l'appeler ça serait plus propre 
        //je n'en ai pas besoin pour le moment je veux 'abord finir mon modèle 
        foreach ($contactsList as $contact ) {
            if($contact['email']===$input['email']){
                http_response_code(409);//conflit
                echo json_encode(['error' => 'Email deja utilisé']);
                return;
            }
            if ($contact['phone'] === $input['phone']) {
                http_response_code(409); // Conflit
                echo json_encode(["error" => "Numéro de téléphone déjà utilisé"]);
                return;
            }
        }

        try {
            $newId = $contact->insert($input);

            http_response_code(201); // Création réussie
            echo json_encode([
                "message" => "Nouveau contact".$input['lastname']." ". $input['firstname'] . "créé avec succès",
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
