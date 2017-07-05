<?php

namespace Controller;

use Model\ArticleManager;
use Model\UserManager;

class DefaultController extends BaseController
{
    public function homeAction()
    {
        $userManager = UserManager::getInstance();
        $articleManager = ArticleManager::getInstance();
        $user_id = $_SESSION['user_id'];
        $user = $userManager->getUserById($user_id);
        $email = $user['email'];
        $admin = false;
        $availableArticle = $articleManager->availableArticle();
        $autor = $userManager->getUserByEmail("cheikhomar60@gmail.com");
        $commentsNumber = [];


        foreach ($availableArticle as $article){
            $commentsNumber[$article['id']] = $articleManager->countComments($article['id']);
        }


        if($email == 'cheikhomar60@gmail.com'){
            $admin = true;
        }
        echo $this->renderView('home.html.twig',
            [
                'user'=>$user,
                'admin' => $admin,
                'availableArticle' => $availableArticle,
                'autor' => $autor,
                'commentsNumber' => $commentsNumber,
            ]);
    }
    public function articleAction()
    {
        $articleManager = ArticleManager::getInstance();
        $userManager = UserManager::getInstance();
        $tokenExist = true;
        $isLog = false;
        $admin = false;
        $token = $_GET['token'];
        $article = $articleManager->getArticleByToken($token);
        $comments = [];
        $commentsNumber = [];

        $articleManager->checkIP($articleManager->get_ip(), $token);

        if(!$article || ($article && ($article['visible']) == 0)){
            $tokenExist = false;
        }

        if(!empty($_SESSION['user_id'])){
            $isLog = true;
        }
        $user = $userManager->getUserById($_SESSION['user_id']);
        $autor = $userManager->getUserById($article['user_id']);

        $email = $user['email'];

        if($email == 'cheikhomar60@gmail.com'){
            $admin = true;
        }
        $commentsNumber[$article['id']] = $articleManager->countComments($article['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res = $articleManager->checkComment($_POST);
            if($res['isFormGood']){
                $articleManager->addComment($_POST);
            }
        }

        $availableComment = $articleManager->availableComment($article['id']);


        foreach ($availableComment as $value){
            $userComment = $userManager->getUserById($value['user_id'])['username'];
            $email2 = $userManager->getUserById($value['user_id'])['email'];
            $adminComment = false;
            if($email2 == "cheikhomar60@gmail.com"){
                $adminComment = true;
            }
            $comments[$value['id']] = [
                'userComment' => $userComment,
                'contentComment' => $value['content'],
                'dateComment' => $value['date'],
                'adminComment' => $adminComment,
            ];
        }


        echo $this->renderView('article.html.twig',
                                    [
                                        'article' => $article,
                                        'user' => $user,
                                        'autor' => $autor,
                                        'tokenExist' => $tokenExist,
                                        'isLog' => $isLog,
                                        'comments' => $comments,
                                        'admin' => $admin,
                                        'commentsNumber' => $commentsNumber,
                                    ]
            );
    }

    public function contactAction(){
        $manager = UserManager::getInstance();
        $user_id = $_SESSION['user_id'];
        $user = $manager->getUserById($user_id);
        $email = $user['email'];
        $admin = false;

        if($email == 'cheikhomar60@gmail.com'){
            $admin = true;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res = $manager->checkContactMe($_POST);
            if($res['isFormGood']){
                $to      = 'cheikhomar60@gmail.com';
                $subject = 'COS - Contact';
                $message = "Nom : " . $_POST['username'] . "<br>Email : " . $_POST['email']."<br>Message : <br>".$_POST['message'];
                $headers = 'From: postmaster@cheikhomarsow.ovh' . "\r\n" .
                    'Reply-To: postmaster@cheikhomarsow.ovh' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                mail($to, $subject, $message, $headers);
            }
        }
        echo $this->renderView('contact.html.twig',
            [
                'user'=>$user,
                'admin' => $admin,
            ]);
    }
}
