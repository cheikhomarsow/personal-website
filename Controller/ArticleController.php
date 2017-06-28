<?php

namespace Controller;

use Model\ArticleManager;

class ArticleController extends BaseController
{
    public function add_articleAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $manager = ArticleManager::getInstance();
            $res = $manager->checkArticle($_POST);
            if ($res['isFormGood']) {
                $manager->addArticle($res['data']);
                echo $this->renderView('admin.html.twig', ['addArticleSuccess' => 'Article ajouté !']);
            }else{
                echo $this->renderView('admin.html.twig', ['addArticleErrors' => $res['errors']]);
            }
        }
    }
}
?>
