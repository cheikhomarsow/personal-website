<?php

namespace Controller;

use Model\UserManager;

class ReglogController extends BaseController
{
    public function loginAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $manager = UserManager::getInstance();
            if ($manager->userCheckLogin($_POST)) {
                $manager->userLogin($_POST['email']);
                if($_POST['email'] == 'cheikhomar60@gmail.com'){
                    echo $this->redirect('admin');
                }else{
                    echo $this->redirect('user');
                }
            }else{
                echo $this->renderView('reglog.html.twig', ['loginErrors' => 'Adresse email ou mot de passe incorrect']);
            }
        }
    }
    public function registerAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $manager = UserManager::getInstance();
            $result = $manager->userCheckRegister($_POST);
            if ($result['isFormGood']) {
                $manager->userRegister($result['data']);
                echo $this->renderView('reglog.html.twig', ['registerSuccess' => 'Inscription reussie !']);
            }
            else {
                $errors = $result['errors'];
                echo $this->renderView('reglog.html.twig', ['registerErrors' => $errors]);
            }
        }
    }
}
?>
