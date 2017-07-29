<?php

namespace Controller;

use Model\ArticleManager;
use Model\UserManager;
use Model\ForumManager;
use Model\SearchManager;

class DefaultController extends BaseController
{
    public function homeAction()
    {
        $userManager = UserManager::getInstance();
        $userManager->auto_login();
        $articleManager = ArticleManager::getInstance();
        $forumManager = ForumManager::getInstance();
        $user_id = $_SESSION['user_id'];
        $user = $userManager->getUserById($user_id);
        $email = $user['email'];
        $admin = false;
        $availableArticle = $articleManager->availableArticle();
        $autor = $userManager->getUserByEmail("cheikhomar60@gmail.com");
        $commentsNumber = [];
        $questions = $forumManager->getQuestions();
        $countAnswers = [];

        foreach($questions as $value){
            $countAnswers[$value['id']] = $forumManager->countAnswers($value['id']);
        }


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
                'questions' => $questions,
                'countAnswers' => $countAnswers,
            ]);
    }
    public function searchAction()
    {
        $searchManager = SearchManager::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($searchManager->checkSearch($_POST)) {
                $search = str_replace(' ', '_', trim($_POST['search']));
                $this->redirect('search&q=' . $search);
                $searchManager->getArticlesLike($_POST['search']);
            }
        }

        if (isset($_GET['q']) && $_GET['q'] !== '') {
            $like = str_replace('_', ' ', $_GET['q']);
            $articles = $searchManager->getArticlesLike($like);
            $questions = $searchManager->getQuestionsLike($like);

            if (!$articles) {
                $messageArticle = 'Aucun article correspondant à votre recherche n\'a été trouvé...';
            }
            if (!$questions) {
                $messageQuestion = 'Aucune question correspondant à votre recherche n\'a été trouvé...';
            }

            echo $this->renderView('search.html.twig',
                [
                    'messageArticle' => $messageArticle,
                    'messageQuestion' => $messageQuestion,
                    'articles' => $articles,
                    'questions' => $questions,
                ]);
        }else{
            $messageArticle = 'Aucun article correspondant à votre recherche n\'a été trouvé...';
            $messageQuestion = 'Aucune question correspondant à votre recherche n\'a été trouvé...';

            echo $this->renderView('search.html.twig',
                [
                    'messageArticle' => $messageArticle,
                    'messageQuestion' => $messageQuestion,
                ]);
        }

    }
    public function articleAction()
    {
        $userManager = UserManager::getInstance();
        $userManager->auto_login();
        $articleManager = ArticleManager::getInstance();
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
        $userManager = UserManager::getInstance();
        $userManager->auto_login();
        $user_id = $_SESSION['user_id'];
        $user = $userManager->getUserById($user_id);
        $email = $user['email'];
        $admin = false;

        if($email == 'cheikhomar60@gmail.com'){
            $admin = true;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res = $userManager->checkContactMe($_POST);
            if($res['isFormGood']){
                $to      = 'cheikhomar60@gmail.com';
                $subject = 'COS - Contact';
                $message = 'Nom : ' . $_POST['username'] . "\r\n" . 'Email : ' . $_POST['email']."\r\n" . 'Sujet : ' . $_POST['sujet'] . "\r\n" . 'Message : ' .  "\r\n" . $_POST['message'];
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
    public function demo_ajaxAction(){
        $userManager = UserManager::getInstance();
        $userManager->auto_login();
        $user_id = $_SESSION['user_id'];
        $user = $userManager->getUserById($user_id);
        $email = $user['email'];
        $admin = false;
        $error = '';


        if($email == 'cheikhomar60@gmail.com'){
            $admin = true;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $manager = UserManager::getInstance();
            if ($manager->userCheckDemo($_POST))
            {
                /*$manager->userLogin($_POST['username']);
                $this->redirect('home');*/
            }
            else {
                $error = "Invalid username or password";
            }
        }
        echo $this->renderView('demo_ajax.html.twig', ['error' => $error,
                                                        'user'=>$user,
                                                        'admin' => $admin,
                                                        ]);
    }


}
