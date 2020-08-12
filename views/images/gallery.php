<?php if (isset($data['images'])) : ?>
    <?php foreach ($data['images'] as $image) : ?>
        <div class="col mb-4">
            <div class="card h-100 bg-light">
                <div class="media mt-3">
                    <img class="rounded-circle media-img mx-3" src="<?= URLROOT . '/assets/img/images/default.png' ?>" alt="profile image">
                    <div class="media-body">
                        <a class="text-decoration-none" href="#">
                            <p class="pt-3 font-weight-bold"><?= $image['user_login'] ?></p>
                        </a>
                    </div>
                </div>
                <div class="m-1">
                    <a class="text-decoration-none" href="#" onclick="openModal(<?= $image['id'] ?>)">
                        <img src="<?= URLROOT . '/' . $image['image_path'] ?>" class="img-fluid card-img-top" alt="<?= isset($image['title']) ? $image['title'] : 'no title' ?>">
                    </a>
                </div>
                <div class="card-body px-sm-4 px-2">
                    <button name="like" data-image-id="<?= $image['id'] ?>" id="like_button_<?= $image['id'] ?>" class="btn py-0 shadow-none"><i class="fas fa-heart icon-7x fa-lg <?= $image['user_liked'] ? 'my_like' : '' ?>"></i><span> <?= $image['likes_amount'] ?></span></button>
                    <button class="btn py-0 shadow-none"><i class="fas fa-comment icon-7x fa-lg"></i><span> 10</span></button>
                    <div class="float-right"><?= $image['created_at'] ?></div>
                </div>
                <form>
                    <div class="form-row mx-auto">
                        <div class="col-8">
                            <label for="<?= $image['id'] ?>" class="sr-only">Comment</label>
                            <input type="password" class="form-control" id="<?= $image['id'] ?>" placeholder="Comment...">
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-success mb-2">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach ?>
<?php else : ?>
    <div class="col-md-6 m-auto text-center pt-5">
        <p>No photo yet</p>
    </div>
<?php endif; ?>