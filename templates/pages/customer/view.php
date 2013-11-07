<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form class="admin-form" method="post" id="form-subscription">
				<div class="widget">
					<div class="widget-header">
						<h3><?php echo $text_subscription; ?></h3>
					</div>
					<div class="widget-content">
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_reference; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<?php echo $subscription->getReferenceNumber(); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_customer; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<?php echo $subscription->getCustomer()->first_name.' '.$subscription->getCustomer()->last_name; ?><br />
								<?php echo $subscription->getCustomer()->email; ?><br />
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_subscription_type; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<?php echo $subscription->getType()->name; ?>
								(<?php echo $subscription->getType()->getPeriodString($this->language); ?>)
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_created; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<?php echo $subscription->created ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_expires; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<?php echo $subscription->expires; ?>
							</div>
						</div>
						<hr class="separator-2column" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
