<?php

    $session = $di->get("session");

    // $posts

    $userid = $session->get("userid");
    $error = $session->getOnce("error_posts");

    $parsedown = new Parsedown();

    function time_elapsed_string($datetime, $full = false) {
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
?>

<div class="posts-container">
    <?php if ($userid): ?>
        <a class="create-post" href="./posts/new">new post +</a>
    <?php endif ?>
    
    <?php if ($error): ?>
        <h1>Failed to create post</h1>
    <?php endif; ?>
    
    <?php if (count($posts) <= 0): ?>
        <h1 class="no-posts">No posts around here ¯\_(ツ)_/¯</h1>
    <?php else: ?>
        <?php foreach($posts as $post): ?>
            <div class="post">
                <a href="user/<?= $post["username"] ?>" class="username"><?= $post["username"] ?> | <?= $post["score"] ?? 0 ?> points | <?= time_elapsed_string($post["created"]) ?> </a>
                <a class="post-link" href="posts/<?= $post["id"] ?>">
                    <h1 class="title"><?= $post["title"] ?></h1>
                    <div class="preview-content">
                        <?= $parsedown->text($post["content"]) ?>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
