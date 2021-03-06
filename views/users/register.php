<?php require 'views/include/header.php' ?>

<div class="col-md-6 m-auto">
    <div class="card card-body bg-light mt-5">
        <h2>Create an account</h2>
        <p>Please fill out this form to sign up</p>
        <form onsubmit="register(this)" method="post">
            <div class="form-group">
                <label for="first_name">First name: </label>
                <input type="text" name="first_name" class="shadow-none form-control form-control-lg" value="" maxlength="25" required>
                <span name="first_name_err" class="invalid-feedback"></span>
            </div>
            <div class="form-group">
                <label for="last_name">Last name: </label>
                <input type="text" name="last_name" class="shadow-none form-control form-control-lg" value="" maxlength="35" required>
                <span name="last_name_err" class="invalid-feedback"></span>
            </div>
            <div class="form-group">
                <label for="login">Login: </label>
                <input type="text" name="login" class="shadow-none form-control form-control-lg" value="" maxlength="15" required>
                <span name="login_err" class="invalid-feedback"></span>
            </div>
            <div class="form-group">
                <label for="email">Email: </label>
                <input type="email" name="email" class="shadow-none form-control form-control-lg" value="" required>
                <span name="email_err" class="invalid-feedback"></span>
            </div>
            <div class="form-group">
                <label for="password">Password: </label>
                <input type="password" onkeyup="checkPasswordStrength(this)" name="password" class="shadow-none form-control form-control-lg" maxlength="128" required>
                <span name="password_err" class="invalid-feedback"></span>
            </div>
            <div class="form-group">
                <label for="password">Confirm password: <sup>*</sup></label>
                <input type="password" name="confirm_password" class="shadow-none form-control form-control-lg" maxlength="128" required>
                <span name="confirm_password_err" class="invalid-feedback"></span>
            </div>
            <div class="row">
                <div class="col">
                    <button id="registerButton" type="submit" class="btn btn-success btn-block">Sign up</button>
                </div>
                <div class="col">
                    <a href="<?= URLROOT ?>/users/login" class="btn btn-light btn-block">
                        <span>Already have an account? Sign in</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require 'views/include/footer.php' ?>