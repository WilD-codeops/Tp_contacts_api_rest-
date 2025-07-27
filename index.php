<?php
require_once "controllers/ContactController.php";
header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('?', $_SERVER['REQUEST_URI'])[0];
$segments = explode('/', trim($uri, '/'));

// Ex: /contacts/5 → ["contacts", "5"]
$resource = $segments[0] ?? null;
$id = $segments[1] ?? null;

switch ($resource) {
    case 'contacts':
        $controller = new ContactController();

        if ($method === 'GET' && !$id) {
            $controller->index(); // GET /contacts
        } elseif ($method === 'GET' && $id) {
            $controller->show($id); // GET /contacts/5
        } elseif ($method === 'POST') {
            $controller->store(); // POST /contacts
        } elseif ($method === 'PUT' && $id) {
            $controller->update($id); // PUT /contacts/5
        } elseif ($method === 'PATCH' && $id) {
            $controller->patch($id); // PATCH /contacts/5
        } elseif ($method === 'DELETE' && $id) {
            $controller->destroy($id); // DELETE /contacts/5
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Ressource non trouvée"]);
}
