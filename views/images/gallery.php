<?php require 'views/include/header.php' ?>
<div class="mt-3 input-group justify-content-center">
    <div id="radioBtn" class="btn-group">
        <a class="sort_images btn btn-light active" data-toggle="sort_image" data-title="newest">Newest</a>
        <a class="sort_images btn btn-light" data-toggle="sort_image" data-title="popular">Popular</a>
    </div>
    <input type="hidden" name="sort_image" id="sort_image">
</div>
<?php if (!empty($images)) : ?>
    <?php foreach ($data['posts'] as $post) : ?>
        <div class="card card-body mb-3">
            <h4 class="card-title"><?php echo $post->title; ?></h4>
            <div class="bg-light p-2 mb-3">
                Written by <?php echo $post->name; ?> on <?php echo $post->postCreated; ?>
            </div>
            <p class="card-text"><?php echo $post->body; ?></p>
            <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $post->postId; ?>" class="btn btn-dark">More</a>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="col-md-6 m-auto text-center pt-5">
        <h3>No photo yet...</h3>
    </div>
<?php endif; ?>
<?php require 'views/include/footer.php' ?>