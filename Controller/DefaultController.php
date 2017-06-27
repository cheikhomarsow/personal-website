<?php

namespace Controller;

use Model\UserManager;

class DefaultController extends BaseController
{
    public function homeAction()
    {
        $manager = UserManager::getInstance();
        $user_id = $_SESSION['user_id'];
        $user = $manager->getUserById($user_id);
        $email = $user['email'];
        $admin = false;

        if($email == 'cheikhomar60@gmail.com'){
            $admin = true;
        }
        echo $this->renderView('home.html.twig',
            [
                'user'=>$user,
                'admin' => $admin,
            ]);
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
