<template>
  <div>
    <v-card style="height: 500px">
      <v-toolbar color="primary" dark flat>
        <v-toolbar-title>Attendance</v-toolbar-title>
      </v-toolbar>
      <v-list style="min-height: 430px">
        <div
          v-if="
            items[0].value == 0 &&
            items[1].value == 0 &&
            items[2].value == 0 &&
            items[3].value == 0
          "
          class="center-parent"
        >
          <div class="center-child">
            Attendance data is not available on Today
          </div>
        </div>
        <div v-else id=" pie"></div>
      </v-list>
    </v-card>
  </div>
</template>

<script>
export default {
  props: ["items"],
  data() {
    return {
      options: {
        title: {
          text: "DAILY ATTENDANCE REPORT",
          align: "left",
          margin: 50,
        },
        colors: ["#A24FDD", "#6DFCCA", "#E78956", "#3A95D9"],

        series: [],
        chart: {
          toolbar: {
            show: false,
          },
          type: "pie",
          width: 590,
          height: 450,
        },
        labels: [],
        dataLabels: {
          dropShadow: {
            blur: 3,
            opacity: 0.8,
          },
        },
        responsive: [
          {
            breakpoint: 1400,
            options: {
              chart: {
                toolbar: {
                  show: false,
                },
                width: 350,
                margin: 50,
                height: 4500,
              },
              legend: {
                position: "bottom",
              },
            },
          },
        ],
      },
    };
  },
  mounted() {
    this.options.labels = this.items.map((e) => e.title);
    this.options.series = this.items.map((e) => e.value);
    try {
      new ApexCharts(document.querySelector("#pie"), this.options).render();
    } catch (error) {}
  },
  methods: {},
};
</script>

<style scoped>
/* .apexcharts-legend-series {
  margin: 0px 100px 2px 0px !important;
} */
#pie .apexcharts-legend-series {
  margin: 0px 50px 2px 0px !important;
}
</style>
