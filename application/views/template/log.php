<?php $this->load->view("template/control.php"); ?>
<div class="row">
  <div class="col-md-12 col-xs-12 num_rows">Items <span class="badge" ng-bind="rows"></span></div>
</div>
<div class="table-responsive">
	<table class="table table-hover table-striped table-bordered jambo_table bulk_action">
		<thead>
			<tr>
				<th width="10%"><div class="btn-sort" sorting="sys_action">Action</div></th>
				<th width="20%"><div class="btn-sort" sorting="lastupdate">Date&amp;Time</div></th>
				<th><div>Message</div></th>
				<th width="15%"><div class="btn-sort" sorting="update_name">By</div></th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="item in items">
				<td class="left">{{item.sys_action}}</td>
				<td>{{formatValue(item.lastupdate,'datetime')}}</td>
				<td class="left">"{{module}}" item was {{item.sys_action}} by {{item.update_name}} time {{formatValue(item.lastupdate,'datetime')}}</td>
				<td class="left">{{item.update_name}}</td>
			</tr>
		</tbody>
	</table>
  <div ng-if="rows==0" class="col-md-12 col-xs-12 not_found">- Data not found -</div>
</div>
<?php $this->load->view("template/control.php"); ?>
<input type="hidden" name="totalpage" id="totalpage" value="{{totalpage}}" />
<input type="hidden" name="thispage" id="thispage" value="1" />
<input type="hidden" name="pagesize" id="pagesize" value="{{pagesize}}" />
<input type="hidden" name="sorting" id="sorting" value="{{module}}_log.lastupdate" />
<input type="hidden" name="orderby" id="orderby" value="desc" />