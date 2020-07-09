<?php

  use Facebook\Facebook;

  class Instagram {

    private $appId;
    private $appSecret;
    private $redirectURI;
    private $accessToken;
    private $sdk;

    function __construct($appId, $appSecret, $redirectURI) {
      //Sessions required for token generation
      if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }

      $this->facebook = new Facebook([
        'app_id' => $appId,
        'app_secret' => $appSecret
      ]);

      $this->redirectURI = $redirectURI;
    }

    private function getLoginURL() {
      $helper = $this->facebook->getRedirectLoginHelper();
      $permissions = ['pages_show_list','instagram_basic'];
      return $helper->getLoginUrl($this->redirectURI, $permissions);
    }

    private function fetchAccessToken($code) {
      $helper = $this->facebook->getRedirectLoginHelper();
      try {
        $token = $helper->getAccessToken();
        $this->saveAccessToken($token);
        return $token;
      } catch(Facebook\Exception\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
      } catch(Facebook\Exception\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
      }
    }

    private function saveAccessToken($token) {
      $_SESSION['access_token'] = $token;
    }

    private function getAccessToken() {
      return $_SESSION['access_token'];
    }

    function login() {
      if(isset($_SESSION['access_token'])) {
        return $_SESSION['access_token'];
      } else if(isset($_GET['code'])) {
        return $this->fetchAccessToken($_GET['code']);
      } else {
        header("Location: ".$this->getLoginURL());
        die;
      }
    }

    function getPageId($pageName) {
      $token = $this->getAccessToken();

      $pages = $this->facebook->get('/me/accounts?access_token='.$token);
      $pages = $pages->getDecodedBody();

      $page = array_filter($pages['data'], function($page) use ($pageName) {
        return $page['name'] == $pageName;
      });

      if(count($page)) {
        return $page[0]['id'];
      } else {
        return false;
      }
    }

    function getInstagramUserId($pageId) {
      $token = $this->getAccessToken();
      $params = [
        'access_token' => $token->getValue(),
        'fields' => 'instagram_business_account'
      ];

      $user = $this->facebook->get('/'.$pageId.'?'.http_build_query($params));
      $user = $user->getDecodedBody();

      if(isset($user['instagram_business_account'])) {
        return $user['instagram_business_account']['id'];
      } else {
        return false;
      }
    }
    
    function getHashtagId($instagramUserId, $hashtag) {
      $token = $this->getAccessToken();

      $params = [
        'access_token' => $token->getValue(),
        'user_id' => $instagramUserId,
        'q' => $hashtag
      ];

      $search = $this->facebook->get('ig_hashtag_search?'.http_build_query($params));
      $search = $search->getDecodedBody();
      if(count($search['data'])) {
        return $search['data'][0]['id'];
      } else {
        return false;
      }
      
    }

    function fetchByHashtag($instagramUserId, $hashtag) {
      $token = $this->getAccessToken();
      $hashtagId = $this->getHashtagId($instagramUserId, $hashtag);
      
      $params = [
        'access_token' => $token->getValue(),
        'user_id' => $instagramUserId,
        'fields' => 'caption,permalink,media_type,media_url'
      ];

      $search = $this->facebook->get('/'.$hashtagId.'/recent_media?'.http_build_query($params));
      $search = $search->getDecodedBody();
      return $search['data'];
    }
  }

?>