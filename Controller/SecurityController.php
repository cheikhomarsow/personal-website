<?php

namespace Controller;

use Model\UserManager;
use Model\ArticleManager;

class SecurityController extends BaseController
{

    public function logoutAction()
    {

        $userManager = UserManager::getInstance();
        //on supprime l'entrée en bdd au niveau de auth_tokens
        $userManager->deleteTokens($_SESSION['user_id']);

        //supprimer les cookies et detruire la session
        setcookie('auth','',time()-3600);

        session_destroy();
        echo $this->redirect('home');
    }




    public function reglogAction(){
        $userManager = UserManager::getInstance();
        $userManager->auto_login();
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('user');
        }else{

            $from = '';
            $token = '';

            if($_GET['from'] != NULL) {
                $from = $_GET['from'];
            }
            if($_GET['token'] != NULL){
                $token = $_GET['token'];
            }


            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['login'])) {
                    //on check les infos de l'utilisateur
                    if ($userManager->userCheckLogin($_POST)) {
                        //on demarre la session
                        $userManager->userLogin($_POST['email']);

                        //Si l'utilisateur choisit l'option "Se souvenir de moi"
                        if(isset($_POST['remember_me']) && $_POST['remember_me'] == "on"){
                            $data = $userManager->getUserByEmail($_POST['email']);
                            /*
                             * on fait appel a la fonction
                             * remember_me
                             * avec comme paramètre
                             * l'id de l'utilisateur connecté
                             */
                            $userManager->rememberMe($data['id']);
                        }
                        if($from != NULL && $token != NULL){
                            $token = $_GET['token'];
                            $route = $from."&token=".$token;
                            echo $this->redirect($route);
                        }
                        elseif ($from != NULL){
                            echo $this->redirect($from);
                        }else{
                            if($_POST['email'] == 'cheikhomar60@gmail.com'){
                                echo $this->redirect('admin');
                            }else{
                                echo $this->redirect('user');
                            }

                        }
                    }else{
                        echo $this->renderView('reglog.html.twig', [
                            'loginErrors' => 'Adresse email ou mot de passe incorrect',
                            'from' => $from,
                            'token'=>$token
                            ]);
                    }
                }
                if (isset($_POST['register'])) {
                    $result = $userManager->userCheckRegister($_POST);
                    if ($result['isFormGood']) {
                        $userManager->userRegister($result['data']);
                        echo $this->renderView('reglog.html.twig', ['registerSuccess' => 'Inscription reussie !','from' => $from, 'token'=>$token]);
                    }
                    else {
                        $errors = $result['errors'];
                        echo $this->renderView('reglog.html.twig', ['registerErrors' => $errors,'from' => $from, 'token'=>$token]);
                    }
                }
            }else{
                echo $this->renderView('reglog.html.twig',['from' => $from, 'token'=>$token]);
            }

        }
    }

   public function adminAction(){
       $userManager = UserManager::getInstance();
       $userManager->auto_login();
       if (!empty($_SESSION['user_id'])) {
           $admin = false;
           $user_id = $_SESSION['user_id'];
           $user = $userManager->getUserById($user_id);
           $email = $user['email'];
           if($email == 'cheikhomar60@gmail.com'){
               if($email == 'cheikhomar60@gmail.com'){
                   $admin = true;
               }
               echo $this->renderView('admin.html.twig',
                   [
                       'user'  => $user,
                       'admin' => $admin,
                   ]);
           }else{
               $this->redirect('user');
           }
       }else{
           $this->redirect('user');
       }
   }
   public function admin_usersAction(){
       $userManager = UserManager::getInstance();
       $userManager->auto_login();
       if (!empty($_SESSION['user_id'])) {
           $articleManager = ArticleManager::getInstance();
           $admin = false;
           $user_id = $_SESSION['user_id'];
           $user = $userManager->getUserById($user_id);
           $email = $user['email'];
           if($email == 'cheikhomar60@gmail.com'){
               if($email == 'cheikhomar60@gmail.com'){
                   $admin = true;
               }
               if($_SERVER['REQUEST_METHOD'] === 'POST'){
                   $articleManager->removeUser($_POST['user_id']);
               }
               $allUsers = $userManager->getAllUsers();
               echo $this->renderView('admin_users.html.twig',
                   [
                       'user'  => $user,
                       'admin' => $admin,
                       'allUsers' => $allUsers,
                   ]);
           }else{
               $this->redirect('user');
           }
       }else{
           $this->redirect('user');
       }
   }
    public function admin_articlesAction(){
        $userManager = UserManager::getInstance();
        $userManager->auto_login();
        if (!empty($_SESSION['user_id'])) {
            $articleManager = ArticleManager::getInstance();
            $admin = false;
            $user_id = $_SESSION['user_id'];
            $autor = $userManager->getUserByEmail("cheikhomar60@gmail.com");
            $user = $userManager->getUserById($user_id);
            $email = $user['email'];

            if($email == 'cheikhomar60@gmail.com'){
                if($email == 'cheikhomar60@gmail.com'){
                    $admin = true;
                }
                if($_SERVER['REQUEST_METHOD'] === 'POST'){
                    $articleManager->removeArticle($_POST['article_id']);
                }
                $allArticle = $articleManager->allArticle();
                echo $this->renderView('admin_articles.html.twig',
                    [
                        'user'  => $user,
                        'admin' => $admin,
                        'autor' => $autor,
                        'allArticle' => $allArticle,
                    ]);
            }else{
                $this->redirect('user');
            }
        }else{
            $this->redirect('user');
        }
    }
    public function admin_commentsAction(){
        $userManager = UserManager::getInstance();
        $userManager->auto_login();
        if (!empty($_SESSION['user_id'])) {
            $articleManager = ArticleManager::getInstance();
            $admin = false;
            $user_id = $_SESSION['user_id'];
            $user = $userManager->getUserById($user_id);
            $email = $user['email'];

            if($email == 'cheikhomar60@gmail.com'){
                if($email == 'cheikhomar60@gmail.com'){
                    $admin = true;
                }
                if($_SERVER['REQUEST_METHOD'] === 'POST'){
                    $articleManager->removeComment($_POST['comment_id']);
                }

                $allComments = $articleManager->getAllComments();

                echo $this->renderView('admin_comments.html.twig',
                    [
                        'user'  => $user,
                        'admin' => $admin,
                        'allComments' => $allComments,
                    ]);
            }else{
                $this->redirect('user');
            }
        }else{
            $this->redirect('user');
        }
    }

}
