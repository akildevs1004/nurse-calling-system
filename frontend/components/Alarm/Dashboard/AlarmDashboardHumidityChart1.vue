<template>
  <div style="width: 100%; height: 193px">
    <!-- <ComonPreloader
      icon="face-scan"
      v-if="loading"
      style="max-height: 180px"
      height="150px"
    /> -->
    <div v-if="loading" style="height: 180px; margin: auto; padding-top: 20%">
      Loading....
    </div>
    <VueGauge
      v-if="!loading"
      :options="options"
      :refid="name"
      :id="name"
      style="height: 180px"
    />

    <span style="font-size: 14px">Updated at : {{ humidity_date_time }}</span>
  </div>
</template>

<script>
// import VueGauge from "vue-gauge";
export default {
  props: [
    "name",
    "humidity_latest",
    "height",
    "branch_id",
    "humidity_date_time",
  ],
  // components: { VueGauge },
  data() {
    return {
      filterDeviceId: null,
      devices: [],
      loading: true,
      display_title: "Recent 7 days Attendance",
      date_from: "",
      date_to: "",
      options: {
        needleValue: 0,
        centralLabel: "0",
        hasNeedle: true,
        arcDelimiters: [50, 75, 99],
        arcColors: ["#008450", "#EFB700", "#B81D13"],
        chartWidth: 400,
      },

      key: 50,
    };
  },
  watch: {},
  mounted() {
    setTimeout(() => {
      this.options = {
        needleValue: this.humidity_latest,
        centralLabel: this.humidity_latest + "%",
        hasNeedle: true,
        arcDelimiters: [50, 75, 99],
        arcColors: ["#008450", "#EFB700", "#B81D13"],
      };

      this.loading = false;
    }, 1000);
  },
  created() {},

  methods: {},
};
</script>
