<?php require 'views/include/header.php' ?>

<div class="mt-3 input-group justify-content-center">
    <div id="radioBtn" class="btn-group">
        <a class="sort_images btn btn-light active" data-toggle="sort_image" data-title="newest">Newest</a>
        <a class="sort_images btn btn-light" data-toggle="sort_image" data-title="popular">Popular</a>
    </div>
    <input type="hidden" name="sort_image" id="sort_image">
</div>
<div class="container article-list" id="article-list"></div>
<!-- <div class="article-list row row-cols-1 row-cols-md-3 pt-5 m-auto px-md-5" id="article-list"></div> -->
<div id="load-more-container">
    <button id="load-more" data-page="0">Loading</button>
</div>
<ul class="article-list__pagination fixed article-list__pagination--inactive" id="post-pagination"></ul>
<script src="<?php echo URLROOT; ?>/assets/js/gallery.js"></script>
<?php require 'views/include/footer.php' ?>