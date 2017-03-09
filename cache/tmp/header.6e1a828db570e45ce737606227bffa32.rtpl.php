<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title><?php  echo PROGRAM_NAME;?> <?php echo $configurationManager->get('PROGRAM_VERSION');?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/x-icon" href="./templates/default/img/favicon.png">

    <!-- Le styles -->
    <link href="./templates/default/css/bootstrap.min.css" rel="stylesheet">
    <link href="./templates/default/css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
    <link rel="stylesheet" href="./templates/default/css/font-awesome.min.css">
    <link href="./templates/default/css/style.css" rel="stylesheet">
    <?php echo Plugin::callLink(); ?>
    <?php echo Plugin::callCss(); ?>

    <link href="./templates/default/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="./templates/default/ico/favicon.png">
  </head>

  <body>
    <div class="hidden" id="UPDATE_URL"><?php echo $configurationManager->get('UPDATE_URL');?></div>
    <div class="hidden" id="PROGRAM_VERSION"><?php echo $configurationManager->get('PROGRAM_VERSION');?></div>
    <div class="navbar navbar-inverse navbar-fixed-top" id="header">
      
      <div class="navbar-inner">

        <div class="container">

          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        <div class="nav-collapse collapse">
          <ul class="nav">
          <li class="logoBox" title="Revenir à l'accueil"><a id="aLogoBox" href="index.php"><div class="logo"><span class="badge badge-warning" id="notification">&nbsp;</span><?php echo $configurationManager->get('PROGRAM_VERSION');?></div></a></li>
          <!-- <li class="logoBox"><div class="logo"><span class="badge badge-warning" id="notification">&nbsp;</span><?php echo $configurationManager->get('PROGRAM_VERSION');?></div></li>-->
          <?php if( $myUser!=false ){ ?>
              <li><a href="index.php"><i class="fa fa-home"></i> Accueil</a></li>
              <?php $counter1=-1; if( isset($menuItems) && is_array($menuItems) && sizeof($menuItems) ) foreach( $menuItems as $key1 => $value1 ){ $counter1++; ?>
              <li><?php echo $value1["content"];?></li>
              <?php } ?>
              <?php if( $myUser!=false && $myUser->can('configuration','r') ){ ?>
                <li><a href="setting.php"><i class="fa fa-cog"></i> Configuration</a></li>
              <?php } ?>
              <?php if( $myUser!=false ){ ?>

            
              <li class="infobulle"  title="Connecté avec <?php echo $myUser->getFullName();?> (<?php echo $rank->getLabel();?>)"><a href="action.php?action=logout"><i class="fa fa-bolt"></i> Déconnexion</a></li>
              <?php } ?>
          <?php } ?>
           </ul>
      </div><!--/.nav-collapse -->

        </div>
      </div>
    </div>



    <div id="body" class="container">

  <?php if( $error!=false ){ ?>
  <div class="alert alert-error fade in">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo $error;?>
  </div>
  <?php } ?>
  <?php if( $notice!=false ){ ?>
    <div class="alert alert-info fade in">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo $notice;?>
  </div>
  <?php } ?>

  <?php echo Plugin::callHook("header_post_notices", array()); ?>
