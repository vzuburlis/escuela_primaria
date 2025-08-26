<?php
$gtable1 = DB::table('email_template');
$gtable2 = DB::table('email_signature');
?>
<form role="form" class="form-horizontal row" data-table="<?=$table?>" data-id="<?=$id?>">
    <div class="form-group col-md-6">
      <label class="col-sm-12" for="inputTo"><span class="glyphicon glyphicon-user"></span><?=__('From', ['es' => 'De'])?></label>
      <div class="col-sm-12">
        <select class="form-control" id="from_email" name="from_email">
          <option :value="campaign.from_email"><?=Session::key('user_email')?></option>
          <?php foreach (Config::getArray('email-marketing.alt_emails') as $alt_email) : ?>
          <option value="<?=$alt_email?>"><?=$alt_email?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group col-md-6">
      <label class="col-sm-12" for="inputTo"><span class="glyphicon glyphicon-user"></span><?=__('To', ['es' => 'A'])?></label>
      <div class="col-sm-12"><input type="email" class="form-control" name="email" value="<?=$email?>"></div>
    </div>

    <div class="form-group col-md-6">
      <label class="col-sm-12" for="inputSubject"><?=__('Subject', ['es' => 'Sujeto'])?></label>
      <div class="col-sm-12"><input type="text" class="form-control" name="subject" id="formpopupsubject" value="<?=$subject?>"></div>
    </div>

<?php if ($gtable1 != null && $tmps = $gtable1->getRows()) : ?>
    <div class="form-group col-md-4">
      <label class="col-sm-2" for="inputSubject"></span><?=__('Template', ['es' => 'Plantilla'])?></label>
      <select onchange="selectEmailTemplate(this.value)" id=formpopuptmp class="form-control">
        <option data-subject="" value=''>-</option>
    <?php foreach ($tmps as $key => $tmp) : ?>
        <option data-subject="<?=htmlEntities($tmp['title'])?>" data-txt='<?=htmlEntities($tmp['message'])?>'
        value='<?=$key?>'><?=htmlEntities($tmp['title'])?></option>
    <?php endforeach; ?>
      </select>
    </div>
<?php endif; ?>

<?php if ($gtable2 != null && $tmps = $gtable2->getRows()) : ?>
    <div class="form-group col-md-2">
      <label class="col-sm-2"></span><?=__('Signature', ['es' => 'Firma'])?></label>
      <select name="signature_id" class="form-control">
        <option data-subject="" value='0'>-</option>
    <?php foreach ($tmps as $tmp) : ?>
        <option value='<?=$tmp['id']?>'><?=htmlEntities($tmp['title'])?></option>
    <?php endforeach; ?>
      </select>
    </div>
<?php endif; ?>

    <div class="form-group col-md-12">
      <label class="col-sm-12" for="inputBody"></span><?=__('Message', ['es' => 'Mensaje'])?></label>
      <div class="col-sm-12" style="height:300px">
        <textarea class="form-control tinymce" name="message" id="formpopupmessage" rows="6" value="<?=$message?>"></textarea>
      </div>
    </div>
</form>
