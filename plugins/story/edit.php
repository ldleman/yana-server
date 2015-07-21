<?php
$story = new Story();
$cause = new Cause();
$effect = new Effect();
		
$causes = array();
$effects = array();
		
		if(isset($_['story'])){
			$story = $story->getById($_['story']);
			$effects = $effect->loadAll(array('story'=>$story->id),'sort');
			$causes = $cause->loadAll(array('story'=>$story->id),'sort');
		}
		
		?>
		
		<div class="story">
		<h1>Nom : <input id="storyName" value="<?php echo $story->label; ?>" placeholder="Scénario 1"/></h1>
		<input type="hidden" id="story" value="<?php echo $story->id; ?>">
		<h2>
			<span id="causePanelButton" class="active" onclick="switchCauseEffect('CAUSE');"><i class="fa fa-puzzle-piece"></i> Causes</span>
			/ 
			<span id="effectPanelButton" onclick="switchCauseEffect('EFFECT');"><i class="fa fa-bolt"></i> Effets</span>
		</h2>
		<div id="causePanel">
			<ul class="toolbar">
				<li data-type="time" class="time"><i class="fa fa-clock-o"></i> Date / Heure</li>
				<li data-type="listen" class="vocal"><i class="fa fa-microphone"></i> Phrase</li>
				<li data-type="event" class="event"><i class="fa fa-tachometer"></i> Capteur</li>
				<li data-type="readvar" class="var"><i class="fa fa-dollar"></i> Variable</li>
				<div class="clear"></div>
			</ul>
			<ul class="workspace">
				<li class="union">SI</li>
				<li id="place-0" class="place">...</li>
			</ul>
		</div>
		
		
		<div id="effectPanel">
			<ul class="toolbar">
				<li data-type="command" class="command"><i class="fa fa-terminal"></i> Commande</li>
				<li data-type="talk" class="vocal"><i class="fa fa-volume-up"></i> Phrase</li>
				<li data-type="actuator" class="actuator"><i class="fa fa-cogs"></i> Action</li>
				<li data-type="var" class="var"><i class="fa fa-dollar"></i> Variable</li>
				<li data-type="sleep" class="sleep"><i class="fa fa-coffee"></i> Pause</li>
				<div class="clear"></div>
			</ul>
			<ul class="workspace">
				<li class="union">ALORS</li>
				<li id="place-effect-0" class="place">...</li>
			</ul>
			
		</div>
		<div onclick="saveScenario();" class="clear btn" style="margin-left:15px;cursor:pointer;"><i class="fa fa-check"></i> Enregistrer</div>
		</div>

		<script type="text/javascript">
		var story = [
		<?php
		$first = true;
		foreach($causes as $caus){
			echo (!$first?',':'').'{type:"'.$caus->type.'",panel:"CAUSE",data:{value:"'.$caus->value.'",target:"'.$caus->target.'",operator:"'.$caus->operator.'",union:"'.$caus->union.'"}}';
			$first = false;
		}
	
		foreach($effects as $eff)
			echo ',{type:"'.$eff->type.'",panel:"EFFECT",data:{value:"'.$eff->value.'",target:"'.$eff->target.'",operator:"'.$eff->operator.'",union:"'.$eff->union.'"}}';
		
		?>
		]
		</script>
