<?php

namespace Model;


class ArticleManager
{
    private $DBManager;
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new ArticleManager();
        return self::$instance;
    }

    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
    }

    public function getArticleByToken($token){
        return $this->DBManager->findOneSecure("SELECT * FROM articles WHERE visible = 1 AND token =:token",
                                            ['token' => $token]
            );
    }


    public function checkArticle($data)
    {
        $errors = array();
        $res = array();
        $isFormGood = true;


        if(isset($_FILES['file']['name']) && !empty($_FILES)){
            $data['file'] = $_FILES['file']['name'];
            $data['file_tmp_name'] = $_FILES['file']['tmp_name'];
            $res['data'] = $data;
        }

        if (!isset($data['title']) || $data['title'] == '') {
            $errors['title'] = 'Veillez remplir le titre';
            $isFormGood = false;
        }

        if (!isset($data['editor1']) || $data['editor1'] == '') {
            $errors['editor1'] = 'Veillez remplir le message';
            $isFormGood = false;
        }
        if (!isset($data['visible']) || ($data['visible'] !== '0' && $data['visible'] !== '1')) {
            $errors['visible'] = 'Veillez remplir le champs visible ?';
            $isFormGood = false;
        }
        $res['isFormGood'] = $isFormGood;
        $res['errors'] = $errors;
        $res['data'] = $data;
        return $res;
    }

    public function addArticle($data){
        if($data['file'] == ''){
            $pathImage = 'assets/img/default-image.jpg';
        }else{
            $pathImage = 'uploads/cosinus/'.$data['file'];
        }
        $article['user_id'] = $_SESSION['user_id'];
        $article['title'] = $data['title'];
        $article['content'] =  $data['editor1'];
        $article['file'] = $pathImage;
        $article['date'] = $this->DBManager->getDatetimeNow();
        $article['token'] = $this->token();
        $article['visible'] =  (int)$data['visible'];
        $this->DBManager->insert('articles', $article);
        move_uploaded_file($data['file_tmp_name'],$pathImage);
        chmod($pathImage, 0666);
    }

    public function availableArticle(){
        return $this->DBManager->findAllSecure("SELECT * FROM articles WHERE visible = 1 ORDER BY date DESC");
    }

    public function token()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 20; $i++) {
            $randstring .= $characters[mt_rand(0, strlen($characters))];
        }
        return $randstring;
    }






}
