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

            if(isset($_POST['updateContact'])){
                $res = $manager->checkContact($_POST);
                if($res['isFormGood']){
                    $manager->updateContact($res['data']);
                    header('Location: ?action=user');
                }else{
                    $errorsUpdateContact = $res['errors'];
                }
            }
            if(isset($_POST['updatePassword'])){
                $res = $manager->checkPassword($_POST);
                if($res['isFormGood']){
                    $manager->updatePassword($res['data']);
                    $successUpdatePassword[] = 'Mot de passe modifié avec success';
                }else{
                    $errorsUpdatePassword = $res['errors'];
                }
            }


            echo $this->renderView('user.html.twig',
                [
                    'user'=>$user,
                    'admin' => $admin,
                    'errorsUpdateContact' => $errorsUpdateContact,
                    'errorsUpdatePassword' => $errorsUpdatePassword,
                    'successUpdatePassword' =>  $successUpdatePassword,
                ]);
        }else{
            echo $this->redirect('reglog');
        }
    }
}
