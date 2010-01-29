<? 
$js_includes = isset($js_includes) ? $js_includes : array();
$breadcrumbs = isset($breadcrumbs) ? $breadcrumbs : array('Home' => '');
?>
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
  <? foreach($css_includes as $css): ?>
    <? if(preg_match('/^http:\/\//',$css) > 0): ?>
      <link rel="stylesheet" href="<?= $css ?>" type="text/css" media="screen, projection" />
    <? else: ?>
      <link rel="stylesheet" href="<?= SEEKING_MICHIGAN_HOST ?>/css/<?= $css ?>.css" type="text/css" media="screen, projection" />
    <? endif; ?>
  <? endforeach; ?>
  <script type="text/javascript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/core.js"></script>
  <script type="text/javascript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/jquery.min.js"></script>
  <? foreach($js_includes as $js): ?>
    <? if(preg_match('/^http:\/\//',$js) > 0): ?>
      <script type="text/javascript" src="<?= $js ?>"></script>
    <? else: ?>
      <script type="text/javascript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/<?= $js ?>.js"></script>
    <? endif; ?>
  <? endforeach; ?>
  <script type="text/javascript" src="http://www.google-analytics.com/ga.js"></script>
  <script type="text/javascript">
    try { _gat._getTracker("UA-7441223-3")._trackPageview(); } catch(err) {}
  </script>
  <?php app()->partial('banner', 
                       array('scene' => app()->helper('header')->banner_scene())); ?>
  <? if(FACEBOX == 'display'): ?>
    <? include('include/facebox.php'); ?>
  <? endif; ?>
  <? if(LIGHTBOX == 'display'): ?>
    <? include('include/lightbox.php'); ?>
  <? endif; ?>
  <? if(DMMONOCLE == 'display'): ?>
    <script type="text/javascript">
      $(window).ready(function() {
        dmMonocle(<?=$width?>, <?=$height?>, <?=$image_cisoptr?>, "<?=$image_cisoroot?>");
      });
    </script>
  <? endif; ?>
</head>
<body id="www.seekingmichigan.com" class="<?= $bodyclass ?>">
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
        <?php app()->partial('breadcrumbs', array('breadcrumbs' => $breadcrumbs)); ?>
      </div>
    </div>
    <div id="main">
      <div class="wrapper">
