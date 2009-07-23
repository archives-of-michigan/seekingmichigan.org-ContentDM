<? $js_includes = isset($js_includes) ? $js_includes : array(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= $title ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="<?= SEEKING_MICHIGAN_HOST ?>/css/screen/main.css" type="text/css" media="screen, projection" />
  <!--[if IE]>
  <link rel="stylesheet" href="<?= SEEKING_MICHIGAN_HOST ?>/css/screen/patches/win-ie-all.css" type="text/css" media="screen, projection" />
  <![endif]-->
  <!--[if IE 7]>
  <link rel="stylesheet" href="<?= SEEKING_MICHIGAN_HOST ?>/css/screen/patches/win-ie7.css" type="text/css" media="screen, projection" />
  <![endif]-->
  <!--[if lt IE 7]>
  <link rel="stylesheet" href="<?= SEEKING_MICHIGAN_HOST ?>/css/screen/patches/win-ie-old.css" type="text/css" media="screen, projection" />
  <script type="text/javascript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/lib/dd-png.js"></script>
  <![endif]-->
  <script type="text/javascript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/core.js"></script>
  <script type="text/javascript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/jquery.js"></script>
  <? foreach($js_includes as $js): ?>
    <? if(preg_match('/^http:\/\//',$js) > 0): ?>
      <script type="text/javascript" src="<?= $js ?>"></script>
    <? else: ?>
      <script type="text/javascript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/<?= $js ?>.js"></script>
    <? endif; ?>
  <? endforeach; ?>
  <script type="text/javascript" src="http://www.google-analytics.com/ga.js"></script>
  <script type="text/javascript">
    try { _gat._getTracker("UA-7441223-2")._trackPageview(); } catch(err) {}
  </script>
  <? include('banners.php'); ?>
  <? if(FACEBOX == 'display'): ?>
    <? include('include/facebox.php'); ?>
  <? endif; ?>
  <? if(LIGHTBOX == 'display'): ?>
    <? include('include/lightbox.php'); ?>
  <? endif; ?>
  <? if(SLIDER == 'display'): ?>
    <? include('include/slider.php'); ?>
  <? endif; ?>
</head>
<body id="www.seekingmichigan.com" class="<?= BODY_CLASS ?>">
  <div class="wrapper">
    <div id="header">
      <div class="wrapper">
        <h1><a href="<?= SEEKING_MICHIGAN_HOST ?>"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/seeking-logo.gif" width="309" height="41" alt="Seeking Michigan Logo" /><span>Seeking Michigan</span></a></h1>
        <ul id="nav">
          <li id="nav-seek"><a href="seek_advanced.php"> Seek</a></li>
          <li id="nav-discover"><a href="<?= SEEKING_MICHIGAN_HOST ?>/discover"> Discover</a></li>
          <li id="nav-look"><a href="<?= SEEKING_MICHIGAN_HOST ?>/look"> Look</a></li>
          <li id="nav-teach"><a href="<?= SEEKING_MICHIGAN_HOST ?>/teach"> Teach</a></li>
        </ul>
      </div>
    </div>
    <div id="utility-bar">
      <div class="wrapper">
        <ul class="breadcrumbs">
          <li><a href="#">Home</a> &raquo; </li>
          <? $last_item = end($breadcrumbs); ?>
          <? foreach($breadcrumbs as $crumb => $link): ?>
            <li <? if(!$link): ?>class="here"<? endif; ?>>
              <? if($link): ?><a href="<?= $link ?>"><?= $crumb ?></a> &raquo; <? else: ?><?= $crumb ?><? endif; ?>
            </li>
          <? endforeach; ?>
        </ul>
        <div class="search">
          <form id="global-search" action="<?= SEEKING_MICHIGAN_HOST ?>" method="get" >
            <label for="s" class="hidden">Seek: </label>
            <input type="text" name="s" id="s" value=" " />
            <label for="search-button" class="hidden">Search </label>
            <input type="submit" value=" " id="search-button" name="search-button" />
          </form>
        </div>
      </div>
    </div>
    <div id="main">
      <div class="wrapper">