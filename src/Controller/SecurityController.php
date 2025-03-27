<?php

namespace App\Controller;

use App\Manager\UserManager;
use App\Model\User;

class SecurityController
{

    private UserManager $userManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    // Route Login ( ancien login.php ) 
    // Route URL -> index.php?action=login
    public function login()
    {

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (empty($_POST['username']) || strlen($_POST['username']) < 4) {
                $errors['username'] = 'Votre username doit contenir 4 caracteres';
            }
            if (empty($_POST['password']) || strlen($_POST['password']) < 4) {
                $errors['password'] = 'Votre password doit contenir 4 caracteres';
            }

            if (count($errors) == 0) {

                $user = $this->userManager->selectByUsername($_POST["username"]);

                //Vérification si User trouvée avec le Username
                if ($user != false) {
                    //Sinon vérificaiton mot de passe Formulaire et Hash BDD
                    if (password_verify($_POST["password"], $user->getPassword())) {
                        // Je connecte l'utilisateur
                        $_SESSION["username"] = $user->getUsername();
                        $adminController = new AdminController();
                        $adminController->dashboardAdmin();
                        exit();
                    }
                }
                //Afficher la même erreur si le problème vient du MDP ou Username
                // Pour ne pas donner trop d'informations
                $errors["login"] = 'Identifiants ou mot de passe incorrecte';
            }
        }
        require_once("./templates/login.php");
    }

    // Route Register ( ancien register.php ) 
    // URL : index.php?action=register
    public function register()
    {

        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $errors = [];

            if (empty($_POST['username']) || strlen($_POST['username']) < 4) {
                $errors['username'] = 'Votre username doit contenir 4 caracteres';
            }
            if (empty($_POST['password']) || strlen($_POST['password']) < 4) {
                $errors['password'] = 'Votre password doit contenir 4 caracteres';
            }

            if (empty($errors)) {
                $usernameExist = $this->userManager->selectByUsername($_POST["username"]);

                if ($usernameExist != false) {
                    $errors["username"] = "Le username existe déja !";
                }

                if (empty($errors)) {

                    $pass = password_hash($_POST["password"], PASSWORD_DEFAULT);

                    $user = new User(null, $_POST["username"], $pass);
                    $this->userManager->insert($user);

                    $_SESSION["username"] = $user->getUsername();

                    $indexController = new IndexController();
                    $indexController->homePage();                }
            }
        }
        require_once("./templates/register.php");
    }

    public function logout(){
        unset($_SESSION["username"]);    
        $indexController = new IndexController();
        $indexController->homePage();
    }

}
