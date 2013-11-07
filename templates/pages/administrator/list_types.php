<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form class="admin-search-form" method="get" id="form-subscription-type-search">
				<div class="widget">
					<div class="widget-header">
						<h3><?php echo $text_type_search; ?></h3>
					</div>
					<div class="widget-content">
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_name; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<input type="text" class="form-control" name="search_name" value="<?php echo htmlspecialchars($form->getValue('search_name')); ?>" />
									<?php echo $form->getHtmlErrorDiv('search_name'); ?>
								</div>
							</div>

						</div>
						<hr class="separator-2column" />
						<div class="align-right">
							<button type="submit" class="btn btn-primary" name="form-subscription-type-search-submit"><?php echo $text_search; ?></button>
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
					<h3><?php echo $text_type_search_results; ?></h3>
				</div>
				<div class="widget-content">
					<div class="pagination">
						<?php echo $pagination->getPageLinks(); ?>
					</div>
					<form action="<?php echo $this->url->getURL('administrator/Subscriptions', 'deleteType'); ?>" method="post">
						<table class="table">
							<tr>
								<th></th>
								<th nowrap="nowrap"><?php echo $text_name; ?> <?php echo $pagination->getSortUrls('title'); ?></th>
								<th nowrap="nowrap"><?php echo $text_period; ?> <?php echo $pagination->getSortUrls('tag'); ?></th>
								<th nowrap="nowrap"><?php echo $text_price; ?> <?php echo $pagination->getSortUrls('tag'); ?></th>
								<th></th>
							</tr>
							<?php foreach ($types as $type) { ?>
							<tr>
								<td class="select"><input type="checkbox" name="selected[]" value="<?php echo $type->id; ?>" /></td>
								<td><?php echo $type->name; ?></td>
								<td><?php echo $type->getPeriodString($this->language); ?></td>
								<td><?php echo money_format('%n', $type->price); ?></td>
								<td>
									<a href="<?php echo $this->url->getURL('administrator/Subscriptions', 'editType', [$type->id]); ?>" class="btn btn-primary"><i class="icon-edit-sign" title="<?php echo $text_edit; ?>"></i></a>
								</td>
							</tr>
							<?php } ?>
						</table>
						<button type="submit" class="btn btn-primary" name="form-subscription-type-list-submit" onclick="return deleteSelected();"><?php echo $text_delete_selected; ?></button>
					</form>
					<div class="pagination">
						<?php echo $pagination->getPageLinks(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	<?php echo $form->getJavascriptValidation(); ?>
	<?php echo $message_js; ?>
</script>
