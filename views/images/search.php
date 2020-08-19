<?php require 'views/include/header.php' ?>
<?php require 'views/modals/image.php' ?>

<div class="text-center" id="tagImages" data-tag="<?= $data['tag'] ?>">
    <h2 class="py-3">All images with tag #<?= $data['tag'] ?></h2>
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