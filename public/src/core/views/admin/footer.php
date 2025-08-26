</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<footer class="main-footer" style="font-size:80%">
  <?=View::renderFile('admin/footer-tag.php')?>
  <div class="float-right d-none d-sm-inline-block">
  </div>
</footer>

</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="assets/core/adminlte/js/jquery.min.js"></script>
<script src="assets/core/adminlte/js/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="assets/core/adminlte/js/bootstrap.bundle.min.js"></script>
<script src="assets/core/adminlte/js/jquery.overlayScrollbars.min.js"></script>
<script src="assets/core/adminlte/js/adminlte.js"></script>
<?=View::cssAsync('lib/font-awesome/css/font-awesome.min.css')?>
<?php if (FS_ACCESS== true) : ?>
<style>[class*=sidebar-dark-]{background-color:#02162B}</style>
<?php endif; ?>
<?=View::css('lib/vue/vue-select.css')?>
<?=View::scriptAsync('lib/vue/vue-select.js');?>
<?php Event::fire('admin-footer') ?>
</body>
</html>
