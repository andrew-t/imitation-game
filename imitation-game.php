<?php
/*
 * Plugin Name:   The Imitation Game
 * Plugin URI:    http://www.github.com/andrew-t/imitation-game
 * Description:   A very simple Captcha plugin for you to modify and use.
 * Version:       0.1
 * Author:        Andrew Taylor
 * Author URI:    http://www.andrewt.net/
 *
 * You will probablt want to make your own challenges rather than rely on the ones here.
 */

function generateChallenge() {
    // TODO - add real challenges
    switch (rand(1, 2)) {
        case 1:
            $a = rand(1, 10);
            $b = rand(1, 10);
            return array(
                'question' => "What is $a + $b?",
                'answer' => $a + $b);
        case 2:
            $a = rand(5, 10);
            $b = rand(1, 5);
            return array(
                'question' => "What is $a - $b?",
                'answer' => $a - $b);
    }
}

function getTime() {
    return floor(time() / 3600);
}

function generateHash($time, $postId, $answer) {
    $salt = get_option('captcha-salt', 0);
    if ($salt === 0) {
        $salt = md5($time);
        update_option('captcha-salt', $salt);
    }
    return md5($time . $salt . $postId . $salt . $answer);
}

function answerIsRight() {
    if (is_user_logged_in()) return true;
    $time = getTime();
    for ($timeOffset = 0; $timeOffset < 3; ++$timeOffset)
        if ($_POST['comment_captcha_hash'] == generateHash($time - $timeOffset,
                                                           $_POST['comment_post_ID'],
                                                           $_POST['comment_captcha_code']))
            return true;
    return false;
}

function showCaptcha() {
    if (is_user_logged_in()) return;
    $captcha = generateChallenge();
    ?>
    <p class="comment-form-captcha">
        <label for="comment_captcha_code" class="small"><?php echo $captcha['question']; ?> *</label>
        <input type="text" name="comment_captcha_code" class="text" value="" />
    </p>
    <input type="hidden" name="comment_captcha_hash" value="<?php 
        echo generateHash(getTime(), get_the_ID(), $captcha['answer']);
    ?>" />
    <?php 
}

function checkCaptchaOnComment() {
    if (!answerIsRight())
        wp_die('Sorry, we could not prove you are a human to <i>p</i>&nbsp;&lt;&nbsp;0.05. Please try again.');
}

function checkCaptchaOnRegistration($errors) {
    if (!answerIsRight())
        $errors->add( 'failed_verification',
            'Sorry, we could not prove you are a human to <i>p</i>&nbsp;&lt;&nbsp;0.05. Please try again.');
    return $errors;
}

function registerSettings() {
    register_setting('captcha', 'captcha-salt');
}

add_action('register_form', 'showCaptcha');
add_action('registration_errors', 'checkCaptchaOnRegistration', 10, 3);
add_action('comment_form_after_fields', 'showCaptcha');
add_action('pre_comment_on_post', 'checkCaptchaOnComment');
add_action('admin_init', 'registerSettings');