<?php require 'views/include/header.php' ?>

<div class="col-md-6 m-auto">
    <?php if (isset($data['message'])) : ?>
        <div class="mt-5 alert <?= $data['message']['class'] ?>"><?= $data['message']['content'] ?></div>
    <?php endif ?>
    <div class="card card-body bg-light mt-5">
        <h2>Create an account</h2>
        <p>Please fill out this form to sign up</p>
        <form action="<?= URLROOT ?>/users/signup" method="post">
            <div class="form-group">
                <label for="first_name">First name: </label>
                <input type="text" name="first_name" class="form-control form-control-lg <?= isset($data['first_name_err']) ? 'is-invalid' : '' ?>" value="<?= isset($data['first_name']) ?: '' ?>">
                <span class="invalid-feedback"><?= isset($data['first_name_err']) ? $data['first_name_err'] : '' ?></span>
            </div>
            <div class="form-group">
                <label for="last_name">Last name: </label>
                <input type="text" name="last_name" class="form-control form-control-lg <?= isset($data['last_name_err']) ? 'is-invalid' : '' ?>" value="<?= isset($data['last_name']) ? $data['last_name'] : '' ?>">
                <span class="invalid-feedback"><?= isset($data['last_name_err']) ? $data['last_name_err'] : '' ?></span>
            </div>
            <div class="form-group">
                <label for="login">Login: </label>
                <input type="text" name="login" class="form-control form-control-lg <?= isset($data['login_err']) ? 'is-invalid' : '' ?>" value="<?= isset($data['login']) ? $data['login'] : '' ?>">
                <span class="invalid-feedback"><?= isset($data['login_err']) ? $data['login_err'] : '' ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email: </label>
                <input type="email" name="email" class="form-control form-control-lg <?= isset($data['email_err']) ? 'is-invalid' : '' ?>" value="<?= isset($data['email']) ? $data['email'] : '' ?>">
                <span class="invalid-feedback"><?= isset($data['email_err']) ? $data['email_err'] : '' ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password: </label>
                <input type="password" name="password" class="form-control form-control-lg <?= isset($data['password_err']) ? 'is-invalid' : '' ?>">
                <span class="invalid-feedback"><?= isset($data['password_err']) ? $data['password_err'] : '' ?></span>
            </div>
            <div class="form-group">
                <label for="password">Confirm password: <sup>*</sup></label>
                <input type="password" name="confirm_password" class="form-control form-control-lg <?= isset($data['confirm_password_err']) ? 'is-invalid' : '' ?>">
                <span class="invalid-feedback"><?= isset($data['confirm_password_err']) ? $data['confirm_password_err'] : '' ?></span>
            </div>
            <div class="row">
                <div class="col">
                    <input type="submit" value="Sign up" class="btn btn-success btn-block">
                </div>
                <div class="col">
                    <a href="<?= URLROOT ?>/users/login" class="btn btn-light btn-block">Already have an account? Sign in</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require 'views/include/footer.php' ?>