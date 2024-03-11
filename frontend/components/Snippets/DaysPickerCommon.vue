<template>
  <div>
    <div>{{ label }}</div>
    <v-checkbox
      dense
      style="float: left"
      class="mr-5"
      v-for="(week_day, index) in week_days"
      :key="index"
      v-model="selectedDays"
      @click="setWeekdays"
      :label="week_day.label"
      :value="week_day.value"
      hide-details
    ></v-checkbox>
  </div>
</template>
<script>
export default {
  props: ["label", "default_value"],
  auth: false,
  layout: "login",
  data() {
    return {
      selectedDays: ["Mon"],
      week_days: [
        { label: "Mon", value: "Mon" },
        { label: "Tue", value: "Tue" },
        { label: "Wed", value: "Wed" },
        { label: "Thu", value: "Thu" },
        { label: "Fri", value: "Fri" },
        { label: "Sat", value: "Sat" },
        { label: "Sun", value: "Sun" },
      ],
    };
  },
  created() {
    this.selectedDays = this.default_value;
  },
  methods: {
    setWeekdays() {
      this.$emit("selectedDays", this.selectedDays);
    },
  },
};
</script>
