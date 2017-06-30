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


        if($email == 'cheikhomar60@gmail.com'){
            $admin = true;
        }
        echo $this->renderView('home.html.twig',
            [
                'user'=>$user,
                'admin' => $admin,
                'availableArticle' => $availableArticle,
                'autor' => $autor,
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

        if(!$article){
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res = $articleManager->checkComment($_POST);
            if($res['isFormGood']){
                $articleManager->addComment($_POST);
            }
        }

        $availableComment = $articleManager->availableComment($article['id']);


        foreach ($availableComment as $value){
            $userComment = $userManager->getUserById($value['user_id'])['username'];
            $comments[$value['id']] = ['userComment' => $userComment, 'contentComment' => $value['content']];
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
        echo $this->renderView('contact.html.twig',
            [
                'user'=>$user,
                'admin' => $admin,
            ]);
    }
}
