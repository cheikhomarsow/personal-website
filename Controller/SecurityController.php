<?php

namespace Controller;

use Model\UserManager;
use Model\ArticleManager;

class SecurityController extends BaseController
{

    public function logoutAction()
    {
        session_destroy();
        echo $this->redirect('home');
    }




    public function reglogAction(){
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('user');
        }else{

            $from = '';
            $token = '';

            if($_GET['from'] != NULL AND $_GET['token'] != NULL) {
                $from = $_GET['from'];
                $token = $_GET['token'];
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['login'])) {
                    $userManager = UserManager::getInstance();
                    if ($userManager->userCheckLogin($_POST)) {
                        $userManager->userLogin($_POST['email']);
                        if($from != NULL && $token != NULL){
                            $token = $_GET['token'];
                            $route = "article&from=".$from."&token=".$token;
                            echo $this->redirect($route);
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
                    $manager = UserManager::getInstance();
                    $result = $manager->userCheckRegister($_POST);
                    if ($result['isFormGood']) {
                        $manager->userRegister($result['data']);
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
       if (!empty($_SESSION['user_id'])) {
           $manager = UserManager::getInstance();
           $admin = false;
           $user_id = $_SESSION['user_id'];
           $user = $manager->getUserById($user_id);
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

}
