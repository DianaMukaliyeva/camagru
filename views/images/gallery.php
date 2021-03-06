<?php foreach ($data['images'] as $image) : ?>
    <div class="col px-md-1 px-lg-3 mb-4">
        <div class="card h-100 bg-light image-card">
            <!-- image's owner -->
            <?php if (!$data['profile']) : ?>
                <div class="media mt-3">
                    <img class="rounded-circle media-img mx-3" name="picture_<?= $image['user_id'] ?>" src="<?= URLROOT . '/' . $image['picture'] ?>" alt="profile image">
                    <div class="media-body">
                        <a class="text-decoration-none" href="<?= URLROOT . '/account/profile/' . $image['user_id'] ?>">
                            <p class="pt-3 font-weight-bold"><?= $image['login'] ?></p>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            <!-- image with tags -->
            <div class="m-1">
                <a class="text-decoration-none" href="#" onclick="openModal(<?= $image['id'] ?>)">
                    <img src="<?= URLROOT . '/' . $image['image_path'] ?>" class="img-fluid card-img-top" alt="no title">
                </a>
                <div class="text-center">
                    <?php foreach ($image['tags'] as $key => $tag) : ?>
                        #<?= $tag['tag'] ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- likes and comments -->
            <div class="card-body px-sm-4 px-2 py-0 pb-1">
                <button data-image-id="<?= $image['id'] ?>" onclick="like(this)" id="like_button_<?= $image['id'] ?>" class="btn py-0 shadow-none"><i class="fas fa-heart icon-7x fa-lg <?= $image['user_liked'] ? 'user_act' : '' ?>"></i><span> <?= $image['likes_amount'] ?></span></button>
                <button class="btn py-0 shadow-none" id="comments_<?= $image['id'] ?>" onclick="openModal(<?= $image['id'] ?>)"><i class="fas fa-comment icon-7x fa-lg <?= $image['user_commented'] ? 'user_act' : '' ?>"></i><span> <?= $image['comments_amount'] ?></span></button>
                <div class="float-right"><?= $image['created_at'] ?></div>
            </div>
            <!-- send comment form -->
            <?php if (!$data['profile']) : ?>
                <form method="post" onsubmit="addComment(this)" data-image-id="<?= $image['id'] ?>">
                    <div class="form-row mx-auto">
                        <div class="col-lg-9 col-8">
                            <input type="text" class="form-control" placeholder="Comment..." required maxlength="150">
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-success mb-2">Send</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach ?>