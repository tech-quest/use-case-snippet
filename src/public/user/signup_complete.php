<?php
require_once __DIR__ . '/../../app/Infrastructure/Dao/UserDao.php';
require_once __DIR__ . '/../../app/Infrastructure/Redirect/redirect.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Usecase\UseCaseInput\SignUpInput;
use App\Usecase\UseCaseInteractor\SignUpInteractor;

$email = filter_input(INPUT_POST, 'email');
$name = filter_input(INPUT_POST, 'name');
$password = filter_input(INPUT_POST, 'password');
$confirmPassword = filter_input(INPUT_POST, 'confirmPassword');

session_start();
if (empty($password) || empty($confirmPassword)) {
    $_SESSION['errors'][] = 'パスワードを入力してください';
}
if ($password !== $confirmPassword) {
    $_SESSION['errors'][] = 'パスワードが一致しません';
}
if (!empty($_SESSION['errors'])) {
    $_SESSION['formInputs']['name'] = $name;
    $_SESSION['formInputs']['email'] = $email;
    redirect('./signup.php');
}

$useCaseInput = new SignUpInput($name, $email, $password);
$useCase = new SignUpInteractor($useCaseInput);
$useCaseOutput = $useCase->handler();

if ($useCaseOutput->isSuccess()) {
  $_SESSION['message'] = $useCaseOutput->message();
  redirect('./signin.php');
}  else {
  $_SESSION['errors'][] = $useCaseOutput->message();
  redirect('./signup.php');
}