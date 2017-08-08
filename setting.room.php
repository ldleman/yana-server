<?php 
global $myUser;
if(!$myUser->can('room','configure')) throw new Exception("Permissions insuffisantes");
?>
<div class="row">
	<div class="col-md-12">
		<h3>Pièces</h3>

		<div id="roomForm" class="row" data-action="save_room">
			<div class="col-md-12">
				<label for="label">Nom de la pièce</label>
				<input id="label" class="form-control" placeholder="Salon,Cuisine..." type="text">
				<label for="description">Description</label>
				<input id="description" class="form-control" placeholder="Description" type="text">
			</div>
			
			<div class="col-md-12"><br/>
				<div onclick="save_room();" class="btn btn-info"><i class="fa fa-check"></i> Enregistrer</div>
			</div>
		</div>
		<br/>
		<div class="panel panel-default">
      <div class="panel-heading">Pièces existantes</div>
      <table id="rooms" class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Description</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr data-id="{{id}}" class="noDisplay">
            <td>{{id}}</td>
            <td>{{label}}</td>
            <td>{{description}}</td>
            <td>
             <div onclick="edit_room(this)" class="btn btn-info btn-mini"><i class="fa fa-pencil"></i></div>
             <div onclick="delete_room(this)" class="btn btn-danger btn-mini"><i class="fa fa-times"></i></div>
           </td>
         </tr>
       </tbody>
     </table>
   </div>
 </div>
</div>

