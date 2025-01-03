<?php

require_once 'bootstrap.php';

if (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

    if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
    }
    exit('Invalid state');

} else {

    // try {
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => urldecode($_GET['code']),
        ]);
        $token = $accessToken->getToken();
        echo $accessToken;
        // header("Location: ");


        // $accessToken = $provider->getAccessToken('authorization_code', [
        //     'code' => urldecode($_GET['code']),
        // ]);
        // $token = $accessToken->getToken();
        // $resourceOwner = $provider->getResourceOwner($accessToken);
        // $code = $_GET['code'];
        // $user_id = $resourceOwner->toArray()['Owner']['oauth_info']['oauth_user_id'];
       
        //     // $update_code = mysqli_query($db_dir, "UPDATE `user` SET `code` = '$code' WHERE `user_id` = '$user_id';");
        // header('Location: https://hydra-cam0.ddns.com?code='.$_GET['code']."&state=".$_GET['state']);   
        // // header('https://google.com');   
        
    // } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
    //     echo "FAILED TO GET";
    //     exit($e->getMessage());
    // }
}

?>