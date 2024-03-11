<template>
  <v-select
    :label="label"
    @change="setWeekend"
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
      weekend: `Not Applicable`,
      items: [
        { id: `Not Applicable`, name: `Not Applicable` },
        { id: `Flexi`, name: `Flexible` },
        //{ id: `Half Day`, name: `Half Day` },
        { id: `Monday`, name: `Monday` },
        { id: `Tuesday`, name: `Tuesday` },
        { id: `Wednesday`, name: `Wednesday` },
        { id: `Thursday`, name: `Thursday` },
        { id: `Friday`, name: `Friday` },
        { id: `Saturday`, name: `Saturday` },
        { id: `Sunday`, name: `Sunday` },
      ],
    };
  },
  created() {
    this.weekend = this.default_value;
    this.$emit("selectedWeekend", this.default_value);
  },
  watch: {
    weekend(value) {
      this.weekend = value;
    },
  },
  methods: {
    setWeekend() {
      this.$emit("selectedWeekend", this.weekend);
    },
  },
};
</script>
