<style>
.gila-darkscreen{z-index:9999999999}
@media print{
  body,.content-wrapper,#app{margin:0!important;width:100%!important}
  footer{display:none}
}
</style>
<div id="app" class="w-100">
  <h3 class="d-none d-print-block"><?=Config::get('title')?></h3>
  <div class="d-flex bg-light gap-1 px-2"
  style="width:fill-content;position:sticky;z-index:1;top:55px;height:57px;justify-content:space-between;">
    <div class="d-flex align-items-center gap-1">
    <label><?=__('Academic Year', ['es' => 'Año academico'])?></label>
    <select class="form-select w-auto" v-model="selectedAcademicYear" @change="fetchGroups()">
      <option v-for="year in academicYears" :value="year.id">{{ year.name }}</option>
    </select>&nbsp;

    <label><?=__('Period', ['es' => 'Periodo'])?></label>
    <select class="form-select w-auto" v-model="selectedPeriod" @change="fetchStudents()">
      <option v-for="period in periods" :value="period.id">{{ period.name }}</option>
    </select>&nbsp;

    <label><?=__('Group', ['es' => 'Grupo'])?></label>
    <select class="form-select w-auto" v-model="selectedGroup" @change="fetchStudents()">
      <option v-for="group in groups" :value="group.id">{{ group.usergroup }}</option>
    </select>&nbsp;

    </div>

    <div class="d-flex align-items-center gap-1 d-print-none">
      <span class="d-none d-md-inline btn btn-outline-primary" @click="print(app)"><?=__('Print', ['es' => 'Imprimir'])?></span>&nbsp;
      <span class="btn btn-success float-right" @click="saveGrades" :disabled="!students.length"><?=__('Save changes', ['es' => 'Guardar cambios'])?></span>
    </div>
  </div>

  <div v-if="selectedPeriod==0&&students.length" class="alert alert-warning mt-2">Los promedios se calculan automaticamente en las boletas. Puede anular los valores desde aqui</div>
  <table class="table mt-2 p-2 rounded-2" v-if="students.length" style="position:relative;z-index:0;background:sandybrown;font-size:90%">
    <thead>
      <tr style="position:sticky;z-index:99;top:112px;height:58px;background:sandybrown">
        <!--th><?=__('Student Name', ['es' => 'Nombre de estudiante'])?></th-->
        <th></th>
        <!--th><?=__('Grade', ['es' => 'Grado'])?></th-->
        <th v-for="subject in subjects"><small>{{ subject.title }}</small></th>
        <th>Observaciones</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="student in students" :key="student.id">
        <td>{{ student.username }}</td>
        <td v-for="subject in subjects"><input type="number" v-model.number="student.grade[subject.id]" min="0" max="100" class="text-center border-0"></td>
        <td><textarea type="txt" v-model.number="student.comments"  class="border-0" style="width:fill-available;width:-webkit-fill-available;min-width:300px" rows=1 :disabled="selectedPeriod==0"></textarea></td>
      </tr>
    </tbody>
  </table>
  <div v-else class="alert alert-info">No hay estudiantes en ese grupo</div>

</div>

<script>
app = new Vue({
  el: '#app',
  data() {
    return {
      academicYears: [],
      groups: [],
      periods: [],
      subjects: [],
      students: [],
      selectedAcademicYear: null,
      selectedGroup: null,
      selectedPeriod: null,
      selectedSubject: null,
      isAdmin: <?=(Session::hasPrivilege('admin edit_students') ? 'true' : 'false')?>,
    };
  },
  mounted() {
    this.fetchAcademicYears();
  },
  methods: {
    fetchAcademicYears() {
      g.getJSON('/cm/list_rows/academic_year', function(data) {
        app.academicYears = data.items;
        app.selectedAcademicYear = data.items[0].id
        app.fetchGroups()
      });
    },
    fetchGroups() {
      periods = 3
      for(i=0;i<this.academicYears.length;i++) if(this.academicYears[i].id==this.selectedAcademicYear) {
        periods = this.academicYears[i].periods
      }
      console.log('periods',periods)
      app.periods = []
      for(i=0;i<periods;i++) {
        app.periods.push({name:'<?=(__('Period', ['es' => 'Periodo']))?> '+(i+1), id:i+1})
      }
      app.periods.push({name:'<?=(__('Average', ['es' => 'Promedio']))?>', id:0})

      g.getJSON('/cm/list_rows/academic_group?academic_year_id='+this.selectedAcademicYear, function(data) {
        if (app.isAdmin==false) {
          app.groups = []
          for(i=0;i<data.items.length;i++) if (data.items[i].teacher_id==<?=Session::userId()?>) {
            app.groups.push(data.items[i])
          }
        } else {
          app.groups = data.items;
        }
        app.fetchStudents()
      });
    },
    fetchSubjects() {
      if (this.selectedGroup==null) return
      for(i in this.groups) if(this.groups[i].id==this.selectedGroup) {
        grade_level = this.groups[i].grade_level
      }
      g.getJSON('/cm/list_rows/academic_subject?grade_level='+grade_level, function(data) {
        app.subjects = data.items;
      });
    },
    fetchStudents() {
      this.fetchSubjects()
      g.getJSON('/academy/get_enter_grades/'+this.selectedGroup+'/'+this.selectedPeriod, function(data) {
        app.students = data.students;
        app.students.forEach(student => {
            if (typeof student.grade=='undefined') student.grade = [];
          });
      });
    },
    saveGrades() {
      if (this.selectedGroup==null) g.alert('Elige un grupo para editar', 'danger');
      if (this.students==[]) g.alert('Los datos son vacios', 'danger');
      g.postJSON('/academy/save_enter_grades/'+this.selectedGroup+'/'+this.selectedPeriod, {
        students: this.students,
      },
      function(response) {
        g.alert(g.tr('Grades saved successfully!',{es:'Calificaciones guardadas con éxito'}), 'success');
      },
      function(response) {
        g.alert(g.tr('Error saving grades',{es:'Error al guardar calificaciones'}), 'warning');
      });
    }
  }
});
</script>
