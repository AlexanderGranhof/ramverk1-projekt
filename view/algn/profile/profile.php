<?php
// use DateTime;

$session = $this->di->get("session");
$uid = $user["id"];
$email = $user["email"];
$size = 40;
$grav_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "&s=" . $size;


$parsedown = new Parsedown();
$loggedInUid = $session->get("userid");
$userIsMod = !!$user["moderator"];
$loggedInUserIsMod = !!$loggedInUser["moderator"];

$isOwnProfile = $loggedInUid === $uid;

function time_elapsed_string($datetime, $full = false)
{
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

    if (!$full) {
        $string = array_slice($string, 0, 1);
    }

    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

$comments = [];

foreach ($activity["comments"] as $row) {
    $comments[$row["post_id"]][] = $row;
}
?>

<?php if (!$user) : ?>
    <h1 class="no-posts">No user around here ¯\_(ツ)_/¯</h1>
<?php else : ?>
    <div class="user-description">
        <a target="_BLANK" href="https://en.gravatar.com/">
            <img src="<?= $grav_url ?>" alt="">
        </a>
        <div class="username-container">
            <?php if ($isOwnProfile) : ?>
                <span class="logged-in-as">Logged in as:</span>
            <?php endif; ?>
            <h1 class="username"><?= $user["username"] ?><?= $userIsMod ? "<img class='mod-badge' src='../img/mod.png'>" : "" ?></h1>
            <span class="light">User score: <?= $score ?? 0 ?></span>
        </div>
        <?php if ($isOwnProfile) : ?>
            <form action="." method="POST">
                <input type="hidden" name="signout" value="true">
                <input type="submit" value="Sign out">
            </form>
        <?php endif; ?>
        <div class="bio">
            <form action="" class="edit-bio hidden">
                <textarea name="" id=""></textarea>
            </form>
            <p class="bio-text"><?= $user["bio"] ?? "<span class='edit-bio-title'>Click here to add or edit your bio</span>" ?></p>
        </div>
        <span class="bio-hint light">The bio will automatically save when you edit</span>
        <?php if ($loggedInUserIsMod && !$isOwnProfile && !$userIsMod) : ?>
            <form class="grant-mod" action="mod" method="POST">
                <input type="hidden" name="user-id" value="<?= $user["id"] ?>">
                <input type="hidden" name="user-name" value="<?= $user["username"] ?>">
                <input type="submit" value="Grant mod">
            </form>
        <?php endif; ?>
    </div>
    <div class="activity">
        <div class="comments">
            <h3>Previous comments</h3>
            <?php foreach ($comments as $row) : ?>
                <?php $comment = $row[0]; ?>
                <div class="comment-container">
                <div class="post-wrapper">
                    <a href="<?= $comment["post_username"] ?>">
                        <span class="light"><?= $comment["post_username"] ?> | <?= $comment["post_score"] ?? 0 ?> points | <?= time_elapsed_string($comment["post_created"]) ?></span>
                    </a>
                    <a href="../posts/<?= $comment["post_id"] ?>">
                        <h1><?= $comment["post_title"] ?></h1>
                    </a>

                    <?php foreach ($row as $comment) : ?>
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
            <?php foreach ($activity["posts"] as $post) : ?>
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

<script>
    const bio = document.querySelector("div.bio");
    const bioForm = document.querySelector("form.edit-bio");
    const textArea = document.querySelector("form.edit-bio > textarea");
    const editTitle = document.querySelector("span.edit-bio-title");
    const bioTextContainer = document.querySelector("div.bio > p.bio-text");

    const defaultText = "Click here to add or edit your bio";

    let hasSaved = true;
    let bioBeforeEdit = bioTextContainer.textContent

    if (bioBeforeEdit !== defaultText) {
        textArea.value = bioBeforeEdit;
    }


    function handleBioClick() {
        bioTextContainer.textContent = "";
        bioForm.classList.remove("hidden");
        editTitle && editTitle.classList.remove("hidden");
        bio.classList.add("editing");

        textArea.focus();
        
        hasSaved = false;
    }

    async function handleTextareaBlur() {
        bio.classList.remove("editing");
        bioForm.classList.add("hidden");
        editTitle && editTitle.classList.add("hidden");

        const newBio = textArea.value;

        bioTextContainer.innerHTML = newBio;

        let data = new FormData();

        data.append("bio", newBio);

        fetch("bio", {
            method: "POST",
            body: data
        });

        hasSaved = true;
    }

    bio.addEventListener("click", handleBioClick);
    textArea.addEventListener("blur", handleTextareaBlur);

    window.onbeforeunload = function(event) {
        const askBeforeReload = !hasSaved && bioBeforeEdit !== textArea.value;
        
        return askBeforeReload ? true : null;
    };
</script>