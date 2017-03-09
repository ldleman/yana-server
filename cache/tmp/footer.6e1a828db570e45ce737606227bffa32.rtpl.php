<?php if(!class_exists('raintpl')){exit;}?>

 </div> <!-- /container -->
<div class="navbar navbar-inverse navbar-fixed-bottom" id="footer">
      <div class="navbar-inner">

 	<?php  echo PROGRAM_NAME;?> <?php echo $configurationManager->get('PROGRAM_VERSION');?> (Exécuté en <?php echo $executionTime;?> secondes) | Licence CC-by-nc-sa   
<?php echo Plugin::callHook("footer_post_copy", array()); ?>
 </div></div>


    <!-- Le javascript
    ================================================== -->
  <script src="./templates/default/js/jquery.min.js"></script>
  <script src="./templates/default/js/bootstrap.min.js"></script>
  <script src="./templates/default/js/jquery.ui.custom.min.js"></script>
  <script src="./templates/default/js/jquery.yana.js"></script>
	<script src="./templates/default/js/chart.min.js"></script>
	<script src="./templates/default/js/script.js"></script>
	<?php echo Plugin::callJs(); ?>
  </body>
</html>





