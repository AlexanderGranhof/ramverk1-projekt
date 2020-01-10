<?php
    $session = $this->di->get("session");
    $userid = $session->get("userid");
?>


<?php if(!$userid): ?>
    <h1>You need to be logged in to create a post</h1>
    <?php else: ?>
    <div class="login-container">
    <h1>New post</h1>
        <form id="createPost" name="createPost" action="./new" method="POST">
            <div class="input-container">
                <label for="title">Title</label>
                <input required type="text" name="title" id="title">
            </div>
            <div class="input-container">
                <label for="tags">Tags (comma seperated)</label>
                <input type="text" name="tags" id="tags">
            </div>
            <div class="input-container textarea">
                <label for="title">Content (markdown)</label>
                <textarea required name="content" id="content" cols="30" rows="10" form="createPost"></textarea>
            </div>
            <input type="submit" value="Create Post">
        </form>
    </div>
<?php endif; ?>
