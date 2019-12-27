<?php
    $parsedown = new Parsedown();
    $req = $this->di->get("request");

    $userCommentScores = [];

    foreach($userUpvoted as $row) {
        $userCommentScores[$row["comment_id"]] = $row["score"];
    }
    
    $sorted = $req->getGet("sort");

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
    <div class="post single">
        <div class="vote" data-id="<?= $post["id"] ?>">
            <span class="arrow-up <?= $postScore == 1 ? "selected" : "" ?>">▶</span>
            <span class="arrow-down <?= $postScore == -1 ? "selected" : "" ?>">▶</span>
        </div>
        <div>
            <a href="users/<?= $post["username"] ?>" class="username"><?= $post["username"] ?> | <?= $post["score"] ?> points | <?= time_elapsed_string($post["created"]) ?></a>
            <h1 class="title"><?= $post["title"] ?></h1>
            <div class="content">
                <?= $parsedown->text($post["content"]) ?>
            </div>
        </div>
    </div>
</div>

<div class="comments-container">
    <div class="write-comment-container">
        <textarea id="comment" placeholder="What are your thoughts?" name="comment"></textarea>
        <div class="extras">
            <button id="writeComment" class="commentButton">comment</button>
        </div>
    </div>
    
    <div class="comments">
        <form class="sort" action="">
            <label for="sort">Sort by:</label>
            <select name="sort" id="sort">
                <option <?= $sorted == "upvotes" ? "selected" : "" ?> value="upvotes">upvotes</option>
                <option <?= $sorted == "date" ? "selected" : "" ?> value="date">date</option>
            </select>
            <input type="submit" value="Update">
        </form>
    
        <?php foreach ($comments as $comment): ?>
        <?php $score = $userCommentScores[$comment["id"]] ?? null ?>
            <div class="comment">
                <div class="vote" data-id="<?= $comment["id"] ?>">
                    <span class="arrow-up <?= $score == 1 ? "selected" : "" ?>">▶</span>
                    <span class="arrow-down <?= $score == -1 ? "selected" : "" ?>">▶</span>
                </div>
                <div>
                    <a class="username" href="../user/<?= $comment["username"] ?>"><?= $comment["username"] ?> | <?= $comment["score"] ?? 0 ?> points | <?= time_elapsed_string($comment["created"]) ?? "" ?></a>
                    <p class="comment-text"><?= $parsedown->text($comment["comment_text"]) ?></p>
                    <div class="comment-extras">
                        <span class="reply-button">reply</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const commentField = document.getElementById("comment");
    const commentButton = document.getElementById("writeComment");

    async function writeComment() {
        const text = commentField.value;
        const postID = parseInt(window.location.pathname.split("/").pop());
        const commentsContainer = document.querySelector("comments");

        if (!text.length) {
            return alert("Your comment cannot be empty");
        }

        const data = new FormData();

        data.append("id", postID);
        data.append("content", text);

        const response = await fetch("./comment", {
            method: "POST",
            body: data
        });

        const result = await response.text();

        if (result !== "true") {
            alert("Error may have occured when submitting comment");
            console.log("Comment unsuccessful");
            console.log(result);
            return
        }

        commentField.value = "";

        window.location.reload();
    }

    commentButton.addEventListener("click", writeComment);

    const voteButtons = document.querySelectorAll(".comment .arrow-up, .comment .arrow-down");


    for (const button of voteButtons) {
        button.addEventListener("click", handleVote)
    }

    async function handleVote() {
        const scoreField = this.parentElement.nextElementSibling.firstElementChild;

        const commentID = this.parentElement.dataset.id;
        const oppositeButton = Array.from(this.parentElement.children)
                                    .filter(element => element.className !== this.className).shift();

        let votescore = this.classList.contains("arrow-up") ? 1 : -1;

        if (this.classList.contains("selected")) {
            votescore = 0;
        }

        const data = new FormData();

        data.append("score", votescore);
        data.append("id", commentID);

        const response = await fetch("commentVote", {
            method: "POST",
            body: data
        });

        if (response.ok) {
            let isUndo = this.classList.contains("selected");
            let isDownvote = this.classList.contains("arrow-down");
            let isOtherSelected = oppositeButton.classList.contains("selected");

            this.classList.toggle("selected");
            oppositeButton.classList.remove("selected");

            let scoreText = scoreField.textContent;

            let [username,score,created] = scoreText.split("|");


            let points = isDownvote ? isUndo ? 1 : -1 : isUndo ? -1 : 1;

            points = isOtherSelected ? points * 2 : points;

            score = ` ${parseInt(score) + points} points `;

            scoreField.textContent = `${username}|${score}|${created}`;
        }
    }

    const postVotes = document.querySelectorAll(".post.single .arrow-up, .post.single .arrow-down");

    for (const btn of postVotes) {
        btn.addEventListener("click", handlePostVote);
    }



    async function handlePostVote() {
        const scoreField = this.parentElement.nextElementSibling.firstElementChild;

        const postID = this.parentElement.dataset.id;
        const oppositeButton = Array.from(this.parentElement.children)
                                    .filter(element => element.className !== this.className).shift();

        let votescore = this.classList.contains("arrow-up") ? 1 : -1;

        if (this.classList.contains("selected")) {
            votescore = 0;
        }

        const data = new FormData();

        data.append("score", votescore);
        data.append("id", postID);

        const response = await fetch("postVote", {
            method: "POST",
            body: data
        });

        if (response.ok) {
            let isUndo = this.classList.contains("selected");
            let isDownvote = this.classList.contains("arrow-down");
            let isOtherSelected = oppositeButton.classList.contains("selected");

            this.classList.toggle("selected");
            oppositeButton.classList.remove("selected");

            let scoreText = scoreField.textContent;

            let [username,score,created] = scoreText.split("|");

            let points = isDownvote ? isUndo ? 1 : -1 : isUndo ? -1 : 1;

            points = isOtherSelected ? points * 2 : points;

            score = ` ${parseInt(score) + points} points `;

            scoreField.textContent = `${username}|${score}|${created}`;
        }
    }

</script>