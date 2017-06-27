<?php

namespace Controller;

use Model\UserManager;

class UserController extends BaseController
{
    public function userAction()
    {
        if (!empty($_SESSION['user_id'])) {
            $manager = UserManager::getInstance();
            $user_id = $_SESSION['user_id'];
            $user = $manager->getUserById($user_id);
            $email = $user['email'];
            $admin = false;

            if($email == 'cheikhomar60@gmail.com'){
                $admin = true;
            }
            echo $this->renderView('user.html.twig',
                [
                    'user'=>$user,
                    'admin' => $admin,
                ]);
        }else{
            echo $this->redirect('reglog');
        }
    }
}
