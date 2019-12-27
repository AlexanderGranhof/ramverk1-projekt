<?php
    $email = $user["email"];
    $size = 40;
    $grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "&s=" . $size;
?>

<?php if (!$user): ?>
    <h1>User not found</h1>
<?php else: ?>
    <img src="<?= $grav_url ?>" alt="">
    <h1 class="username"><?= $user["username"] ?></h1>
    <div class="comments">
        <?php foreach($comments as $comment): ?>
            <div class="comment">
                <?= $comment["comment_text"] ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>