<form id="formPeriod" action="<?php echo site_url("master/cmdSavePeriod"); ?>" method="post">
	<button type="button" class="btn btn-primary btn-xs <?php if($period=="Today") echo "selected";?>" range="Today" style="text-transform: lowercase; height: 25px; width: 60px;">today</button>
	<button type="button" class="btn btn-primary btn-xs <?php if($period=="1W") echo "selected";?>" range="1W" style="text-transform: lowercase; height: 25px; width: 60px;">7 days</button>
	<button type="button" class="btn btn-primary btn-xs <?php if($period=="1M") echo "selected";?>" range="1M" style="text-transform: lowercase; height: 25px; width: 70px;">30 days</button>
	<button type="button" class="btn btn-primary btn-xs <?php if($period=="3M") echo "selected";?>" range="3M" style="text-transform: lowercase; height: 25px; width: 70px;">3 Months</button>
	<button type="button" class="btn btn-primary btn-xs <?php if($period=="Custom") echo "selected";?>" range="Custom" id="btnCustomPeriod" style="text-transform: lowercase; height: 25px; width: 70px;" >Custom</button>
	<input type="hidden" name="save_period" value="save_period" />
	<input type="hidden" name="period" value="<?php echo $period; ?>" />
	<input type="hidden" name="module" value="<?php echo $module; ?>" />
	<input type="hidden" name="graph_id" value="<?php echo $this->input->get("graph_id");?>" />
	<input type="hidden" name="graph_y" value="<?php echo $this->input->get("graph_y");?>" />
	<input type="hidden" name="graph_x" value="<?php echo $this->input->get("graph_x");?>" />
	<input type="hidden" name="graph_type" value="<?php echo $this->input->get("graph_type");?>" />
	<input type="text" id="custom_date" name="custom_date" class="form-control input-daterange-datepicker" value="<?php echo $custom_date;?>" />
	<input type="hidden" name="mediaType" value="<?php echo $this->input->get("mediaType");?>" />
	<input type="hidden" name="companyType" value="<?php echo $this->input->get("companyType");?>" />
	<input type="hidden" name="other_keyword" value="<?php echo $this->input->get("other_keyword");?>" />
</form>