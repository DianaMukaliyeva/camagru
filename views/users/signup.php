<?php require 'views/include/header.php' ?>

<div class="col-md-6 m-auto">
    <div class="card card-body bg-light mt-5">
        <h2>Create an account</h2>
        <p>Please fill out this form to sign up</p>
        <form action="<?php echo URLROOT; ?>/users/signup" method="post">
            <div class="form-group">
                <label for="first_name">First name: </label>
                <input type="text" name="first_name" class="form-control form-control-lg <?php echo (!empty($data['first_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['first_name']; ?>">
                <span class="invalid-feedback"><?php echo $data['first_name_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="last_name">Last name: </label>
                <input type="text" name="last_name" class="form-control form-control-lg <?php echo (!empty($data['last_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['last_name']; ?>">
                <span class="invalid-feedback"><?php echo $data['last_name_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="login">Login: </label>
                <input type="text" name="login" class="form-control form-control-lg <?php echo (!empty($data['login_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['login']; ?>">
                <span class="invalid-feedback"><?php echo $data['login_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email: </label>
                <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password: </label>
                <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Confirm password: <sup>*</sup></label>
                <input type="password" name="confirm_password" class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
                <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
            </div>
            <div class="row">
                <div class="col">
                    <input type="submit" value="Sign up" class="btn btn-success btn-block">
                </div>
                <div class="col">
                    <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-light btn-block">Already have an account? Sign in</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php require 'views/include/footer.php' ?>