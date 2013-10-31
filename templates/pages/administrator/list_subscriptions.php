<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form class="admin-search-form" method="get" id="form-subscription-search">
				<div class="widget">
					<div class="widget-header">
						<h3><?php echo $text_subscription_search; ?></h3>
					</div>
					<div class="widget-content">
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_first_name; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<input type="text" class="form-control" name="search_first_name" value="<?php echo htmlspecialchars($form->getValue('search_first_name')); ?>" />
									<?php echo $form->getHtmlErrorDiv('search_first_name'); ?>
								</div>
							</div>
							<div class="col-md-6 visible-xs">
								<hr class="separator-2column" />
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_last_name; ?></div>
									<div class="col-md-9 col-sm-9 ">
										<input type="text" class="form-control" name="search_last_name" value="<?php echo htmlspecialchars($form->getValue('search_last_name')); ?>" />
										<?php echo $form->getHtmlErrorDiv('search_last_name'); ?>
									</div>
								</div>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_login; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<input type="text" class="form-control" name="search_login" value="<?php echo htmlspecialchars($form->getValue('search_login')); ?>" />
									<?php echo $form->getHtmlErrorDiv('search_login'); ?>
								</div>
							</div>
							<div class="col-md-6 visible-xs">
								<hr class="separator-2column" />
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_email; ?></div>
									<div class="col-md-9 col-sm-9 ">
										<input type="text" class="form-control" name="search_email" value="<?php echo htmlspecialchars($form->getValue('search_email')); ?>" />
										<?php echo $form->getHtmlErrorDiv('search_email'); ?>
									</div>
								</div>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="align-right">
							<button type="submit" class="btn btn-primary" name="form-subscription-search-submit"><?php echo $text_search; ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

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
					<form action="<?php echo $this->url->getURL('administrator/Subscriptions', 'deleteSubscriptions'); ?>" method="post">
						<table class="table">
							<tr>
								<th></th>
								<th nowrap="nowrap"><?php echo $text_email; ?> <?php echo $pagination->getSortUrls('email'); ?></th>
								<th nowrap="nowrap"><?php echo $text_created; ?> <?php echo $pagination->getSortUrls('created'); ?></th>
								<th nowrap="nowrap"><?php echo $text_expiry; ?> <?php echo $pagination->getSortUrls('expires'); ?></th>
								<th nowrap="nowrap"><?php echo $text_paid; ?> <?php echo $pagination->getSortUrls('price'); ?></th>
								<th></th>
							</tr>
							<?php foreach ($subscriptions as $subscription) { ?>
							<tr>
								<td class="select"><input type="checkbox" name="selected[]" value="<?php echo $subscription->id; ?>" /></td>
								<td><?php echo $subscription->getCustomer()->email; ?></td>
								<td><?php echo $subscription->created; ?></td>
								<td><?php echo $subscription->expires; ?></td>
								<td><?php echo money_format('%n', $subscription->price); ?></td>
								<td>
									<a href="<?php echo $this->url->getURL('administrator/Subscriptions', 'editSubscription', [$subscription->id]); ?>" class="btn btn-primary"><i class="icon-edit-sign" title="<?php echo $text_edit; ?>"></i></a>
								</td>
							</tr>
							<?php } ?>
						</table>
						<button type="submit" class="btn btn-primary" name="form-subscription-type-list-submit" onclick="return deleteSelected();"><?php echo $text_delete_selected; ?></button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	<?php echo $form->getJavascriptValidation(); ?>
	<?php echo $message_js; ?>
</script>
