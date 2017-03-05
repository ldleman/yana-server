<?php 
require_once __DIR__.DIRECTORY_SEPARATOR.'common.php';




$mainMenu = array();
Plugin::callHook("menu_main", array(&$mainMenu));
uasort ($mainMenu , function($a,$b){return $a['sort']>$b['sort']?1:-1;});



$userMenu = array();
Plugin::callHook("menu_user", array(&$userMenu));
uasort ($userMenu , function($a,$b){return $a['sort']>$b['sort']?1:-1;})

?>
<!doctype html>
<html class="no-js" lang="">
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title><?php echo PROGRAM_NAME.' V'.SOURCE_VERSION.'.'.BASE_VERSION ?></title>
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1">
  	  <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,800,700,400italic,600italic,700italic,800italic,300italic" rel="stylesheet" type="text/css">
  	  <link rel="stylesheet" href="css/bootflat.min.css">
      <link rel="stylesheet" href="css/font-awesome.min.css">
      <link rel="stylesheet" href="css/main.css">
	  <?php echo Plugin::callCss("css"); ?>
    </head>
    <body>
        <div id="wrap">
    


		<!-- menu -->
		<nav role="navigation" class="navbar navbar-inverse">
                  <div class="container-fluid">
                    <div class="navbar-header">
                      <button data-target="#menuBar" data-toggle="collapse" class="navbar-toggle" type="button">
                        <span class="sr-only">Toggle navigation</span>
                        <i class="fa fa-bars"></i>
                      </button>
                      <a href="index.php" class="navbar-brand"><?php echo PROGRAM_NAME; ?></a>
                    </div>
                 
                    
					
					<div id="menuBar" class="collapse navbar-collapse">
						 <?php if ($myUser->connected()): ?>
						<ul class="nav navbar-nav">
						
						<?php foreach($mainMenu as $item): ?>
						<li <?php echo $page==$item['url']?'class="active"':''; ?> <?php echo isset($item['disabled'])?'class="disabled"':'' ?>
						<?php if(isset($item['items'])): ?>
							class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="#"><?php echo (isset($item['icon'])?'<i class="fa fa-'.$item['icon'].'"></i> ':'').$item['label']; ?></a>
								<ul role="menu" class="dropdown-menu">
									<?php foreach($item['items'] as $subitem): ?>
										<li <?php echo $page==$subitem['url']?'class="active"':''; ?> <?php echo isset($subitem['disabled'])?'class="disabled"':'' ?> ><a href="<?php echo $subitem['url']; ?>"><?php echo (isset($subitem['icon'])?'<i class="fa fa-'.$subitem['icon'].'"></i> ':'').$subitem['label']; ?></a></li>
									<?php endforeach; ?>
								</ul>
						<?php else: ?>
						 ><a  href="<?php echo $item['url']; ?>" ><?php echo (isset($item['icon'])?'<i class="fa fa-'.$item['icon'].'"></i> ':'').$item['label']; ?></a>
						<?php endif; ?>
						</li>
						<?php endforeach; ?>
                      </ul>
					  
					
                      <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown <?php echo $page=='account.php'?'active':''; ?>" >
                          <a data-toggle="dropdown" id="userBar" class="dropdown-toggle" href="#"><img class="avatar-mini" src="<?php echo $myUser->getAvatar(); ?>"> Connecté en tant que <?php echo $myUser->login; ?></a>
                          <ul role="menu" class="dropdown-menu">
                            <li class="dropdown-header">Profil</li>
                            <?php foreach($userMenu as $item): ?>
										<li <?php echo $page==$item['url']?'class="active"':''; ?> <?php echo isset($item['disabled'])?'class="disabled"':'' ?> ><a href="<?php echo $item['url']; ?>"><?php echo $item['label']; ?></a></li>
							<?php endforeach; ?>
                            <li class="divider"></li>
                            <li><a href="action.php?action=logout">Déconnexion</a></li>
                          </ul>
                        </li>
                      </ul>
					  <?php else: ?>
					 
						<form id="loginForm" method="post" action="action.php?action=login" class="navbar-form navbar-right">
                                          Identifiant : 
                                          <input name="login" maxlength="8" class="form-control input-medium" type="text">
                                            Mot de passe : 
                                          <input name="password" class="form-control input-medium" type="password">
                                          <input class="btn btn-success" value="Connexion" type="submit">
                        </form>
					
					  <?php endif; ?>
					 

            

					  <!--<button class="btn btn-danger navbar-btn" onclick="window.location='action.php?action=logout';" type="button">MAJ</button>-->
					  
                    </div><!-- /.navbar-collapse -->
					
					
					
                  </div><!-- /.container-fluid -->
                </nav>
		<!-- menu -->
		

		
		<!-- body -->
		<div class="container-fluid">
		
		
		<!-- messages -->
		<div id="message" class="alert alert-{{type.class}} {{#closable}}alert-dismissable{{/closable}} noDisplay">
            <strong>{{type.label}}</strong> <span>{{label}}</span>
        </div>
			
			
