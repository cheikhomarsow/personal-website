<?php

namespace Controller;

use Model\UserManager;

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
            echo $this->renderView('reglog.html.twig');
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
   }

}
