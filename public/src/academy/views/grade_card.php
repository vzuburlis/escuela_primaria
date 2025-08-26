<div id="app" class="container  mt-4 container-sm">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="student_ogrades">Calificaciones</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Ciclo escolar {{ academic_year }}</li>
    </ol>
  </nav>
  <hr>

  <!--h3><?=__('Student Grades', ['es' => 'Calificaciones ciclo escolar'])?></h3-->
  <div class="mt-4 d-flex" style="justify-content:space-between">
    <h5>Nombre(s): <b>{{ student.username }}</b></h5>
    <h5>Nivel: <b>{{ grade_level }}</b></h5>
    <h5>Grupo: <b>{{ group }}</b></h5>
    <span class="d-none d-md-inline btn btn-outline-primary" @click="print(app)"><?=__('Print', ['es' => 'Imprimir'])?></span>
  </div>
  <div class="bordered rounded-2 my-4">
  <table class="table my-0 bordered rounded-2">
    <thead>
      <tr class="bg-warning">
        <th></th>
        <th v-for="period in periods" style="text-align:center">
          <span class="d-none d-sm-inline">{{ period.name }}</span>
          <span class="d-sm-none">P. {{ period.id }}</span>
        </th>
        <th style="text-align:center">Promedio</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="subject in subjects">
        <td>{{ subject.title }}</td>
        <td v-for="period in periods" style="text-align:center">
          {{ getGrade(subject.id, period.id) || '--' }}
        </td>
        <td style="text-align:center">{{ getAverage(subject.id) }}</td>
      </tr>
    </tbody>
  </table>
  </div>

  <div class="bordered rounded-2 my-4">
  <table class="table my-0">
    <thead>
      <tr class="bg-warning">
        <th colspan=2>Observaciones:</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="period in periods">
        <td>{{ period.name }}</td>
        <td style="width:75%">
          {{ getNotes(period.id) || '' }}
        </td>
      </tr>
    </tbody>
  </table>
  </div>

</div>

<?php
Gila\View::script('lib/vue/vue.min.js');
Gila\View::script('core/gila.js');
?>
<script>
app = new Vue({
  el: '#app',
  data() {
    return {
      student: <?=json_encode($student)?>,
      grade_level: <?=$grade_level?>,
      group: '<?=$group['usergroup'] ?? '?'?>',
      academic_year: '<?=DB::value("SELECT a.name FROM academic_year a,usergroup WHERE usergroup.id-? AND a.id=academic_year_id", [$group['id']]) ?? 'N/A'?>',
      periods: <?=json_encode(get_academic_periods(DB::value("SELECT academic_year_id FROM usergroup WHERE id=?", [$group['id']])))?>,
      subjects:  <?=json_encode(DB::getAssoc("SELECT id,`title` FROM academic_subject WHERE grade_level=$grade_level"))?>,
      list_grades: <?=json_encode(DB::getAssoc("SELECT * FROM academic_grade WHERE user_id=? AND grade_level=?", [$student['id'], $grade_level]))?>,
      grades: {},
      list_comments: <?=json_encode(DB::getAssoc("SELECT * FROM academic_comments WHERE user_id=? AND grade_level=?", [$student['id'], $grade_level]))?>,
      comments: {},
    };
  },
  methods: {
    fetchGrades: function() {
      for(i in this.subjects) {
        this.grades[this.subjects[i].id] = {}
      }
      items = this.list_grades
      for(i in items) {
        if (items[i].grade==null) continue
        if (typeof this.grades[items[i].subject_id]=='undefined') continue
        this.grades[items[i].subject_id][items[i].period_id] = items[i].grade
      }
      for(i in this.list_comments) {
        this.comments[this.list_comments[i].period_id] = this.list_comments[i].comments
      }
      app.$forceUpdate()
    },
    getAverage(subject_id) {
      //return '--'
      total = 0.0
      avg = this.getGrade(subject_id, 0)
      if (avg!=null) return avg
      for(i in this.periods) if (this.periods[i].id>0) {
        g = this.getGrade(subject_id, this.periods[i].id)
        if (g==null) {
          return '--'
        }
        total += g
      }
      if (this.periods.length==0) return '--'
      return parseFloat((total/this.periods.length).toFixed(1));
    },
    getGrade(subject_id, period_id) {
      if (typeof this.grades[subject_id]=='undefined' || typeof this.grades[subject_id][period_id]=='undefined') {
        return null
      }
      return this.grades[subject_id][period_id];
    },
    getNotes(period_id) {
      if (typeof this.comments=='undefined' || typeof this.comments[period_id]=='undefined') {
        return null
      }
      return this.comments[period_id];
    }
  }
});
app.fetchGrades();

</script>
