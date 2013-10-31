<div class="container">
	<h1>Subscriptions</h1>
	<div class="row margin-bottom-40">
		<?php foreach ($types as $type) { ?>
			<div class="col-md-3 col-sm-6">
				<div class="pricing hover-effect">
					<div class="pricing-head">
						<h3><?php echo $type->name; ?></h3>
						<?php if ($type->price == 0) { ?>
						<h4><?php echo $text_free; ?></h4>
						<?php } else { ?>
							<h4><i>$</i><?php echo (int)$type->price; ?><i>.<?php echo str_pad(str_replace("0.", "", $type->price - (int)$type->price), 2, '0'); ?></i></h4>
						<?php } ?>
					</div>
					<div class="pricing-footer">
						<p><?php echo $type->description; ?></p>
						<form action="<?php echo $this->url->getURL('Cart', 'add', ['subscription', $type->id]); ?>" method="post">
							<button type="submit"><?php echo $text_order_now; ?></button>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
