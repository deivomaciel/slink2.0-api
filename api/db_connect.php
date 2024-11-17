<?php
    class Database {
        private $host;
        private $port;
        private $dbname;
        private $user;
        private $password;
        private $connection;

        public function __construct() {
            $this->host = getenv('DB_HOST');
            $this->port = getenv('DB_PORT');
            $this->dbname = getenv('DB_NAME');
            $this->user = getenv('DB_USER');
            $this->password = getenv('DB_PASSWORD');
            $this->connection = $this->connect();
        }

        private function connect() {
            try {
                $dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->dbname";

                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ];
                $conn = new PDO($dsn, $this->user, $this->password, $options);
                return $conn;

            } catch (PDOException $e) {
                echo "Erro ao conectar: " . $e->getMessage();
            }
        }

        private function verifyIfEmaiLExist($email) {
            $stmt = $this->connection->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                http_response_code(409);
                echo json_encode(['message' => 'E-mail ja cadastrado']);
                die;
            }
        }

        public function register($name, $email, $password) {
            $this->verifyIfEmaiLExist($email);

            $stmt = $this->connection->prepare("INSERT INTO \"usuario\" (nome, email, senha) VALUES (:name, :email, :password)");
            $stmt->bindParam(':name', $name); 
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);

            return $stmt->execute() ? true : false;
        }

        public function getLinks($id_user) {
            $stmt = $this->connection->prepare("
                SELECT id_link, url, descricao
                FROM usuario
                JOIN link ON link.id_usuario = usuario.id_usuario
                WHERE usuario.id_usuario = :id_usuario;
            ");
            
            $stmt->bindParam(':id_usuario', $id_user);
            $stmt->execute();
            $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            $links_dict = [];
            foreach ($links as $link) {
                array_push($links_dict, [
                    'id' => $link['id_link'],
                    'url' => $link['url'],
                    'descricao' => $link['descricao']
                ]);
            }
        
            echo json_encode($links_dict);
        }

        
        public function login($email, $password) {
            $stmt = $this->connection->prepare("SELECT id_usuario, nome, email from \"usuario\" WHERE email = :email AND senha = :password");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);

            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user) {
                echo json_encode([
                    'id' => $user['id_usuario'],
                    'email' => $user['email'],
                    'nome'=> $user['nome'],
                ]);

            } else {
                echo json_encode(['message' => 'Email ou senha invalidos']);
            }
        }

        public function insertLink($url, $desc, $id_usuario) {
            $stmt = $this->connection->prepare("INSERT INTO \"link\" (url, descricao , id_usuario) VALUES (:url, :desc, :id_usuario)");
            $stmt->bindParam(':url', $url); 
            $stmt->bindParam(':desc', $desc);
            $stmt->bindParam(':id_usuario', $id_usuario);

            if($stmt->execute()) {
                echo json_encode(['message' => 'ok']);
            } else {
                echo json_encode(['message' => 'error']);
            }
        }

        public function deleteLink($id_link) {
            $stmt = $this->connection->prepare("DELETE FROM \"link\" WHERE id_link = :id_link;");
            $stmt->bindParam(':id_link', $id_link);

            if($stmt->execute()) {
                echo json_encode(['message' => 'ok']);
            } else {
                echo json_encode(['message' => 'error']);
            }
        }
    }
?>
