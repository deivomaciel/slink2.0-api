<?php
    header('Content-Type: application/json');
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestUri = $_SERVER['REQUEST_URI'];

    require __DIR__ . '/Controllers/user_controller.php';

    function verifyError() {
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erro ao decodificar JSON: " . json_last_error_msg());
        }
    }

    function getData() {
        return json_decode(file_get_contents('php://input'), true);
    }

    $user_controller = new UserController();

    switch ($requestMethod) {
        case 'POST':
            if (strpos($requestUri, '/register') !== false) {
                $data = getData();
                verifyError();

                $name = $data["name"] ?? null;
                $email = $data["email"] ?? null;
                $password = $data["password"] ?? null;

                if(!$name || !$email || !$password) {
                    throw new Exception('name, email, or password not valid!');
                }

                $user_controller->register($name, $email, $password);
            }

            if(strpos($requestUri, '/login') !== false) {
                $data = getData();
                verifyError();

                $email = $data["email"] ?? null;
                $password = $data["password"] ?? null;

                if(!$email || !$password) {
                    throw new Exception('email, or password not valid!');
                }

                $user_controller->login($email, $password);
            }

            if(strpos($requestUri, '/insert') !== false) {
                $data = getData();
                verifyError();

                $url = $data["url"] ?? null;
                $desc = $data["desc"] ?? null;
                $id_user = $data["id_user"] ?? null;

                if(!$url || !$desc || !$id_user) {
                    throw new Exception('url, desc or id_user not valid!');
                }

                $user_controller->insertLink($url, $desc, $id_user);
            }

            if(strpos($requestUri, '/getlink') !== false) {
                $data = getData();
                verifyError();

                $id_user = $data["id_user"] ?? null;

                if(!$id_user) {
                    throw new Exception('id_user not valid!');
                }

                $user_controller->getLink($id_user);
            }

            if(strpos($requestUri, '/deletelink') !== false) {
                $data = getData();
                verifyError();

                $id_link = $data["id_link"] ?? null;

                if(!$id_link) {
                    throw new Exception('id_link not valid!');
                }

                $user_controller->deleteLink($id_link);
            }

            break;

        case 'GET':
            if (strpos($requestUri, '/usuarios') !== false) {
                echo "teste";

            } else if (strpos($requestUri, '/') !== false){
                echo 'Teste API';
            }
            break;
        
        default:
            echo json_encode(['message' => 'Método não suportado']);
            break;
    }
