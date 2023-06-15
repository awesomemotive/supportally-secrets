<?php 
/**
 * Display the secret.
 */
$secret_url = $this->decrypt_secret();
include_once __DIR__.'/../views/header.html';
?>
	<main class="grow content overflow-x-hidden view secrets">
		<div class="secrets-form is-layout-flow wp-block-group alignwide">
			<div class="am-block am-block-icon-grid align"></div>
			<div class="is-layout-constrained wp-container-12 wp-block-group">
				<div class="wpforms-container wpforms-container-full">
					<form method="get" id="view-form" class="form secret-form wpforms-form">
						<div class="wpforms-field-container">
							<div class="form-field wpforms-field wpforms-field-textarea">
								<h3>View Your Secret</h3>
								<p class="label wpforms-field-label">
									Click to view the secret:
								</p>
								<textarea readonly style="display:none;" name="secret" id="alert-url">
									<?php echo ( '' === $secret_url ) ? htmlspecialchars( 'Secret expired or not found' ) : $secret_url; ?>
								</textarea>
							</div>
						</div>
						<button type="submit" class="btn btn-primary wpforms-submit" id="view-button">View secret</button>
					</form>
					<div id="alert" class="text-center wpforms-field wpforms-field-textarea" style="display: none;">
						<p id="alert-text"></p>
						<textarea id="display-url" readonly></textarea>
					</div>
					<?php if ( $secret_url ) : ?>
					<form method="post" id="delete-form" class="form delete-form wpforms-form" style="display: none;">
						<input type="hidden" name="secret_url" id="secret-delete">
						<button type="detete" class="btn btn-primary wpforms-submit" id="delete-button">Remove Secret</button>
					</form>
					<div id="alert-delete" class="text-center" style="display: none;"></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php include_once __DIR__.'/../views/brands.html'; ?>
	</main>
<?php include_once __DIR__.'/../views/footer.html'; ?>