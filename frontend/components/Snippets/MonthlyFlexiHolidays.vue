<template>
<v-select
      :label="label"
      @change="setMonthlyHolidays"
      v-model="weekend"
      :items="items"
      item-value="id"
      item-text="name"
      dense
      outlined
      :hide-details="true"
    ></v-select>
</template>
<script>
export default {
  props: ["label", "default_value"],
  auth: false,
  layout: "login",
  data() {
    return {
      weekend: 0,
      items: [
        { id: 0, name: `Not Applicable` },
        { id: 1, name: `1 Day` },
        { id: 2, name: `2 Days` },
        { id: 3, name: `3 days` },
        { id: 4, name: `4 days` },
        { id: 5, name: `5 days` },
        { id: 6, name: `6 days` },
        { id: 7, name: `7 days` },
        { id: 8, name: `8 days` },
        { id: 9, name: `9 days` },
        { id: 10, name: `10 days` },
      ],
    };
  },
  created() {
    this.weekend = parseInt(this.default_value);
  },
  methods: {
    setMonthlyHolidays() {
      this.$emit("selectedMonthlyHolidays", this.weekend);
    },
  },
};
</script>
