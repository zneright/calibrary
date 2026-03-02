<form action="<?= base_url('auth/sendResetLink') ?>" method="POST">
    <?= csrf_field() ?>
    <h3>Forgot Password</h3>
    <input type="email" name="email" placeholder="Enter Admin Email" required class="form-control">
    <button type="submit" class="btn btn-primary mt-3">Send Reset Link</button>
</form>