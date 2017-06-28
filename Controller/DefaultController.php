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

        if($email == 'cheikhomar60@gmail.com'){
            $admin = true;
        }
        echo $this->renderView('home.html.twig',
            [
                'user'=>$user,
                'admin' => $admin,
                'availableArticle' => $availableArticle,
            ]);
    }
    public function articleAction()
    {
        $articleManager = ArticleManager::getInstance();
        $userManager = UserManager::getInstance();
        $tokenExist = true;
        $isLog = false;
        $token = $_GET['token'];
        $article = $articleManager->getArticleByToken($token);

        if(!$article){
            $tokenExist = false;
        }
        if(!empty($_SESSION['user_id'])){
            $isLog = true;
        }
        $user = $userManager->getUserById($_SESSION['user_id']);
        $autor = $userManager->getUserById($article['user_id']);

        echo $this->renderView('article.html.twig',
                                    [
                                        'article' => $article,
                                        'user' => $user,
                                        'autor' => $autor,
                                        'tokenExist' => $tokenExist,
                                        'isLog' => $isLog,
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
