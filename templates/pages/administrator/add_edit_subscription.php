<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form class="admin-form" method="post" id="form-subscription">
				<div class="widget">
					<div class="widget-header">
						<h3><?php
							if ($is_add_page) echo $text_subscription_add_header;
						 	else echo $text_subscription_update_header;
						?></h3>
					</div>
					<div class="widget-content">
						<div class="row">
							<?php if ($is_add_page) { ?>
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_customer_email; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<input type="text" class="form-control" name="customer_email" value="<?php echo htmlspecialchars($form->getValue('customer_email')); ?>" />
									<?php echo $form->getHtmlErrorDiv('customer_email'); ?>
								</div>
							<?php } else { ?>
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_customer; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<?php echo $subscription->getCustomer()->first_name.' '.$subscription->getCustomer()->last_name; ?><br />
									<?php echo $subscription->getCustomer()->email; ?><br />
									Created: <?php echo $subscription->created; ?>
								</div>
							<?php } ?>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<?php if ($is_add_page) { ?>
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_subscription_type; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<select class="form-control" name="subscription_type">
										<option value=""></option>
										<?php foreach ($types as $type) { ?>
											<option value="<?php echo $type->id; ?>" <?php if ($form->getValue('subscription_type') == $type->id) echo 'selected="selected"'; ?>><?php echo $type->name; ?></option>
										<?php } ?>
									</select>
									<?php echo $form->getHtmlErrorDiv('subscription_type'); ?>
								</div>
							<?php } else { ?>
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_subscription; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<?php echo $subscription->getType()->name; ?>
									(<?php echo $subscription->getType()->getPeriodString($this->language); ?>)
								</div>
							<?php } ?>
						</div>
						<?php if (!$is_add_page) { ?>
							<hr class="separator-2column" />
							<div class="row">
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_expiry; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<input class="form-control" type="text" id="datepicker" name="expiry" />
									<?php echo $form->getHtmlErrorDiv('expiry'); ?>
								</div>
							</div>
						<?php } ?>
						<hr class="separator-2column" />
						<div class="col-md-12 align-center">
							<button class="btn btn-primary" type="submit" name="form-subscription-submit"><?php
								if ($is_add_page) echo $text_subscription_add_button;
								else echo $text_subscription_update_button;
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

	$("#datepicker").datepicker();
	$('#datepicker').datepicker('option', 'dateFormat', 'yy-mm-dd');
	$("#datepicker").datepicker("setDate" , new Date('<?php echo date('Y-m-d', strtotime($subscription->expires)); ?>'));
</script>
