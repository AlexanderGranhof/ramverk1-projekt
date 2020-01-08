<?php
    $uid = $user["id"];
    $email = $user["email"];
    $size = 40;
    $grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "&s=" . $size;

    $parsedown = new Parsedown();

    function time_elapsed_string_home($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
    
        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
    
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    $postTags = explode(",", $post["tags"])
?>

<div class="popular-list">
    <div style="width: 100%">
        <h1 class="most-popular-title">Most popular post</h1>
        <div class="post">
            <?php if ($post): ?>
                <a href="profile/<?= $post["username"] ?>" class="username"><?= $post["username"] ?> | <?= $post["score"] ?? 0 ?> points | <?= time_elapsed_string_home($post["created"]) ?></a>
                <a href="posts/<?= $post["id"] ?>">
                    <h1 class="title"><?= $post["title"] ?></h1>
                    <div class="tags">
                        <?php foreach($postTags as $tag): ?>
                        <a class="tag-wrapper" href="posts?tags=<?= $tag ?>">
                            <span class="tag"><?= $tag ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="content">
                        <?= $parsedown->text($post["content"]) ?>
                    </div>
                </a>
            <?endif;?>
        </div>
    </div>
    <div class="user-tag-container">
        <div>
            <h1 class="most-popular-title">Most popular User</h1>
            <? if($user): ?>
                <div class="user-description">
                    <img src="<?= $grav_url ?>" alt="">
                    <div class="username-container">
                        <h1 class="username"><?= $user["username"] ?></h1>
                        <span class="light">User score: <?= $score ?? 0 ?></span>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <div>
            <h1 class="most-popular-title" style="text-align: center">Most popular tag</h1>
            <?php if (count($tags)): ?>
                <?php foreach($tags as $tag => $tagScore): ?>
                    <a href="posts?tags=<?= $tag ?>">
                        <span style="display: grid; place-self: center; width: fit-content; margin: 0 auto" class="tag large"><?= $tag ?>: <?= $tagScore ?></span>
                    </a>
                    <?php break; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>