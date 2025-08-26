<div id="app">
  <h2>Student Grades</h2>

  <label for="student">Select Student:</label>
  <select v-model="selectedStudent" @change="fetchGrades">
    <option v-for="student in students" :value="student.id">{{ student.name }}</option>
  </select>

  <div v-if="grades.length > 0">
    <table>
      <thead>
        <tr>
          <th>Subject</th>
          <th v-for="period in periods">{{ period.name }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="subject in subjects">
          <td>{{ subject.title }}</td>
          <td v-for="period in periods">
            {{ getGrade(user.id, subject, period) || 'N/A' }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <p v-else>No grades available for this student.</p>
</div>

<script>
new Vue({
  el: '#app',
  data() {
    return {
      students: [], // List of students associated with the parent
      selectedStudent: null,
      grades: [], // Grades data for the selected student
    };
  },
  computed: {

    periods() {
      // Get unique periods (e.g., Q1, Q2) from grades data
      return [...new Set(this.grades.map(grade => grade.period))];
    }
  },
  mounted() {
    this.fetchStudents(); // Fetch students when the page loads
    this.fetchSubjects();
    this.fetchPeriods();
  },
  methods: {
    fetchStudents() {
      // Fetch the list of students associated with the parent
      axios.get('/api/get-students')
        .then(response => {
          this.students = response.data;
          // Automatically select the first student if available
          if (this.students.length > 0) {
            this.selectedStudent = this.students[0].id;
            this.fetchGrades();
          }
        })
        .catch(error => {
          console.error('Error fetching students:', error);
        });
    },
    fetchSubjects() {
      g.getJSON('/cm/list_rows/academic_subject', function(data) {
        app.subjects = data.items;
      });
    },
    fetchPeriods() {
      g.getJSON('/cm/list_rows/academic_period', function(data) {
        app.periods = data.items;
      });
    },
    fetchGrades() {
      // Fetch grades for the selected student
      axios.post('/api/get-student-grades', { studentId: this.selectedStudent })
        .then(response => {
          this.grades = response.data;
        })
        .catch(error => {
          console.error('Error fetching grades:', error);
        });
    },
    getGrade(user_id, subject_id, period_id) {
      return grades[user_id][subject_id][period_id] ?? null;
    }
  }
});
</script>
