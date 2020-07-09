<?php

  require 'vendor/autoload.php';
  require 'classes/Instagram.php';

  if(!isset($_SERVER['FACEBOOK_APP_ID']))
    die('FACEBOOK_APP_ID missing');

  if(!isset($_SERVER['FACEBOOK_APP_SECRET']))
    die('FACEBOOK_APP_SECRET missing');

  $appId = $_SERVER['FACEBOOK_APP_ID'];
  $appSecret = $_SERVER['FACEBOOK_APP_SECRET'];
  $thisURI = 'https://instagram-fetch.dev/index.php';

  //Setup the page to use (ig account should be connected to this page)
  $pageName = 'Divino Diseño';

  //Select a hashtag to search images
  $hashtag = 'city';

  //Setup sdk
  $instagram = new Instagram($appId, $appSecret, $thisURI);

  //Get access token (may trigger redirect)
  $instagram->login();

  //Get pageId
  $pageId = $instagram->getPageId($pageName);
  
  //Get instagram user id connected to the page
  $instagramUserId = $instagram->getInstagramUserId($pageId);

  //Fetch images for hashtag
  $images = $instagram->fetchByHashtag($instagramUserId, $hashtag);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>#<?=$hashtag?></title>
</head>
<body>
  <h1><?=$hashtag?></h1>
  <ul>
    <?php foreach($images as $image): ?>
    <li>
      <img src="<?=$image['media_url']?>" alt="<?=substr($image['caption'], 0, 20)?>">
    </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>