<?php

use Algn\Database\User;

$session = $di->get("session");

$error = $session->getOnce("error_login");
$usernameErr = $session->getOnce("register_error_username");

$userid = $session->get("userid");

$user = null;

if ($userid) {
    $userdb = new User();
    $user = $userdb->get($userid);

    $email = $user["email"];
    $size = 40;
    $grav_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "&s=" . $size;
}
?>

<?php if ($error) : ?>
<h4 class="pwd-usr-err">Username or password is incorrect</h4>
<?php endif; ?>
<?php if ($usernameErr) : ?>
<h4 class="pwd-usr-err">Username already exists</h4>
<?php endif; ?>
<?php if (!$userid) : ?>
    <div class="login-container">
        <h1>Login</h1>
        <form action="" method="POST">
            <div class="input-container">
                <label for="username">Username</label>
                <input type="text" name="username" id="username">
            </div>
            <div class="input-container">
                <label for="password">Password</label>
                <input type="password" name="password" id="password">
            </div>
            <input type="submit" value="Sign in">
        </form>
        <a class="sign-up" href="profile/register">Click here to sign up</a>
    </div>
<?php endif; ?>
