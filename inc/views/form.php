<?php 
/**
 * View for the form.
 *
 */
include __DIR__.'/header.html';
?>
	<main class="grow content overflow-x-hidden">
		<div class="form-text has-neutral-600-color has-text-color">
			<p>Share information with your customer service agent without it being stored in email in 3 simple steps</p>
			<ol type="1">
				<li>Sumbit the form below and receive a secure link</li>
				<li>Provide the link to your support agent</li>
				<li>The secure information is automatically removed within 30 days or when the support agent no longer needs, whichever comes first </li>
			</ol>	
		</div>
		<div class="is-layout-flow wp-block-group alignwide">
			<div class="am-block am-block-icon-grid align"></div>
			<div class="is-layout-constrained wp-container-12 wp-block-group  has-primary-50-background-color has-background">
				<div class="wpforms-container wpforms-container-full">
					<form method="post" id="share-secret" class="form secret-form wpforms-form">

						<div class="wpforms-field-container">
							<div class="form-field wpforms-field wpforms-field-textarea">
								<h3>Secure Entry Form</h3>
								<p class="label wpforms-field-label">
									Secure information:<span class="required">*</span>
								</p>
								<textarea name="secret" id="secret" class="form-control"></textarea>
							</div>
						</div>
						<div class="g-recaptcha" data-sitekey="6LfzLTwmAAAAABw1orGpihxHx-dmbnAoX-8-HLsk"></div>
						<button type="submit" class="btn btn-primary wpforms-submit" id="submit-button">Create Secret Link</button>
					</form>
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
