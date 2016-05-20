<?php
$story = new Story();


		
if(isset($_['story']))
	$story = $story->getById($_['story']);

		
		?>
		
		<div class="story" id="story" data-id="<?php echo $story->id; ?>">
		<h1>Nom : <input id="storyName" value="<?php echo $story->label; ?>" placeholder="Scénario 1"/></h1>
		
		<h2>
			<span id="causePanelButton" class="active" onclick="switchCauseEffect('CAUSE');"><i class="fa fa-cog"></i> CAUSES</span>
			/ 
			<span id="effectPanelButton" onclick="switchCauseEffect('EFFECT');"><i class="fa fa-bolt"></i> EFFETS</span>
		</h2>
		<div id="causePanel">
			<ul class="toolbar">
				<?php 
					foreach(Cause::types() as $key=>$type):
						echo '<li data-type="'.$key.'" title="'.$type['description'].'" class="'.$key.' infobulle typeButton"><i class="fa '.$type['icon'].'"></i> '.$type['label'].'</li>';
					endforeach;
				?>
				<div class="clear"></div>
			</ul>
			<div class="firstunion">SI</div>
			<ul class="workspace workspace-cause">
				
			</ul>
		</div>
		
		
		<div id="effectPanel">
			<ul class="toolbar">
				<?php 
					foreach(Effect::types() as $key=>$type):
						echo '<li data-type="'.$key.'"  title="'.$type['description'].'"  class="'.$key.' infobulle typeButton"><i class="fa '.$type['icon'].'"></i> '.$type['label'].'</li>';
					endforeach;
				?>
				<div class="clear"></div>
			</ul>
			<div class="firstunion">ALORS</div>
			<ul class="workspace workspace-effect">
				
			</ul>
			
		</div>
		<div onclick="saveStory();" class="clear btn" style="cursor:pointer;"><i class="fa fa-check"></i> Enregistrer</div>
		</div>


