<div class="container mt-4"  style="min-height:400px">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <span>Calificaciones</span>
      </li>
    </ol>
  </nav>
  <hr>

<?=View::alerts()?>
<table class="table">
  <thead>
    <tr>
      <th>Nombre de estudiante</th>
      <th>Grado</th>
      <th>Ciclo escolar</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($cards as $card) : ?>
      <tr>
        <td><?php echo htmlspecialchars($card['username']); ?></td>
        <td><?php echo htmlspecialchars($card['grade_level']); ?></td>
        <td><?php echo htmlspecialchars($card['academic_year']); ?></td>
        <td><a class="btn btn-sm btn-outline-primary" href="<?=$card['href']?>">Ver calificaciones</a><td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
