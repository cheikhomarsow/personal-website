<?php

namespace Model;


class UserManager
{
    private $DBManager;
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new UserManager();
        return self::$instance;
    }

    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
    }

    public function getUserById($id)
    {
        $id = (int)$id;
        $data = $this->DBManager->findOne("SELECT * FROM users WHERE id = " . $id);
        return $data;
    }

    public function getUserByEmail($email)
    {
        $data = $this->DBManager->findOneSecure("SELECT * FROM users WHERE email = :email",
            ['email' => $email]);
        return $data;
    }
    public function getAllUsers()
    {
        $email = "cheikhomar60@gmail.com";
        return $this->DBManager->findAllSecure("SELECT * FROM users WHERE email !=:email ORDER BY registerDate DESC",['email'=> $email]);
    }


    public function userCheckRegister($data)
    {
        $errors = array();
        $res = array();
        $isFormGood = true;

        $username = trim($data['username']);
        if(empty($username)){
            $errors['username'] = 'Pseudo de 2 caractères minimum';
            $isFormGood = false;
        }else{
            if(strlen($username) < 2){
                $errors['username'] = 'Pseudo de 2 caractères minimum';
                $isFormGood = false;
            }
        }


        if (!$this->emailValid($data['email'])) {
            $errors['email'] = "email non valide";
            $isFormGood = false;
        }else{
            $data2 = $this->getUserByEmail($data['email']);
            if ($data2 !== false) {
                $errors['email'] = 'L\'adresse email est déjà utilisé';
                $isFormGood = false;
            }
        }

        if (!isset($data['password']) || !$this->passwordValid($data['password'])) {
            $errors['password'] = "Veiller saisir un mot de passe valide ";
            $isFormGood = false;
        }
        if($this->passwordValid($data['password']) && $data['password'] !== $data['verifpassword']){
            $errors['password'] = "Les deux mot de passe ne sont pas identiques";
            $isFormGood = false;
        }
        $res['isFormGood'] = $isFormGood;
        $res['errors'] = $errors;
        $res['data'] = $data;
        return $res;
    }


    private function emailValid($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /*private function usernameValid($username)
    {
        return preg_match('`^([a-zA-Z0-9- ]{2,20})$`', $username);
    }*/


    //Minimum : 8 caractères avec au moins une lettre majuscule et un nombre
    private function passwordValid($password)
    {
        return preg_match('`^([a-zA-Z0-9-_]{6,20})$`', $password);
    }


    private function userHash($pass)
    {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        return $hash;
    }

    public function userRegister($data)
    {
        $user['username'] = $data['username'];
        $user['email'] = $data['email'];
        $user['password'] = $this->userHash($data['password']);
        $user['registerDate'] = $this->DBManager->getDatetimeNow();
        $this->DBManager->insert('users', $user);
    }

    public function userCheckLogin($data)
    {
        $isFormGood = true;
        if (empty($data['email']) OR empty($data['password'])) {
            $isFormGood = false;
        }else{
            $user = $this->getUserByEmail($data['email']);
            if (!password_verify($data['password'], $user['password'])) {
                $isFormGood = false;
            }

        }
        return $isFormGood;
    }

    public function userCheckDemo($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        $isFormGood = true;
        if (empty($data['username'])) {
            $isFormGood = false;
            $errorsLogin['username'] = 'Ce champs est obligatoire';
        }else{
            if(!$this->usernameValid($data['username'])){
                $isFormGood = false;
                $errorsLogin['username'] = 'username non valide : entre 6 et 20 caractères';
            }
        }
        if (empty($data['password'])) {
            $isFormGood = false;
            $errorsLogin['password'] = 'Ce champs est obligatoire';
        }else{
            if(!$this->passwordValid($data['password'])){
                $isFormGood = false;
                $errorsLogin['password'] = 'password non valide : entre 6 et 20 caractères';
            }
        }
        if($isFormGood)
        {
            json_encode(array('success'=>true, 'user'=>$_POST));
        }
        else
        {
            echo(json_encode(array('success'=>false, 'errors'=>$errorsLogin), JSON_UNESCAPED_UNICODE ,http_response_code(400)));
            exit(0);
        }
        return $isFormGood;
    }
    private function usernameValid($username){
        return preg_match('`^([a-zA-Z0-9-_]{6,20})$`', $username);
    }


    public function userLogin($username)
    {
        if($this->emailValid($username)){
            $data = $this->getUserByEmail($username);
            if ($data === false)
                return false;
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['user_username'] = $data['username'];
            $date = $this->DBManager->take_date();
            $write = $date . ' -- ' . $_SESSION['user_username'] . ' is connected' . "\n";
            $this->DBManager->watch_action_log('access.log', $write);
            return true;
        }else{
            $data = $this->getUserByEmail($username);
            if ($data === false)
                return false;
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['user_username'] = $data['username'];
            $date = $this->DBManager->take_date();
            $write = $date . ' -- ' . $_SESSION['user_username'] . ' is connected' . "\n";
            $this->DBManager->watch_action_log('access.log', $write);
            return true;
        }

    }

    public function checkContact($data){
        $isFormGood = true;
        $errors = [];
        $res = [];

        $email = $data['email'];
        $user = $this->getUserById($_SESSION['user_id']);
        $username = trim($data['username']);
        if(empty($username)){
            $errors['username'] = 'Pseudo de 2 caractères minimum';
            $isFormGood = false;
        }
        else {
            if (strlen($username) < 2) {
                $errors['username'] = 'Pseudo de 2 caractères minimum';
                $isFormGood = false;
            }
        }
        if(!$this->emailValid($email)){
            $errors['email'] = "email non valide";
            $isFormGood = false;
        }else{
            $referee = $this->getUserByEmail($email);
            if($referee == true && $user['id'] != $referee['id']){
                $errors['email'] = "L'adresse email est déjà utilisé";
                $isFormGood = false;
            }
        }
        $res['isFormGood'] = $isFormGood;
        $res['errors'] = $errors;
        $res['data'] = $data;

        return $res;
    }
    public function updateContact($data){
        $username = $data['username'];
        $email = $data['email'];
        $id = $_SESSION['user_id'];
        return $this->DBManager->findOneSecure(
            "UPDATE users SET username = :username, email = :email WHERE id=:id",
            [
                'email' => $email,
                'username' => $username,
                'id' => $id
            ]);
    }
    public function checkPassword($data){
        $isFormGood = true;
        $errors = [];
        $res = [];

        $old = $data['oldPassword'];
        $new = $data['newPassword'];

        $user = $this->getUserById($_SESSION['user_id']);

        if(!password_verify($old, $user['password'])){
            $errors['password'] = "Le mot de passe n'est pas valide";
            $isFormGood = false;
        }else{
            if(!$this->passwordValid($new)){
                $errors['password'] = "Nouveau mot de passe non valide";
                $isFormGood = false;
            }
        }


        $res['isFormGood'] = $isFormGood;
        $res['errors'] = $errors;
        $res['data'] = $data;

        return $res;
    }
    public function updatePassword($data){
        $new = $this->userHash($data['newPassword']);
        $id = $_SESSION['user_id'];
        return $this->DBManager->findOneSecure(
            "UPDATE users SET password = :new WHERE id=:id",
            [
                'new' => $new,
                'id' => $id
            ]);
    }

    public function checkContactMe($data){
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');

        $isFormGood = true;
        $errors = [];
        $res = [];

        $email = $data['email'];
        $sujet = trim($data['sujet']);
        $message = trim($data['message']);
        $username = trim($data['username']);

        if(empty($username) ){
            $isFormGood = false;
        }else{
            if(strlen($username) < 2) {
                $isFormGood = false;
            }
        }
        if(empty($sujet) ){
            $isFormGood = false;
        }
        if(empty($message) ){
            $isFormGood = false;
        }else{
            if(strlen($username) < 2) {
                $isFormGood = false;
            }
            if(strlen($username) > 5000) {
                $isFormGood = false;
            }
        }

        if(!$this->emailValid($email)){
            $errors['erreur'] = "Adresse email non valide";
            $isFormGood = false;
        }
        if(!$isFormGood){
            $errors['erreur'] = "Veillez remplir tous les champs";
        }
        if($isFormGood)
        {
            echo(json_encode(array('success'=>true, 'data'=>$data), JSON_UNESCAPED_UNICODE ,http_response_code(200)));
            $res['isFormGood'] = $isFormGood;
            $res['errors'] = $errors;
            $res['data'] = $data;
            //exit(0);
            return $res;

        }else
        {
            echo(json_encode(array('error'=>false, 'error'=>$errors), JSON_UNESCAPED_UNICODE ,http_response_code(400)));
            exit(0);
        }
    }



}
