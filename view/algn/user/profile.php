<?php
    $session = $this->di->get("session");
    $uid = $user["id"];
    $email = $user["email"];
    $size = 40;
    $grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "&s=" . $size;

    $parsedown = new Parsedown();
    $loggedInUid = $session->get("userid");

    $isOwnProfile = $loggedInUid === $uid;

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

    $comments = [];

    foreach($activity["comments"] as $row) {
        $comments[$row["post_id"]][] = $row;
    }
?>

<?php if (!$user): ?>
    <h1 class="no-posts">No user around here ¯\_(ツ)_/¯</h1>
<?php else: ?>
    <div class="user-description">
        <img src="<?= $grav_url ?>" alt="">
        <div class="username-container">
            <?php if ($isOwnProfile): ?>
                <span class="logged-in-as">Logged in as:</span>
            <?php endif; ?>
            <h1 class="username"><?= $user["username"] ?></h1>
        </div>
        <?php if ($isOwnProfile): ?>
            <form action="." method="POST">
                <input type="hidden" name="signout" value="true">
                <input type="submit" value="Sign out">
            </form>
        <?php endif; ?>
    </div>
    <div class="activity">
        <div class="comments">
            <h3>Previous comments</h3>
            <?php foreach($comments as $row): ?>
                <?php $comment = $row[0]; ?>
                <div class="comment-container">
                <div class="post-wrapper">
                    <a href="<?= $comment["post_username"] ?>">
                        <span class="light"><?= $comment["post_username"] ?> | <?= $comment["post_score"] ?? 0 ?> points | <?= time_elapsed_string($comment["post_created"]) ?></span>
                    </a>
                    <a href="../posts/<?= $comment["post_id"] ?>">
                        <h1><?= $comment["post_title"] ?></h1>
                    </a>

                    <?php foreach($row as $comment): ?>
                            <div class="comment">
                                <span class="light"><?= $user["username"] ?> | <?= $comment["comment_score"] ?? 0 ?> points | <?= time_elapsed_string($comment["created"]) ?></span>
                                <p><?= $parsedown->text($comment["comment_text"]) ?></p>
                            </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="posts">
            <h3>Previous posts</h3>
            <?php foreach($activity["posts"] as $post): ?>
                <!-- <?= var_dump($post) ?> -->
                <div class="user-post post">
                    <a href="<?= $post["username"] ?>">
                        <span class="light"><?= $post["username"] ?> | <?= $post["score"] ?? 0 ?> points | <?= time_elapsed_string($post["created"]) ?></span>
                    </a>
                    <a href="../posts/<?= $post["post_id"] ?>">
                        <h1><?= $post["title"] ?></h1>
                        <p><?= $parsedown->text($post["content"]) ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>