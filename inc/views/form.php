<?php
/**
 * View for the form.
 *
 */
include __DIR__.'/header.html';
?>
	<main class="grow content overflow-x-hidden secrets">
		<div class="form-text has-text-color">
			<p>Paste a password, secret message or private link below.</br>
			<span class="has-neutral-600-color">Keep sensitive info out of your email and chat logs.</span></p>
		</div>
		<div class="secrets-form is-layout-flow wp-block-group alignwide">
			<div class="am-block am-block-icon-grid align"></div>
			<div class="is-layout-constrained wp-container-12 wp-block-group">
				<div class="wpforms-container wpforms-container-full">
					<form method="post" id="share-secret" class="form secret-form wpforms-form">

						<div class="wpforms-field-container">
							<div class="form-field wpforms-field wpforms-field-textarea">
								<textarea name="secret" id="secret" class="form-control" placeholder="Secret content goes here..."></textarea>
							</div>
						</div>
						<div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_V2_SITE_KEY; ?>"></div>
						<button type="submit" class="btn btn-primary wpforms-submit" id="submit-button">Create Secret Link</button>
					</form>
					<div class="form-text-below has-neutral-600-color has-text-color">
						<p>Share information with your customer service agent without it being stored in email in 3 simple steps</p>
						<ol type="1">
							<li>Sumbit the form below and receive a secure link</li>
							<li>Provide the link to your support agent</li>
							<li>The secure information is automatically removed within 30 days or when the support agent no longer needs, whichever comes first </li>
						</ol>	
					</div>
					<div id="alert" class="text-center wpforms-field wpforms-field-textarea" style="display: none;">
						<p id="alert-text"></p>
						<textarea id="display-url" readonly></textarea>
					</div>
					<form method="post" id="delete-form" class="form delete-form wpforms-form">
						<input type="hidden" name="secret_url" id="secret-delete">
						<button type="detete" class="btn btn-primary wpforms-submit" id="delete-button">Remove Secret</button>
					</form>
					<div id="alert-delete" class="text-center" style="display: none;">
				</div>
			</div>
		</div>
	</div>
	<div class="am-block am-block-spacer align">
		<div class="h-12 md:h-24"></div>
	</div>

	<?php include __DIR__.'/brands.html'; ?>

	</main>
<?php include __DIR__.'/footer.html'; ?>
