<?php if (isset($data['images'])) : ?>

    <!-- <div class="row row-cols-1 row-cols-md-3 pt-5 m-auto px-md-5"> -->
    <?php foreach ($data['images'] as $image) : ?>
        <div class="col mb-4">
            <div class="card h-100 bg-light">
                <div class="media mt-3">
                    <img class="rounded-circle media-img mx-3" src="<?= URLROOT . '/assets/img/images/default.png' ?>" alt="profile image">
                    <div class="media-body">
                        <a href="<?= URLROOT ?>">
                            <h5 class="pt-3"><?= $image['id'] ?></h5>
                        </a>
                    </div>
                </div>
                <div class="m-1">
                    <img src="<?= URLROOT . '/' . $image['image_path'] ?>" class="img-fluid card-img-top" alt="<?= isset($image['title']) ? $image['title'] : 'no title' ?>">
                </div>
                <div class="card-body">
                    <div class="float-left pr-2"><i class="fas fa-comment icon-7x"></i> 10</div>
                    <div class="float-left pr-2"><i class="fas fa-heart icon-7x"></i> 5</div>
                    <div class="float-right"><?= $image['created_at'] ?></div>
                </div>
                <form class="form-inline">
                    <div class="form-group mx-3 mb-2">
                        <label for="<?= $image['id'] ?>" class="sr-only">Comment</label>
                        <input type="password" class="form-control" id="<?= $image['id'] ?>" placeholder="Comment...">
                    </div>
                    <button type="submit" class="btn btn-success mb-2">Send</button>
                </form>
            </div>
        </div>
    <?php endforeach ?>
    <!-- </div> -->
<?php else : ?>
    <div class="col-md-6 m-auto text-center pt-5">
        <p>No photo yet</p>
    </div>
<?php endif; ?>