<?php require __DIR__.'/core/bootstrap.php';if(!is_post()){http_response_code(405);header('Allow: POST');exit;}verify_csrf();$_SESSION=[];session_destroy();go('login.php');
