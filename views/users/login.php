<?php require 'views/include/header.php' ?>

<div class="col-md-6 m-auto">
    <?php if (isset($data['message']['class']) && isset($data['message']['content'])) : ?>
        <div class="mt-5 alert <?= $data['message']['class'] ?>"><?= $data['message']['content'] ?></div>
    <?php endif ?>
    <div class="card card-body bg-light mt-5">
        <h2>Login</h2>
        <p>Please fill in your credentials to log in</p>
        <form action="<?= URLROOT ?>/users/login" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control form-control-lg <?= isset($data['email_err']) ? 'is-invalid' : '' ?>" value="<?= isset($data['email']) ? $data['email'] : '' ?>">
                <span class="invalid-feedback"><?= isset($data['email_err']) ? $data['email_err'] : '' ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control form-control-lg <?= isset($data['password_err']) ? 'is-invalid' : '' ?>">
                <span class="invalid-feedback"><?= isset($data['password_err']) ? $data['password_err'] : '' ?></span>
            </div>
            <div class="row">
                <div class="col">
                    <input type="submit" value="Login" class="btn btn-success btn-block">
                </div>
                <div class="col">
                    <a href="<?= URLROOT ?>/users/resetPassword" class="btn btn-light btn-block">Forgot password? Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require 'views/include/footer.php' ?>