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
    public function allUsers()
    {
        return $this->DBManager->findAllSecure("SELECT * FROM users");
    }




    public function userCheckRegister($data)
    {
        $errors = array();
        $res = array();
        $isFormGood = true;

        if (!isset($data['username']) || !$this->usernameValid($data['username'])) {
            $errors['username'] = 'Pseudo de 2 caractères minimum';
            $isFormGood = false;
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

    private function usernameValid($username)
    {
        return preg_match('`^([a-zA-Z0-9]{2,20})$`', $username);
    }


    //Minimum : 8 caractères avec au moins une lettre majuscule et un nombre
    private function passwordValid($password)
    {
        return preg_match('`^([a-zA-Z0-9-_]{8,20})$`', $password);
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


}
