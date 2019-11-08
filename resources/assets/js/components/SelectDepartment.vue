<template>
  <div>
      <select v-model="selectedDepartment" class="form-control">
          <option v-for="department in departments" :key="department.id" :value="department.id">{{department.name}}</option>
      </select>
  </div>
</template>

<script>
export default {
    name: 'select-department',
    computed: {
        loading() {
            return this.$store.state.loading.department
        },
        departments() {
            return this.$store.state.departments
        },
        selectedDepartment: {
            get: function() {
                return this.$store.state.selected_department_id
            },
            set: function(id) {
                this.$store.dispatch('setDepartment', id)
            },
        }
    }
}
</script>