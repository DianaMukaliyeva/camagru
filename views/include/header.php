<?php
if (isset($_SESSION['last_activity']) && $_SESSION['last_activity'] < time() - $_SESSION['expire_time']) {
    // logout if user inactive for the given expire_time
    unset($_SESSION[APPNAME]['user']);
} else {
    // set the moment of last activity
    $_SESSION['last_activity'] = time();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Camagru</title>
    <!-- Bootstrap css framework without javascript -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <!-- Fontawesome icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Css files -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/gallery.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/photomaker.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/pagination.css">
</head>

<body>
    <div class="wrapper">
        <?php require 'views/include/navbar.php'; ?>
        <div class="content">
            <?php require 'views/modals/alert.php' ?>