<?php

    $session = $di->get("session");
    $req = $di->get("request");

    // $posts

    $userid = $session->get("userid");
    $error = $session->getOnce("error_posts");

    $tags = $req->getGet("tags") ?? "";

    $parsedown = new Parsedown();

    function time_elapsed_string_index($datetime, $full = false) {
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
    <?php if ($error): ?>
        <h1>Failed to create post</h1>
    <?php endif; ?>
    
    <?php if (count($posts) <= 0): ?>
        <h1 class="no-posts">No posts around here ¯\_(ツ)_/¯</h1>
    <?php else: ?>
    <div class="create-filter-container">
        <div>
            <?php if ($userid): ?>
                <a class="create-post" href="./posts/new">new post +</a>
            <?php endif ?>
        </div>
        <form class="<?= !$userid ? "full-width" : "" ?>" action="posts">
        <div class="input-container">
            <div>
                <label style="display: block" for="tags">Filter by tags (comma seperated):</label>
                <input type="text" name="tags" id="tags" value="<?= $tags ?>">
            </div>
            <input type="submit" value="Filter">
        </div>
    </div>
    </form>
        <?php foreach($posts as $post): ?>
            <div class="post">
                <a href="profile/<?= $post["username"] ?>" class="username"><?= $post["username"] ?> | <?= $post["score"] ?? 0 ?> points | <?= time_elapsed_string_index($post["created"]) ?> </a>
                <div class="tags">
                    <?php foreach(explode(",", $post["tags"]) as $tag): ?>
                    <a class="tag-wrapper" href="posts?tags=<?= $tag ?>">
                        <span class="tag"><?= $tag ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
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
