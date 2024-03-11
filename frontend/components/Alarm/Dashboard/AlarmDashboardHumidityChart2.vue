<template>
  <div style="width: 100%">
    <v-menu
      style="z-index: 9999"
      v-model="from_menu"
      :close-on-content-click="false"
      :nudge-left="0"
      transition="scale-transition"
      offset-y
      min-width="auto"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-text-field
          style="
            width: 230px;
            float: right;
            z-index: 9999;
            height: 5px;
            padding-top: 7px;
          "
          outlined
          v-model="from_date"
          v-bind="attrs"
          v-on="on"
          dense
          class="custom-text-box shadow-none"
          label="Date Filter"
        ></v-text-field>
      </template>
      <v-date-picker
        no-title
        scrollable
        v-model="from_date"
        @input="from_menu = false"
        @change="getDataFromApi()"
      ></v-date-picker>
    </v-menu>
    <div v-id="key > 1" :id="name" style="width: 100%; margin-top: -10px"></div>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  props: ["name", "height", "branch_id", "device_serial_number"],
  data() {
    return {
      key: 1,
      from_date: "",
      from_menu: false,
      series: [
        {
          name: "Humidity",
          type: "column",
          data: [],
        },
        {
          name: " ",
          type: "line",
          data: [],
        },
      ],
      chartOptions: {
        plotOptions: {
          bar: {
            columnWidth: "10%",
          },
        },
        colors: ["#00b0f0", "#FF8000"],
        chart: {
          height: 350,
          type: "line",
          toolbar: {
            show: false,
          },
        },
        stroke: {
          width: [0, 2],
          curve: "smooth",
        },
        title: {
          text: "Humidity Hourly Chart",
        },
        dataLabels: {
          enabled: true,
          enabledOnSeries: [1],
        },
        labels: [],
        xaxis: {
          type: "string",
        },
        yaxis: [
          {
            title: {
              text: "Humidity",
            },
          },
          {
            opposite: true,
            title: {
              text: "",
            },
          },
        ],
      },
    };
  },
  watch: {
    // branch_id() {
    //   try {
    //     this.$store.commit("AlarmDashboard/alarm_temparature_hourly", null);
    //     this.getDataFromApi();
    //   } catch (e) {}
    // },
  },

  created() {
    const today = new Date();

    this.from_date = today.toISOString().slice(0, 10);
  },
  mounted() {
    this.chartOptions.chart.height = this.height;
    this.chartOptions.series = this.series;
    /// setTimeout(() => {
    this.getDataFromApi();
    // }, 2000);
  },

  methods: {
    viewLogs() {
      this.$router.push("/attendance_report");
    },
    getDataFromApi() {
      if (!this.device_serial_number) return;
      // const data = await this.$store.dispatch("dashboard/every_hour_count");
      this.$axios
        .get("alarm_dashboard_get_humidity_hourly_data", {
          params: {
            company_id: this.$auth.user.company_id,
            branch_id: this.branch_id > 0 ? this.branch_id : null,
            device_serial_number: this.device_serial_number,
            from_date: this.from_date,
          },
        })
        .then(({ data }) => {
          this.data = data;
          this.loading = false;
          this.$store.commit("AlarmDashboard/alarm_humidity_hourly", data);

          this.temperature_hourly_data = data.houry_data;
          this.key = this.key + 1;

          this.renderChart(this.temperature_hourly_data);
        });
    },
    renderChart(data) {
      let counter = 0;

      data.forEach((item) => {
        this.chartOptions.series[0]["data"][counter] = item.count; //parseInt(item.count);

        this.chartOptions.series[1]["data"][counter] = item.count;

        this.chartOptions.labels[counter] = item.hour;
        counter++;
      });
      try {
        new ApexCharts(
          document.querySelector("#" + this.name),
          this.chartOptions
        ).render();
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
