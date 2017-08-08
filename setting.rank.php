<?php 
global $myUser;
if(!$myUser->can('rank','configure')) throw new Exception("Permissions insuffisantes");
?>
<div class="row">
	<div class="col-md-12">
		<h3>Rangs</h3>

		<div id="rankForm" class="row" data-action="save_rank">
			<div class="col-md-4">
				<label for="label">Libellé</label>
				<input id="label" class="form-control" placeholder="Libellé" type="text">
			</div>
			<div class="col-md-4">
				<label for="description">Description</label>
				<input id="description" class="form-control" placeholder="Description" type="text">
			</div>
			<div class="col-md-4">
				<div onclick="save_rank();" class="btn btn-info noLabel"><i class="fa fa-check"></i> Enregistrer</div>
			</div>
		</div>
		<br/>
		<div class="panel panel-default">
			<div class="panel-heading">Rangs existants</div>
			<table id="ranks" class="table">
       <thead>
         <tr>
          <th>#</th>
          <th>Libellé</th>
          <th>Description</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
       <tr data-id="{{id}}" class="noDisplay">
        <td>{{id}}</td>
        <td>{{label}}</td>
        <td>{{description}}</td>
        <td class="option option-3">
         <a href="setting.php?section=right&amp;rank={{id}}" class="btn btn-primary btn-mini"><i class="fa fa-lock"></i></a>
         <div onclick="edit_rank(this)" class="btn btn-info btn-mini"><i class="fa fa-pencil"></i></div>
         <div onclick="delete_rank(this)" class="btn btn-danger btn-mini"><i class="fa fa-times"></i></div>
       </td>
     </tr>
   </tbody>
 </table>
</div>
</div>
</div>