<?php if(!class_exists('raintpl')){exit;}?> <div class="well well-small" id="footer">Copyright <?php  echo PROGRAM_NAME;?> <?php  echo PROGRAM_VERSION;?>
<?php echo Plugin::callHook("footer_post_copy", array()); ?>
 </div>
 </div> <!-- /container -->



    <!-- Le javascript
    ================================================== -->
    <script src="./templates/default/js/jquery.min.js"></script>
    <script src="./templates/default/js/bootstrap.min.js"></script>
    <script src="./templates/default/js/jquery.ui.custom.min.js"></script>
    <script src="./templates/default/js/jquery.sys1.js"></script>
	<script src="./templates/default/js/script.js"></script>
	<?php echo Plugin::callJs(); ?>
  </body>
</html>





