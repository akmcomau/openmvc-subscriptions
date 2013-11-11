<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-header">
					<h3><?php echo $text_subscription_search_results; ?></h3>
				</div>
				<div class="widget-content">
					<div class="pagination">
						<?php echo $pagination->getPageLinks(); ?>
					</div>
					<table class="table">
						<tr>
							<th nowrap="nowrap"><?php echo $text_subscription_type; ?> <?php echo $pagination->getSortUrls('email'); ?></th>
							<th nowrap="nowrap"><?php echo $text_created; ?> <?php echo $pagination->getSortUrls('created'); ?></th>
							<th nowrap="nowrap"><?php echo $text_expiry; ?> <?php echo $pagination->getSortUrls('expires'); ?></th>
							<th nowrap="nowrap"><?php echo $text_paid; ?> <?php echo $pagination->getSortUrls('price'); ?></th>
							<th></th>
						</tr>
						<?php foreach ($subscriptions as $subscription) { ?>
							<tr>
								<td><?php echo $subscription->getType()->name; ?></td>
								<td><?php echo $subscription->created; ?></td>
								<td><?php echo $subscription->expires; ?></td>
								<td><?php echo money_format('%n', $subscription->getPricePaid()); ?></td>
								<td>
									<a href="<?php echo $this->url->getUrl('customer/Subscriptions', 'view', [$subscription->getReferenceNumber()]); ?>" class="btn btn-primary"><i class="icon-edit-sign" title="<?php echo $text_edit; ?>"></i></a>
								</td>
							</tr>
						<?php } ?>
					</table>
					<div class="pagination">
						<?php echo $pagination->getPageLinks(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	<?php echo $message_js; ?>
</script>
