<div class="container mt-4"  style="min-height:400px">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <span>Calificaciones</span>
      </li>
    </ol>
  </nav>
  <hr>
<form method="get" action="student_ogrades">
<h2>Inserta CURP de estudiante</h2>
<!--p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent lacinia lacinia enim, a malesuada urna convallis at. Etiam finibus, odio gravida fringilla luctus, urna diam pellentesque nunc, ut elementum tellus dolor non quam. Donec blandit varius hendrerit.</p-->
<?=View::alerts()?>
<input name=curp class="form-control my-3" style="max-width:300px">
<?php if (Config::get('academy.student_ogrades') == 2) : ?>
<h2>Código PIN</h2>
<input name=pin class="form-control my-3" type=password style="max-width:300px">
<?php endif; ?>
<button class="btn btn-lg btn-primary mt-1" >Enviar</button>
</form>
</div>
