<!-- modal window for profile settings -->
<div class="modal fade" id="settings" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profile edit settings</h5>
                <button type="button" class="close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" onsubmit="saveChanges(this)">
                    <div class="row py-1">
                        <div class="col-sm-4">Login:</div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="login" required maxlength="15">
                            <span id="modal_login_err" class="invalid-feedback"></span>
                        </div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">First name:</div>
                        <div class="col-sm-8" name="test">
                            <input type="text" class="form-control" name="first_name" required maxlength="25">
                            <span id="modal_first_name_err" class="invalid-feedback"></span>
                        </div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">Last name:</div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="last_name" required maxlength="25">
                            <span id="modal_last_name_err" class="invalid-feedback"></span>
                        </div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">email:</div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="email" required>
                            <span id="modal_email_err" class="invalid-feedback"></span>
                        </div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">New password:</div>
                        <div class="col-sm-4 pr-sm-1 pb-1 pb-sm-0">
                            <input type="password" class="form-control" name="new_pswd" placeholder="new password" maxlength="25">
                            <span id="modal_password_err" class="invalid-feedback"></span>
                        </div>
                        <div class="col-sm-4 pl-sm-1">
                            <input type="password" class="form-control" name="new_pswd_confirm" placeholder="confirm new password" maxlength="25">
                            <span id="modal_confirm_password_err" class="invalid-feedback"></span>
                        </div>
                    </div>
                    <div class="custom-control custom-checkbox py-2 mx-3">
                        <input type="checkbox" class="custom-control-input" name="notifications" id="profile-notifications">
                        <label class="shadow-none custom-control-label" for="profile-notifications">Send me email notifications</label>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-4">Password:*</div>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" name="old_pswd" placeholder="Required to update account" required>
                            <span id="modal_pass_err" class="invalid-feedback"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="editForm" class="btn btn-success">Save changes</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>
</div>
