<div id="BoxPage" class="row">
	<div class="col-md-8">
		<ul ng-if="totalpage>1"class="pagination">
		  <li><a href="javascript:;">&laquo;</a></li>
		  <li class="active"><a href="javascript:;">1</a></li>
		  <li><a href="javascript:;">&raquo;</a></li>
		</ul>
	</div>
	<div ng-if="control==true" class="col-md-4 control">
		<div class="btn-group">
			<button ng-if="add_mode!='OFF'" type="button" class="btn btn-xs btn-dark btn-add" ref="{{module}}">
				<span class="glyphicon glyphicon-plus-sign"></span> Added
			</button>
			<button ng-if="delete_mode!='OFF'" type="button" class="btn btn-xs btn-dark btn-delete-all">
				<span class="glyphicon glyphicon-trash"></span> Delete
			</button>
			<button ng-if="publish_mode!='OFF'" type="button" class="btn btn-xs btn-dark btn-publish-all">
				<span class="glyphicon glyphicon-globe"></span> Publish
			</button>
		</div>
	</div>
</div>