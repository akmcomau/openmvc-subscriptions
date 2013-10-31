<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form class="admin-form" method="post" id="form-subscription-type">
				<div class="widget">
					<div class="widget-header">
						<h3><?php
							if ($is_add_page) echo $text_add_header;
						 	else echo $text_update_header;
						?></h3>
					</div>
					<div class="widget-content">
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_name; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($type->name); ?>" />
								<?php echo $form->getHtmlErrorDiv('name'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_description; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<textarea class="form-control" name="description"><?php echo htmlspecialchars($type->description); ?></textarea>
								<?php echo $form->getHtmlErrorDiv('description'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_period; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<select class="form-control" name="period">
									<option value="months" <?php if (is_null($type->period) || $type->period == 'months') echo 'selected="selected"'; ?>>Months</option>
									<option value="days" <?php if ($type->period == 'days') echo 'selected="selected"'; ?>>Days</option>
								</select>
								<?php echo $form->getHtmlErrorDiv('period'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_period_length; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<input type="text" class="form-control" name="period_length" value="<?php echo htmlspecialchars($type->period_length); ?>" />
								<?php echo $form->getHtmlErrorDiv('period_length'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_price; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<input type="text" class="form-control" name="price" value="<?php echo htmlspecialchars(money_format('%^!n', $type->price)); ?>" />
								<?php echo $form->getHtmlErrorDiv('price'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_active; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<select class="form-control" name="active">
									<option value="1" <?php if (is_null($type->active) || $type->active) echo 'selected="selected"'; ?>>Yes</option>
									<option value="0" <?php if (!is_null($type->active) && !$type->active) echo 'selected="selected"'; ?>>No</option>
								</select>
								<?php echo $form->getHtmlErrorDiv('active'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="col-md-12 align-center">
							<button class="btn btn-primary" type="submit" name="form-subscription-type-submit"><?php
								if ($is_add_page) echo $text_add_button;
								else echo $text_update_button;
							?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	<?php echo $form->getJavascriptValidation(); ?>
</script>
