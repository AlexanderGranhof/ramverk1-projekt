<?php
    $parsedown = new Parsedown();
    $req = $this->di->get("request");

    $temp = $isOwnPost;
    $temp2 = $loggedIn;

    global $userCommentScores;
    global $isOwnPost;
    global $hasAnswer;
    global $loggedIn;
    global $currentUID;

    $currentUID = $this->di->get("session")->get("userid");

    $isOwnPost = $temp;
    $loggedIn = $temp2;

    $userCommentScores = [];

    foreach($userUpvoted as $row) {
        $userCommentScores[$row["comment_id"]] = $row["score"];
    }

    foreach($comments as $comment) {
        if ($comment["answer"] == 1) {
            $hasAnswer = true;
            break;
        }
    }
    
    $sorted = $req->getGet("sort");

    function time_elapsed_string_single($datetime, $full = false) {
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

    function createSingleComment($comment, $isChild=false, $child="") {
        global $userCommentScores;
        global $isOwnPost;
        global $hasAnswer;
        global $loggedIn;
        global $currentUID;
        // var_dump($comment);
        // echo "<br><br>";
        $parsedown = new Parsedown();

        $score = $userCommentScores[$comment["id"]] ?? 0;

        $deleted = $comment["deleted"];
        $id = $comment["id"];
        $arrowUp = $score == 1 ? "selected" : "";
        $arrowDown = $score == -1 ? "selected" : "";
        $username = $comment["username"];
        $created = time_elapsed_string_single($comment["created"]) ?? "";
        $content = $parsedown->text($comment["comment_text"]);
        $answer = $comment["answer"];

        $isChild = $isChild ? "child" : "";
        $answerClass = $answer ? "class='answer'" : "";

        $arrowsDisabled = !$loggedIn || $deleted ? "disabled" : "";

        $modImg = $comment["moderator"] ? "<img class='mod-badge small' src='../img/mod.png'>" : "";
           
        return "<div class='comment $isChild'>" .
            "<div class='vote' data-id='$id'>" .
                "<span class='arrow-up $arrowUp $arrowsDisabled'>▶</span>" .
                "<span class='arrow-down $arrowDown $arrowsDisabled'>▶</span>" .
            "</div>" .
            "<div data-id='$id' $answerClass>" .
                "<a class='username' href='../profile/$username'>$username$modImg | $score points | $created</a>" .
                "<div class='comment-text " . ($deleted ? "removed" : "") . "'>" . (!$deleted ? $content : '[REMOVED]') . "</div>" .
                "<div class='comment-extras'>" . 
                    (!$deleted ? "<span data-id='$id' class='reply-button'>reply</span>" : "") .
                    ($isOwnPost && !$hasAnswer ? "<span class='mark-as-answer'>mark as answer</span>" : "") .
                    ($currentUID == $comment["user_id"] && !$deleted ? "<span data-id='$id' class='delete-comment'>delete</span>" : "") .
                "</div>" . 
            "</div>" .
            $child .
        "</div>";
    }

    // Nothing to see from here and the next 100 lines.
    // Please have mercy on these lines if you read them.

    $finished = [];

    for($i = 0; $i < count($comments); $i++) {
        if (!isset($comments[$i]["comment_reply_id"])) {
            $finished[] = $comments[$i];
            continue;
        }

        for($j = 0; $j < count($comments); $j++) {
            if ($comments[$j]["id"] == $comments[$i]["comment_reply_id"]) {
                $comments[$j]["child"][] = $comments[$i];
            } else {
                if (isset($comments[$j]["child"])) {
                    $current = $comments[$j]["child"];
    
                    while ($current) {
                        for($x = 0; $x < count($comments[$j]["child"]); $x++) {
                            if ($comments[$j]["child"][$x]["id"] == $comments[$i]["comment_reply_id"]) {
                                $comments[$j]["child"][$x]["child"][] = $comments[$i]; 
                            }
                        }

                        if (!isset($current["child"])) {
                            break;
                        }
    
                        $current = $current["child"];
                    }
                }
            }
        }

        for($j = 0; $j < count($finished); $j++) {
            if ($finished[$j]["id"] == $comments[$i]["comment_reply_id"]) {
                $finished[$j]["child"][] = $comments[$i];
            } else {
                if (isset($finished[$j]["child"])) {
                    $current = $finished[$j]["child"];
    
                    while ($current) {
                        for($x = 0; $x < count($finished[$j]["child"]); $x++) {
                            if ($finished[$j]["child"][$x]["id"] == $comments[$i]["comment_reply_id"]) {
                                $finished[$j]["child"][$x]["child"][] = $comments[$i]; 
                            }
                        }

                        if (!isset($current["child"])) {
                            break;
                        }
    
                        $current = $current["child"];
                    }
                }
            }
        }
    }


    function makeComments($current, $isChild=false, $childHTML="") {

        if (isset($current["child"])) {
            foreach($current["child"] as $child) {
                $childHTML .= makeComments($child, true);
            }
        }

        return createSingleComment($current, $isChild, $childHTML);
    }

    $commentHTML = "";

    foreach($finished as $comment) {
        $commentHTML .= makeComments($comment);
    }


?>

<div class="reply-window hidden">
    <div class="reply-container">
        <div class="original">
            <span class="light" id="original-username"></span>
            <div id="original-content"></div>
        </div>
        <div class="reply-section">
            <textarea id="reply-content"></textarea>
            <button id="reply-button">Reply</button>
        </div>
    </div>
</div>

<div class="posts-container">
    <div class="post single">
        <div class="vote" data-id="<?= $post["id"] ?>">
            <span class="arrow-up <?= $postScore == 1 ? "selected" : "" ?> <?= !$loggedIn ? "disabled" : "" ?>">▶</span>
            <span class="arrow-down <?= $postScore == -1 ? "selected" : "" ?> <?= !$loggedIn ? "disabled" : "" ?>">▶</span>
        </div>
        <div>
            <a href="../profile/<?= $post["username"] ?>" class="username"><?= $post["username"] ?><?= $postUser["moderator"] ? "<img class='mod-badge medium' src='../img/mod.png'>" : "" ?> | <?= $post["score"] ?? 0 ?> points | <?= time_elapsed_string_single($post["created"]) ?> | <span class="post-rank rank-<?= $postRank ?>">Rank <?= $postRank ?></span></a>
            <h1 class="title"><?= $post["title"] ?></h1>
            <div class="tags">
                <?php foreach(explode(",", $post["tags"]) as $tag): ?>
                <a class="tag-wrapper" href="../posts?tags=<?= $tag ?>">
                    <span class="tag"><?= $tag ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="content">
                <?= $parsedown->text($post["content"]) ?>
            </div>
            <?php if ($isOwnPost || $user["moderator"]) : ?>
                <span data-id="<?= $post["id"] ?>" class="delete-post">delete</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="comments-container">
    <div class="write-comment-container">
        <p class="comment-count"><?= count($comments) ?> <?= "comment" . (count($comments) > 1 ? "s" : "") ?></p>
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
    
        <?= $commentHTML ?>
    </div>
</div>

<script>

    const deletePostBtn = document.querySelector(".delete-post");

    async function handleDeletePost() {
        const confirmDelete = confirm("Are you sure you want to delete this post?");

        if (!confirmDelete) return;

        const response = await fetch("posts", {
            method: "DELETE",
            body: JSON.stringify({
                id: parseInt(this.dataset.id)
            })
        });

        if (response.ok) {
            window.location.reload();
        }
    }

    deletePostBtn.addEventListener("click", handleDeletePost)
    

    const deleteBtns = document.querySelectorAll(".delete-comment");

    async function handleDeleteComment() {
        const container = this.parentElement.parentElement.parentElement;
        const id = parseInt(this.dataset.id);

        const answer = confirm("Are you sure you want to delete your comment?");

        if (!answer) return;

        const response = await fetch("comment", {
            method: "DELETE",
            body: JSON.stringify({ id })
        });

        if (response.ok) {
            container.remove();
        }
    }

    for(const btn of deleteBtns) {
        btn.addEventListener("click", handleDeleteComment);
    }

    const markAsAnswerButtons = document.querySelectorAll(".mark-as-answer");

    async function handleMarkAsAnswer() {
        const commentContainer = this.parentElement.parentElement;
        const cid = parseInt(commentContainer.dataset.id);

        const answer = confirm(`Are you sure you want to mark this comment as answer?`);

        if (!answer) {
            return;
        }

        const data = new FormData();

        data.append("cid", cid);

        await fetch("commentAnswer", {
            method: "POST",
            body: data
        });

        commentContainer.classList.add("answer");

        for(const btn of markAsAnswerButtons) {
            btn.remove();
        }
    }

    for(const btn of markAsAnswerButtons) {
        btn.addEventListener("click", handleMarkAsAnswer);
    }

    const replyButtons = document.querySelectorAll(".reply-button");

    document.querySelector(".reply-window").addEventListener("click", ({target}) => target.classList.add("hidden"))

    function handleReply() {
        const replyID = parseInt(this.dataset.id);
        const replyWindow = document.querySelector(".reply-window");
        const container = this.parentElement.parentElement;
        const replyUsername = document.getElementById("original-username");
        const replyOriginalContent = document.getElementById("original-content");
        const replyButton = document.getElementById("reply-button");

        replyButton.dataset.id = replyID;

        const textContainer = container.querySelector(".comment-text");
        const usernameText = container.querySelector(".username").textContent;

        replyUsername.textContent = usernameText;
        replyOriginalContent.innerHTML = textContainer.innerHTML;

        replyWindow.classList.remove("hidden");
    }

    for (const btn of replyButtons) {
        btn.addEventListener("click", handleReply);
    }

    const replyButton = document.getElementById("reply-button");

    async function sendReply() {
        const commentText = document.getElementById("reply-content").value;
        const replyCommentID = parseInt(this.dataset.id);
        const postID = parseInt(window.location.href.split("/").pop());

        const data = new FormData();

        data.append("id", postID);
        data.append("content", commentText);
        data.append("reply", replyCommentID);


        const response = await fetch("../posts/comment", {
            method: "POST",
            body: data
        });       

        window.location.reload();
    }

    replyButton.addEventListener("click", sendReply);
    
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

    const voteButtons = document.querySelectorAll(".comment .arrow-up:not(.disabled), .comment .arrow-down:not(.disabled)");


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

    const postVotes = document.querySelectorAll(".post.single .arrow-up:not(.disabled), .post.single .arrow-down:not(.disabled)");

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