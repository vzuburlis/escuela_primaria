<?php
if (Form::posted('contact-form' . $widget_data->widget_id) && empty($_POST['subject']) && Event::get('recaptcha', true)) {
    $clog = new Logger('log/contact-form.log');
    $clog->log($_SERVER['HTTP_HOST'], $_SERVER['REMOTE_ADDR'], $_POST);
    preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $_POST['message'], $out, PREG_PATTERN_ORDER);
    if (count($out[0]) > 0) {
        View::alert('error', __('Links on text are not allowed'));
    } else {
        Email::send(["post" => ["name","email","message"]]);
        View::alert('success', htmlentities($widget_data->success_msg));
    }
}
$borderRadius = 'border-radius:' . ($data['border-radius'] ?? '0');
$justifyButton = $data['justify-button'] ?? 'left';
$buttonWidth = $data['button-width'] ?? 'auto';
?>

<div class="container lazy" data-container="*"
<?=($data['animation'] ? 'data-animation="' . $data['animation'] . ' 0.6s,fade-in 0.6s"' : '')?>>
<form method="post" action="<?=$_SERVER['REQUEST_URI']?>" class="g-form" style="margin:auto;max-width:700px">
  <?=Form::hiddenInput('contact-form' . $widget_data->widget_id)?>
  <?php View::alerts() ?>
  <input style="display:none" name="subject">
  <input name="name" placeholder="<?=__("Name")?>" class="form-control g-input" style="<?=$borderRadius?>;margin-top:8px;padding:1em;border:1px solid var(--main-primary-color)" required>
  <input name="email" placeholder="<?=__("E-mail")?>" class="form-control g-input" style="<?=$borderRadius?>;margin-top:8px;padding:1em;border:1px solid var(--main-primary-color)" type="email" required>
  <textarea name="message" placeholder="<?=__("Subject")?>" class="form-control g-input" style="<?=$borderRadius?>;margin-top:8px;padding:1em;border:1px solid var(--main-primary-color);resize: none;" required></textarea>
  <?php Event::fire('recaptcha.form')?>
  <div style="display:grid;justify-content:<?=$justifyButton?>">
    <button type="submit" class="btn g-btn btn-primary" style="<?=$borderRadius?>;font-size:120%;min-width:<?=$buttonWidth?>" value="Send">
    <?=__('Send')?>
    </button>
  </div>
</form>
</div>

