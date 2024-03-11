<template>
  <table>
    <tr>
      <th></th>
      <td style="text-align: right;">
        <v-icon
          v-if="can(`employee_personal_edit_access`) && hideEditBtn !== true"
          @click="openEdit"
          small
          class="grey"
          style="border-radius: 50%; padding: 5px"
          color="secondary"
          >mdi-pencil</v-icon
        >
      </td>
    </tr>
    <tr v-for="(item, index) in data" :key="index">
      <th>{{ index }}</th>
      <td>
        {{ data[index] }}
      </td>
    </tr>
  </table>
</template>

<script>
export default {
  props: ["data", "hideEditBtn"],
  methods: {
    can(item) {
      return true;
    },
    openEdit() {
      this.$emit("open-edit");
    }
  }
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #fbfdff;
}
</style>
