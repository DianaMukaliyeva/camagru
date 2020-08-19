<nav class="fixed-top navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= URLROOT ?>">
            <img src="<?= URLROOT ?>/assets/img/images/logo1.png" alt="Logo" style="height:40px;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <form class="form-inline my-2 mx-5 my-lg-0 ml-auto position-relative" autocomplete="off">
                <input class="form-control" id="search" type="search" placeholder="Search" aria-label="Search">
                <div class="live_search position-absolute rounded p-2 d-none" id="live_search_columns"></div>
            </form>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION[APPNAME]['user'])) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URLROOT ?>/camera/takePhoto">Add photo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URLROOT ?>/account/profile/<?= $_SESSION[APPNAME]['user']['id'] ?>">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URLROOT ?>/users/logout">Logout</a>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URLROOT ?>/users/login">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URLROOT ?>/users/register">Sign up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>