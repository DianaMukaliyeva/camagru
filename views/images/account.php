<!-- check if we have images -->
<?php if (isset($data['images'])) : ?>
    <?php foreach ($data['images'] as $image) : ?>
        <div class="col mb-4">
            <div class="card h-100 bg-light image-card">
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
                <div class="card-body px-sm-4 px-2">
                    <button data-image-id="<?= $image['id'] ?>" onclick="like(this)" id="like_button_<?= $image['id'] ?>" class="btn py-0 shadow-none"><i class="fas fa-heart icon-7x fa-lg <?= $image['user_liked'] ? 'user_act' : '' ?>"></i><span> <?= $image['likes_amount'] ?></span></button>
                    <button class="btn py-0 shadow-none" id="comments_<?= $image['id'] ?>" onclick="openModal(<?= $image['id'] ?>)"><i class="fas fa-comment icon-7x fa-lg <?= $image['user_commented'] ? 'user_act' : '' ?>"></i><span> <?= $image['comments_amount'] ?></span></button>
                    <div class="float-right"><?= $image['created_at'] ?></div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
<?php else : ?>
    <!-- in case if no images -->
    <div class="col-md-6 m-auto text-center pt-5">
        <p>No photo yet</p>
    </div>
<?php endif; ?>