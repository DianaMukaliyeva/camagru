<?php require 'views/include/header.php' ?>

<!-- form to send email with reset password credentials -->
<div class="col-md-6 m-auto">
    <div class="card card-body bg-light mt-5">
        <form onsubmit="forgetPassword(this)" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="shadow-none form-control form-control-lg">
                <span name="email_err" class="invalid-feedback"></span>
            </div>
            <div class="row">
                <div class="col">
                    <button id="forgetPasswordButton" type="submit" class="btn btn-success btn-block">Send reset password email</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require 'views/include/footer.php' ?>
