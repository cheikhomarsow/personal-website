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
        $token = $_GET['token'];
        $article = $articleManager->getArticleByToken($token);
        if(!$article){
            $tokenExist = false;
        }
        $user = $userManager->getUserById($article['user_id']);
        echo $this->renderView('article.html.twig',
                                    [
                                        'article' => $article,
                                        'user' => $user,
                                        'tokenExist' => $tokenExist,
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
