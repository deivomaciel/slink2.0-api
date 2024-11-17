<?php
    require __DIR__ . '/../db_connect.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    

    class UserController {
        private $database;

        public function __construct() {
            $this->database = new Database();
        }

        public function register($name, $email, $password) {
            $user = $this->database->register($name, $email, $password);

            if ($user) {
                $this->database->login($email, $password);

            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Erro ao cadastrar usuario']);
            }
        }

        public function login($email, $password) {
            $this->database->login($email, $password);
        }

        public function insertLink($url, $desc, $id_usuario) {
            $this->database->insertLink($url, $desc, $id_usuario);
        }

        public function getLink($id_user) {
            $this->database->getLinks($id_user);
        }

        public function deleteLink($id_link) {
            $this->database->deleteLink($id_link);
        }
    }
?>

     
    
