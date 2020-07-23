<?php require 'views/include/header.php' ?>

<div class="col-md-6 m-auto">
    <?php if (isset($data['reset'])) : ?>
        <div class="card card-body bg-light mt-5">
            <h2>Change your password</h2>
            <form action="<?php echo URLROOT; ?>/users/resetPassword" method="post">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="hidden" name="email" value="<?= $data['email'] ?>">
                    <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm password:</label>
                    <input type="password" name="confirm_password" class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Reset password" class="btn btn-success btn-block">
                    </div>
                </div>
            </form>
        </div>
    <?php else : ?>
        <div class="card card-body bg-light mt-5">
            <form action="<?php echo URLROOT; ?>/users/resetPassword" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Send reset password email" class="btn btn-success btn-block">
                    </div>
                </div>
        </div>
        </form>
    <?php endif ?>
</div>

<?php require 'views/include/footer.php' ?>