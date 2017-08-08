<?php 

global $myUser;
if(!$myUser->can('rank','configure')) throw new Exception("Permissions insuffisantes");

$rank = Rank::getById($_['rank']);
$rights = Right::loadAll(array('rank'=>$_['rank']));
$rightsTable = array();
foreach($rights as $right)
  $rightsTable[$right->section] = $right;

?>
<div class="row">
	<div class="col-md-12">
		<h3 id="rank" data-rank="<?php echo $rank->id ?>">Droits pour le rang <?php echo $rank->label ?></h3>

		<br/>
		<div class="panel panel-default">
      <div class="panel-heading">Droits</div>
      <table id="ranks" class="table">
        <thead>
          <tr>
            <th>Libellé</th>
            <th>Description</th>
            <th>Tout cocher</th>
            <th class="rightColumn">Consultation</th>
            <th class="rightColumn">Edition</th>
            <th class="rightColumn">Supression</th>
            <th class="rightColumn">Configuration</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $sections = array();
          Plugin::callHook('section',array(&$sections));
          foreach($sections as $section=>$description): 
            $right = isset($rightsTable[$section])? $rightsTable[$section] : new Right();
          ?>
          <tr data-section="<?php echo $section ?>">
            <td><?php echo $section ?></td>
            <td><?php echo $description ?></td>
            <td><div class="btn btn-success" onclick="right_switch(this);"><i class="fa fa-rotate-right"></i> Tout switcher</div></td>
            <td class="rightColumn">
             <label class="toggle" data-right="read">
              <input <?php echo $right->read?'checked=""':''; ?> type="checkbox">
              <span class="handle"></span>
            </label>
          </td>
          <td class="rightColumn">
            <label class="toggle" data-right="edit">
              <input <?php echo $right->edit?'checked=""':''; ?> type="checkbox">
              <span class="handle"></span>
            </label>
          </td>
          <td class="rightColumn">
            <label class="toggle" data-right="delete">
              <input <?php echo $right->delete?'checked=""':''; ?> type="checkbox">
              <span class="handle"></span>
            </label>
          </td>
          <td class="rightColumn">
            <label class="toggle" data-right="configure">
              <input <?php echo $right->configure?'checked=""':''; ?> type="checkbox">
              <span class="handle"></span>
            </label>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</div>
</div>