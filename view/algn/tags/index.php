<?php
    $tags = isset($tags) ? $tags : [];
    $req = $this->di->get("request");

?>

<form action="tags" method="GET">
    <label for="tags">Tags</label>
    <input type="text" name="tags" id="tags" value="<?= $req->getGet("tags") ?>">
    <input type="submit" value="Search">
</form>

<div class="tags-container">
    <?php foreach($tags as $tag): ?>
        <a class="tag-wrapper" href="posts?tags=<?= $tag ?>">
            <span class="tag"><?= $tag ?></span>
        </a>
    <?php endforeach; ?>
</div>