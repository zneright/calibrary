<form action="<?= base_url('auth/updateResetPassword') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= $token ?>">
    <h3>Set New Password</h3>
    <input type="password" name="password" placeholder="New Password" required class="form-control">
    <input type="password" name="confirm_password" placeholder="Confirm Password" required class="form-control mt-2">
    <button type="submit" class="btn btn-success mt-3">Update Password</button>
</form>