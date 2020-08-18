<?php require 'views/include/header.php' ?>
<?php require 'views/modals/image.php' ?>

<!-- sorting buttons -->
<div class="mt-3 input-group justify-content-center">
    <div id="radioBtn" class="btn-group">
        <button class="sort_images shadow-none btn btn-light active" onclick="sortImages(this.dataset.title)" data-title="newest">Newest</button>
        <button class="sort_images shadow-none btn btn-light" onclick="sortImages(this.dataset.title)" data-title="popular">Popular</button>
    </div>
    <input type="hidden" name="sort_image" id="sort_image">
</div>

<!-- gallery -->
<div class="container article-list px-0 px-sm-4" id="article-list"></div>

<!-- button in case of big screen -->
<div class="text-center" id="load-more-container">
    <button class="btn btn-outline-info d-none" id="load-more" data-page="0">Load more</button>
</div>

<!-- page numeration -->
<ul class="article-list__pagination fixed article-list__pagination--inactive" id="post-pagination"></ul>
<script src="<?php echo URLROOT; ?>/assets/js/gallery.js"></script>

<?php require 'views/include/footer.php' ?>