<?php require 'views/include/header.php' ?>

<div class="col-md-6 m-auto">
    <?php if (isset($data['message'])) : ?>
        <div class="mt-5 alert <?= $data['message']['class'] ?>"><?= $data['message']['content'] ?></div>
    <?php endif ?>
    <div class="card card-body bg-light mt-5">
        <h2>Login</h2>
        <p>Please fill in your credentials to log in</p>
        <form onsubmit="login(this)" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="shadow-none form-control form-control-lg" value="">
                <span name="email_err" class="invalid-feedback"></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="shadow-none form-control form-control-lg">
                <span name="password_err" class="invalid-feedback"></span>
            </div>
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-success btn-block">Login</button>
                </div>
                <div class="col">
                    <a href="<?= URLROOT ?>/account/forgetPassword" class="btn btn-light btn-block">Forgot password? Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require 'views/include/footer.php' ?>