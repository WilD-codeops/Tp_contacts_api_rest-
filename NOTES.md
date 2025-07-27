
# Notes personnelles - API Contacts PHP / MySQL

## 1. Problèmes rencontrés liés à l’environnement PHP

- Utiliser `php --ini` pour vérifier quel fichier `php.ini` est chargé.  
- Ajouter le chemin du PHP CLI dans la variable d’environnement PATH sous Windows.  
- Dans le fichier `php.ini`, activer les extensions nécessaires (ex: `extension=pdo_mysql` sans point-virgule).  
- Vérifier la directive `extension_dir` pour qu’elle pointe vers le bon dossier où les DLL sont présentes (ex: `C:/wamp64/bin/php/php8.4.0/ext`).  
- Redémarrer le terminal ou l’application après modification.  
- Vérifier la présence du fichier `php_pdo_mysql.dll` dans le dossier d’extensions.


## 2. Architecture et responsabilités

### 2.1 Modèle (`Model`)
- Contient **les méthodes qui exécutent les requêtes SQL**.  
- Gère la logique d’accès à la base de données.  
- Les méthodes prennent souvent des paramètres (par ex. `$id`, `$data`) et retournent des données (tableaux, objets).

### 2.2 Contrôleur (`Controller`)
- Crée une instance du modèle (ex: `Contact`).  
- Appelle les méthodes du modèle selon la requête reçue.  
- Gère la logique métier complémentaire (validation, traitement).  
- Définit les headers HTTP, notamment :  
  ```php
  header("Content-Type: application/json");
  ```
- Envoie les réponses (JSON) et les codes HTTP via `http_response_code()`.  
- Contrôle le flux selon la méthode HTTP (GET, POST, PUT, DELETE).

### 2.3 Front Controller (index.php)
- Configure la réponse HTTP (Content-Type).  
- Récupère la méthode HTTP avec :  
  ```php
  $method = $_SERVER['REQUEST_METHOD'];
  ```
- Récupère le chemin d’URL via `$_SERVER['REQUEST_URI']`, segmenté dans un tableau :  
  ```php
  $uri = explode('?', $_SERVER['REQUEST_URI']);
  $segments = explode('/', trim($uri, '/'));
  ```
- Récupère les différentes parties de l’URL (ressource, id) en utilisant l’opérateur de coalescence nulle (`??`), par exemple :  
  ```php
  $resource = $segments[0] ?? null;
  $id = $segments[1] ?? null;
  ```
- Utilise un `switch`/`case` pour router vers la bonne méthode du contrôleur selon la ressource + méthode HTTP.



## 3. Récupération des données JSON POSTées
- Les données JSON envoyées en requête POST **ne sont pas récupérables avec `$_POST`**.  
- Pour lire le contenu brut du corps HTTP (JSON ou autre), utiliser :  
  ```php
  $rawData = file_get_contents('php://input');
  ```
- `php://input` permet la lecture brute du corps HTTP, utile pour récupérer du JSON. 
- Puis transformer ce JSON en tableau PHP avec :  
  ```php
  $data = json_decode($rawData, true);
- Ce tableau `$data` peut ensuite être utilisé dans le code pour accéder aux valeurs.


## 4. Récupération des données pour insertion dans la base
- Dans les requêtes INSERT/UPDATE avec PDO, **pas besoin de `bindParam()` si tu passes directement un tableau associatif dans `execute()`**.  
- On utilise des placeholders nommés en SQL (`:lastname`, `:firstname`, etc.) et un tableau PHP dont les clés correspondent aux placeholders.  
- Exemple :  
  ```php
  $stmt = $db->prepare("INSERT INTO contacts (lastname, firstname) VALUES (:lastname, :firstname)");
  $stmt->execute([
      'lastname' => $data['lastname'],
      'firstname' => $data['firstname'],
  ]);
  ```
- Cela est plus simple quand on reçoit directement un tableau `$data` issu du JSON décodé.


## 5. Gestion des codes HTTP dans les réponses
- Les fonctions `http_response_code()` permettent d’indiquer le statut de la réponse (succès, erreur, etc.).  
- Exemples courants :

| Code  | Signification            | Usage courant dans l’API         |
|-------|--------------------------|----------------------------------|
| 200   | OK                       | Réponse réussie                  |
| 201   | Created (Ressource créée)| Création réussie (ex: POST)      |
| 400   | Bad Request              | Données invalides ou manquantes  |
| 404   | Not Found                | Ressource non trouvée            |
| 405   | Method Not Allowed       | Méthode HTTP non supportée       |
| 500   | Internal Server Error    | Erreur serveur                   |

- Toujours envoyer un message clair (JSON) pour expliquer l’état.


## 6. Liste rapide des codes HTTP utiles dans une API

| Code  | Signification          |
|-------|------------------------|
| 200   | Succès (OK)            |
| 201   | Ressource créée        |
| 400   | Requête mal formée     |
| 401   | Non autorisé           |
| 403   | Interdit               |
| 404   | Non trouvé             |
| 405   | Méthode non autorisée  |
| 500   | Erreur serveur         |


## 7. Conventions et bonnes pratiques
- Nommage clair et cohérent des fonctions (ex: `findAll()`, `findById()`, `insert()`, `update()`, `destroy()`).  
- Organiser le code en fichiers séparés (modèle, contrôleur, front controller).  
- Commenter le code régulièrement pour clarifier la logique.  
- Gérer proprement les erreurs (try/catch PDO, renvoyer des messages JSON structurés).  
- Prendre en compte la montée en charge (ex: prévoir un vrai routeur plus souple que le switch actuel).  
- Penser à ajouter des vues ou réponses plus riches dans de futures évolutions.


## 8. Notes sur l’évolution future
- Le routeur basé sur `switch` est limitant. À terme, envisager un vrai routeur PHP (ex: FastRoute) ou un micro-framework.  
- Implémenter des fonctions de vues ou templates.  
- Ajouter une gestion des erreurs plus fine, par ex. exceptions personnalisées.  
- Sécuriser l’API (validation stricte des données, authentification).


## Annexes utiles à noter
- L’opérateur de coalescence nulle `??` permet d’éviter des erreurs d’indexation avec des valeurs par défaut.  
- Préparer les requêtes avec PDO évite les injections SQL (`prepare()` + `execute()` avec placeholders).

