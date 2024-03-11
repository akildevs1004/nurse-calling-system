<template>
  <div style="width: 100%">
    <v-row>
      <v-col md="6">
        <h4>{{ display_title }}</h4></v-col
      >

      <v-col md="6">
        <CustomFilter
          style="float: right"
          @filter-attr="filterAttr"
          :default_date_from="date_from"
          :default_date_to="date_to"
          :defaultFilterType="1"
          :height="'35px '"
      /></v-col>
    </v-row>
    <ComonPreloader icon="face-scan" v-if="loading" />
    <div :id="name" style="width: 100%" :key="display_title"></div>
  </div>
</template>

<script>
export default {
  props: ["name", "height", "branch_id"],
  data() {
    return {
      loading: false,
      display_title: "Last 2 weeks  Attendance",
      date_from: "",
      date_to: "",
      series: [
        {
          name: "Present",
          data: [],
        },
        {
          name: "Absent",
          data: [],
        },
        {
          name: "Missing",
          data: [],
        },
        {
          name: "WeekOff",
          data: [],
        },
      ],

      chartOptions: {
        colors: ["#14B012", "#FF0000", "#FFB600", "#2196F3"],
        chart: {
          toolbar: {
            show: false,
          },
          type: "bar",
          width: "98%",
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "25%",
            endingShape: "rounded",
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          show: true,
          width: 2,
          colors: ["transparent"],
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          title: {
            text: " ",
          },
        },
        fill: {
          opacity: 1,
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val;
            },
          },
        },
      },
    };
  },
  watch: {
    async display_title() {
      await this.getDataFromApi();
    },
    async branch_id(val) {
      this.$store.commit("dashboard/setDashboardData", null);
      //this.$store.commit("setDashboardData", null);
      await this.getDataFromApi();
    },
  },
  async created() {
    // Get today's date
    let today = new Date();

    let monthObj = this.$dateFormat.monthStartEnd(today);

    // Subtract 7 days from today
    let sevenDaysAgo = new Date(today);
    sevenDaysAgo.setDate(today.getDate() - 15);

    // Format the dates (optional)
    this.date_to = today.toISOString().split("T")[0];
    this.date_from = sevenDaysAgo.toISOString().split("T")[0];

    // this.display_title =
    //   "Attendance : " + this.date_from + " to " + this.date_to;

    await this.getDataFromApi();
  },

  methods: {
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;

      this.filterType = "Monthly"; // data.type;

      this.display_title =
        "Attendance : " + this.date_from + " to " + this.date_to;

      this.$store.commit("dashboard/setDashboardData", null);
    },
    async getDataFromApi() {
      this.loading = true;

      this.$store.dispatch("dashboard/setDates", {
        date_from: this.date_from,
        date_to: this.date_to,
        branch_id: this.branch_id,
        system_user_id: this.$auth.user.employee.system_user_id,
      });

      this.renderChart(
        await this.$store.dispatch("dashboard/states_for_7_days")
      );
    },
    renderChart(data) {
      try {
        this.chartOptions.chart.height = this.height;
        this.chartOptions.series = this.series;

        let counter = 0;

        this.chartOptions.series = [
          {
            name: "Present",
            data: [],
          },
          {
            name: "Absent",
            data: [],
          },
          {
            name: "Missing",
            data: [],
          },
          {
            name: "WeekOff",
            data: [],
          },
        ];

        this.chartOptions.xaxis = {
          categories: [],
        };
        data.forEach((item) => {
          this.chartOptions.series[0]["data"][counter] = parseInt(
            item.presentCount
          );
          this.chartOptions.series[1]["data"][counter] = parseInt(
            item.absentCount
          );
          this.chartOptions.series[2]["data"][counter] = parseInt(
            item.missingCount
          );
          this.chartOptions.series[3]["data"][counter] = parseInt(
            item.offCount
          );
          this.chartOptions.xaxis.categories[counter] =
            this.$dateFormat.format2(item.date);

          counter++;
        });
        this.loading = false;
        try {
          new ApexCharts(
            document.querySelector("#" + this.name),
            this.chartOptions
          ).render();
        } catch (error) {}
      } catch (error) {}
    },
  },
};
</script>

<style>
.apexcharts-canvas {
  width: 100%;
}
</style>
