<?php
	$nerve = __ROOT__.'nerve';
?>
<div class="row">
	<div class="col-md-12">



<div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Documentation</h3>
              </div>
              <div class="panel-body">
              
          
<h3 id="doc.install">Installation</h3>
Pour profiter pleinement de ce plugin, vous devez ajouter (si ce n'est pas déja fait) une tâche planifiée sur le raspberry PI.<br/>Pour cela tapez : 
<br/><code>sudo crontab -e</code> 
<br/>Puis ajoutez la ligne <br/><code>*/1 * * * * wget http://localhost/yana-server/action.php?action=crontab -O /dev/null 2>&1</code><br/>
<br/>Puis ajoutez la ligne <br/><code>@reboot <?php echo $nerve; ?> <?php echo __ROOT__.'action.php'; ?> -O /dev/null 2>&1</code><br/>Puis sauvegardez (ctrl+x puis O puis Entrée)<br/>
<br/>Executez la commande<br/><code>sudo chmod +x <?php echo $nerve; ?></code>
<br/><br/>

<h3>Variables</h3>

Des variables peuvent être définies, testées ou consultées dans les scénarios.<br/>
Les points suivants sont à noter
<ul>
	<li>Pour définir une variable et sa valeur, il faut créer un effet "variable"</li>
	<li>Pour utiliser une variable existante en tant que cause il faut créer une cause "variable" en reprenant le nom de la variable créée</li>
	<li>Pour utiliser la valeur d'une variable dans un autre effet (liste de commande, url etc...) vous pouvez placer la variable entre accolades.<small> ex : pour utiliser une variable <code>toto</code> dans une ligne de commande, créez un effet commande et placez dans le texte <code>ma-commande {toto}</code> </small></li>
	<li>Les effet de type <code>commande</code> envoient automatiquement leurs résultat de sortie dans la variable <code>cmd_result</code></li>
	<li>Les effet de type <code>url</code> envoient automatiquement leurs résultat de requette dans la variable <code>url_result</code></li>
	<li>Certaines variables "communes" sont définies par défaut (voir ci dessous)</li>
</ul>

Les variables par défaut sont les suivantes
<ul>
	<?php foreach(Story::keywords() as $key=>$value): ?>
		<li><code><?php echo $key; ?></code> : <?php echo $value; ?></li>
	<?php endforeach; ?>
</ul>
 </div>
            </div>

</div>
</div>
