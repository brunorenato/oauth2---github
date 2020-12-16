<?php

ob_start();

require __DIR__."/vendor/autoload.php";

if(empty($_SESSION["userlogin"])) {
    echo "<h1>Convidado</h1>";
    /**
     *  AUTH GITHUB
     */
    $github = new \League\OAuth2\Client\Provider\Github([
        "clientId" => GITHUB["app_id"],
        "clientSecret" => GITHUB["app_secret"],
        "redirectUri" => GITHUB["app_redirect"],
        "graphApiVersion" => GITHUB["app_version"],
    ]);

    $authUrl = $github->getAuthorizationUrl([
        "scope" => ["email"]
    ]);

    //$error = filter_input( type: INPUT_GET, variable_name: "error", filter: FILTER_SANITIZE_STRIPPED);
    $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
    if($error){
        echo "<h4>Você precisa autorizar para continuar</h4>";
    }

    $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);
    if($code){
        $token = $github->getAccessToken( "authorization_code", [
            "code" => $code
        ]);
        $_SESSION["userlogin"] = $github->getResourceOwner($token);
        header( "Refresh: 0");
    }

    echo "<a title='GH login' href='{$authUrl}'>Github Login</a>";

} else {

    $user = $_SESSION["userlogin"];
    echo "<h1>Bem-vindo(a) {$user->getNickName()}</h1>";

    //var_dump($user);

    echo "<a title='sair' href='?off=true'>Sair</a>";
    $off = filter_input( INPUT_GET, "off", FILTER_VALIDATE_BOOLEAN );
    if($off){
        unset($_SESSION["userlogin"]);
        header( "Refresh: 0");
    }
}
ob_end_flush();