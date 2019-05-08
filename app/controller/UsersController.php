<?php

namespace app\controller;

use app\model\UsersManager;

class UsersController extends AppController
{

    public function loginPage($compactVars = null)
    {
        if ($compactVars == null) {
            echo $this->twig->render('login.twig');
        } else {
            echo $this->twig->render('login.twig', $compactVars);
        }
    }

    /**
     * @param compacted array $compactVars
     * To display errors in the twig view
     */
    public function registerPage($compactVars = null)
    {
        if ($compactVars == null) {
            echo $this->twig->render('register.twig');
        } else {
            echo $this->twig->render('register.twig', $compactVars);
        }
    }

    public function login()
    {
        if (isset($_POST['signinEmail'], $_POST['signinPwd'])) {
            $errors = [];
            $usersManager = new UsersManager();
            $login = $usersManager->getEmail($_POST['signinEmail']);

            if (empty($_POST['signinEmail']) || empty($_POST['signinPwd'])) {
                $errors['emptyField'] = true;
            }
            if ($login == false && $login > 0) {
                $errors['noEmail'] = 'L\'identifiant n\'existe pas';
            } else {
                $pwdCheck = password_verify($_POST['signinPwd'], $login->getPassword());
                if ($pwdCheck == false) {
                    $errors['wrongPwd'] = 'Le mot de passe n\'est pas correct';
                } else {
                    $_SESSION['id'] = $login->getId();
                    $_SESSION['email'] = $login->getEmail();
                    $_SESSION['nickname'] = $login->getNickname();
                    $_SESSION['role'] = $login->getRole();
                    header('location:' . BASEURL);
                }
            }

            if (!empty($errors)) {
                $this->loginPage(compact('errors'));
            }
        } else {
            header('Location: ' . BASEURL . '/register');
        }
    }

    public function newUser()
    {
        try {
            // $array = ['sdf', 'sdfgfgh', true] -> if (in_array('empty_field', $errors)) {<p>remplissez les champs</p>}
            if (isset($_POST['registerEmail'], $_POST['registerNickname'], $_POST['registerPwd'])) {
                $errors = [];
                $usersManager = new UsersManager();
                $emailExists = $usersManager->getEmail($_POST['registerEmail']);
                $nicknameExists = $usersManager->getNickname($_POST['registerNickname']);

                if (empty($_POST['registerEmail']) || empty($_POST['registerNickname']) || empty($_POST['registerPwd'])) {
                    $errors['emptyField'] = true;
                }
                if ($emailExists == true) {
                    $errors['emailExists'] = true;
                }
                if (!preg_match('#^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]{2,4}+$#', $_POST['registerEmail'])) {
                    $errors['regexEmail'] = true;
                }
                if ($nicknameExists == true) {
                    $errors['nicknameExists'] = true;
                }
                if (!preg_match('#^[a-zA-Z0-9]#', $_POST['registerNickname'])) {
                    $errors['regexNickname'] = true;
                }
                if (strlen($_POST['registerNickname']) < 3) {
                    $errors['shortNickname'] = true;
                }
                if (!preg_match('#^[a-zA-Z0-9]#', $_POST['registerPwd'])) {
                    $errors['regexPwd'] = true;
                }
                if (strlen($_POST['registerPwd']) < 8) {
                    $errors['shortPwd'] = true;
                }
                if (!empty($errors)) {
                    // Si erreurs, on compacte le tableau pour les afficher dans la view contenue dans registerPage
                    // Si on ne fait pas ça on perd les erreurs entre les méthodes
                    $this->registerPage(compact('errors'));
                } else {
                    $hash = password_hash($_POST['registerPwd'], PASSWORD_DEFAULT);
                    $usersManager->createUser($_POST['registerEmail'], $_POST['registerNickname'], $hash);
                    header('location:' . BASEURL);
                    exit;
                }
            } else {
                header('Location: ' . BASEURL . '/register');
                exit;
            }
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function disconnect()
    {
        session_destroy();
        header('Location:' . BASEURL);
        exit;
    }
}