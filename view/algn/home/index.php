<?php 
    $tags = $tags ? $tags : [];
    $comments = $comments ? $comments : [];
    $users = $users ? $users : [];
    $posts = $posts ? $posts : [];

    $slicedTags = array_slice($tags, 0, 10);
    $slicedComments = array_slice($comments, 0, 10);
    $slicedUsers = array_slice($users, 0, 10);
    $slicedPosts = array_slice($posts, 0, 10);

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
?>

<div class="popular">
    <div>
        <h1>Most popular posts</h1>
        <div>
            <?php foreach($slicedPosts as $post): ?>
            <div class="post">
                <a href="user/<?= $post["username"] ?>" class="username"><?= $post["username"] ?> | <?= $post["score"] ?? 0 ?> points | <?= time_elapsed_string_home($post["created"]) ?></a>
                <a href="posts/<?= $post["id"] ?>">
                    <h1 class="title"><?= $post["title"] ?></h1>
                    <div class="content">
                        <?= $parsedown->text($post["content"]) ?>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div>
        <h1>Most active users</h1>
        <div>
            <?php foreach($slicedUsers as $row): ?>
                <a class="user-container" href="user/<?= $row["username"] ?>">
                <div class="user">
                    <span class="light large">Joined <?= time_elapsed_string_home($row["created"]) ?> | <?= $row["score"] ?? 0 ?> points</span>
                    <h2 class="username"><?= $row["username"] ?></h2>
                </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div>
        <h1>Top comments</h1>
        <div>
            <?php foreach($slicedComments as $row): ?>
            <div class='comment'>
                <div data-id='<?= $row["post_id"] ?>' $answerClass>
                    <a class='light' href='user/<?= $row["username"] ?>'><?= $row["username"] ?> | <?= $row["score"] ?? 0 ?> points | <?= time_elapsed_string_home($row["created"]) ?></a>
                    <div class='comment-text'><?= $row["comment_text"] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="popular-tags">
        <h1>Most popular tags</h1>
        <div class="tags">
            <?php foreach($slicedTags as $tag => $score): ?>
                <a href="posts?tags=<?= $tag ?>" class="single-tag"><?= $tag ?? "?" ?>: <?= $score ?? "" ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>