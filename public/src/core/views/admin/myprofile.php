<style>
.device-pill{background: var(--main-a-color); border-radius:6px; padding:8px; display:inline-block;margin:8px;opacity:0.8}
.device-pill .close {cursor:pointer}
.device-pill.selected {opacity:1}
#main-wrapper>div{background: inherit !important;border:none}
#profile-photo>div{margin:auto}
</style>
<?=View::script('lib/vue/vue.min.js')?>
<?=View::script('core/admin/vue-components.js')?>
<?=View::script('core/admin/media.js')?>
<?=View::css('core/admin/vue-editor.css');?>
<?=View::script('core/admin/vue-editor.js');?>
<?php
$user = User::getById($user_id);
View::alerts();

if (Request::post('submit-btn') === 'submited') {
    if (isset($_POST['user_about'])) {
        DB::query("UPDATE user SET about=? WHERE id=?;", [$_POST['user_about'], $user_id]);
    }
}
?>

<div class="gm-grid container my-4">

<div>
  <div id="profile-forms" class="g-card bg-white" style="padding:16px;">
    <form method="post" action="" class="g-form row">
    <h2 class="text-align-center"><?=__('Personal Information')?></h2>
    <br>
    <div id="profile-photo" class="text-align-center">
      <input-upload-media name="gila_photo" size=120 thumb="assets/core/default-user.png" value="<?=$user_photo?>"/>
    </div>

    <div class="col-12">
    <label class="col-12"><?=__('Name')?></label>
    <input class="form-control" name="gila_username" value="<?=Session::key('user_name')?>">
    </div>

    <!--div class="col-sm-6">
    <label class="col-12"><?=__('Email')?></label>
    <input disabled value="<?=Session::key('user_email')?>">
    </div-->
<?php if (isset($twitter_account)) : ?>
    <div class="col-sm-6">
    <label class="col-12"><?=__('Twitter Account')?></label>
    <input class="form-control" name='meta[twitter_account]' value="<?=$twitter_account?>">
    </div>
<?php endif; ?>

  <div class="col-sm-6">
    <label class="col-12"><?=__("Language")?></label>
    <?=Form::input('user_language', ["type" => "select","options" => ['en' => __('English'),'es' => 'Español'],'label' => false], Config::lang()) ?>
    </div>

    <div class="col-12">
      <button type="submit" name="submit-btn" onclick="this.value='submited'"
      class="btn btn-primary"><?=__('Update Profile')?></button>
    </div>

    <?php
    $allPermissions = Profile::getAllPermissions();
    $userPermissions = Gila\Session::permissions();
    if (!empty($allPermissions) && !empty($userPermissions)) {
        echo '<br><div class=mt-3>' . __('Permissions') . '<ul>';
        foreach ($userPermissions as $per) {
            echo '<li>' . Config::tr($per, $allPermissions[$per]);
        }
        echo '</ul></div>';
    }
    ?>

    </form>
  </div>
</div>

<div>
  <?php Event::fire('myprofile.col2') ?>
  <div id="profile-forms" class="g-card bg-white" style="padding:16px;">
    <form method="post" action="<?=$action_url ?? 'admin/profile'?>" class="g-form">
    <h2 class="text-align-center"><?=__('Security')?></h2>
    <div>
    <label class="col-12"><?=__('Password')?></label>
    <input class="form-control" name="old_pass" type="password">
    </div>

    <div>
    <label class="col-12"><?=__('New Password')?></label>
    <input class="form-control" name="new_pass" type="password">
    </div>

    <div>
    <label class="col-12"><?=__('Confirm Password')?></label>
    <input class="form-control" name="new_pass2" type="password">
    </div>

    <div>
      <button type="submit" name="submit-btn" onclick="this.value='password'"
      class="btn btn-primary"><?=__('Change Password')?></button>
    </div>
    </form>

<?php if (Config::get('utk_level') && Session::level() >= Config::get('utk_level')) : ?>
    <form method="post" action="<?=$action_url ?? 'admin/profile'?>" class="">
    <div>
      <label class="col-12 g-label px-0"><?=__('Unique Token Key')?></label> 
      <div class="small"><?=__('This API key is unique and secret. Do not reveal it or share it.', ['es' => 'Esa llave es unica y privada. No la compartes.'])?></div>
      <div class="input-group">
        <input type="text" id="token_input" value="<?=$token?>" class="form-control" readonly>
        <span class="btn btn-secondary" onclick="token_input.select(); document.execCommand('copy');g.snackbar('Copied to clipboard')" title="Copy Link to Clipboard"><i class="fa fa-copy"></i></span>
      </div>
    </div>

    <br><div>
    <button type="submit" name="token" value="generate"
    class="btn btn-outline-secondary"><?=__('Generate Token Key')?></button>
    <button type="submit" name="token" value="delete"
    class="btn btn-outline-danger"><?=__('Delete Token Key')?></button>
    </div>

    </form>
<?php endif; ?>
  </div>
</div>

<script>
var profileForms = new Vue({
  el: '#profile-forms'
});
</script>

</div>
