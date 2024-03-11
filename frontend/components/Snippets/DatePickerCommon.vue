<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    :nudge-right="40"
    transition="scale-transition"
    offset-y
    min-width="auto"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
        :label="label"
        v-model="date"
        append-icon="mdi-calendar"
        readonly
        outlined
        v-bind="attrs"
        v-on="on"
        :hide-details="true"
        dense
      ></v-text-field>
    </template>
    <v-date-picker v-model="date" @input="setDate"></v-date-picker>
  </v-menu>
</template>
<script>
export default {
  props: ["label", "default_value"],
  data() {
    return {
      date: null,
      menu: false,
    };
  },
  created() {
    this.date = this.formatDate(this.default_value);
  },
  watch: {
    default_value(value) {
      this.date = this.formatDate(value);
    },
  },
  methods: {
    formatDate(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0"); // Month is 0-based
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    setDate() {
      this.menu = false;
      this.$emit("selectedDate", this.date);
    },
  },
};
</script>
