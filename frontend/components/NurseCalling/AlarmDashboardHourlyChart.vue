<template>
  <div style="width: 100%; height: 100%">
    <v-row>
      <v-col md="5">
        <h4>{{ display_title }}</h4></v-col
      >

      <!-- <v-col md="4">
        <CustomFilter
          style="float: right; padding-top: 5px"
          @filter-attr="filterAttr"
          :default_date_from="date_from"
          :default_date_to="date_to"
          :defaultFilterType="1"
          :height="'40px'"
      /></v-col> -->

      <!-- <v-col md="3">
        <v-autocomplete
          dense
          outlined
          label="Door"
          @change="filterDevice"
          v-model="filterDeviceId"
          :items="[{ name: 'All Doors', device_id: null }, ...devices]"
          item-value="device_id"
          item-text="name"
        >
        </v-autocomplete>
      </v-col> -->
    </v-row>

    <div
      :id="name"
      style="width: 100%; height: 400px"
      :key="display_title"
    ></div>
  </div>
</template>

<script>
// import VueApexCharts from 'vue-apexcharts'
export default {
  props: ["height", "branch_id", "date_from", "date_to"],
  data() {
    return {
      name: "apexDashboardHour",
      filterDeviceId: null,
      devices: [],
      loading: false,
      display_title: "Alarm Events",

      series: [
        {
          name: "Alarm",
          data: [],
        },

        {
          name: "Battery",
          data: [],
        },
      ],
      chartOptions1: {
        series: [
          {
            name: "Alarm",
            data: [],
          },

          {
            name: "Battery",
            data: [],
          },
        ],
        colors: ["#fe0000", "#14B012"],
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
      chartOptions2: {
        series: [
          {
            name: "Alarm",
            data: [],
          },

          {
            name: "Battery",
            data: [],
          },
        ],
        colors: ["#fe0000", "#14B012"],
        chart: {
          type: "bar",
          width: "98%",
          toolbar: {
            show: false,
          },
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
      this.$store.commit("CommDashboard/setDashboardData", null);
      //this.$store.commit("setDashboardData", null);
      await this.getDataFromApi();
    },
  },
  mounted() {
    this.chartOptions1.chart.height = this.height;
    this.chartOptions1.series = this.series;
    // new ApexCharts(
    //   document.querySelector("#" + this.name),
    //   this.chartOptions
    // ).render();
  },
  async created() {
    // // Get today's date
    // let today = new Date();

    // // Subtract 7 days from today
    // let sevenDaysAgo = new Date(today);
    // sevenDaysAgo.setDate(today.getDate() - 0);

    // // Format the dates (optional)
    // this.date_to = today.toISOString().split("T")[0];
    // this.date_from = sevenDaysAgo.toISOString().split("T")[0];
    // // this.display_title =
    // //   "Attendance : " + this.date_from + " to " + this.date_to;

    await this.getDataFromApi();
    this.getDeviceList();
  },

  methods: {
    getDeviceList() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`/device_list`, options).then(({ data }) => {
        this.devices = data;
      });
    },
    filterDevice() {
      this.$store.commit("CommDashboard/setDashboardData", null);
      this.$store.commit("CommDashboard/every_hour_count", null);

      this.getDataFromApi();
    },
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;

      this.filterType = "Monthly"; // data.type;
      if (this.date_from != this.date_to)
        this.display_title =
          "Access  : " + this.date_from + " to " + this.date_to;
      else this.display_title = "Access  : " + this.date_from;

      this.$store.commit("CommDashboard/setDashboardData", null);
      this.$store.commit("CommDashboard/every_hour_count", null);
      this.getDataFromApi();
    },
    async getDataFromApi() {
      this.loading = true;

      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,

          date_from: this.date_from,
          date_to: this.date_to,
        },
      };
      if (this.date_from == this.date_to) {
        this.$axios
          .get(`/alarm_dashboard_get_hourly_data`, options)
          .then(({ data }) => {
            this.renderChart1(data.houry_data);
          });
      } else
        this.$axios
          .get(`/alarm_dashboard_get_monthly_data`, options)
          .then(({ data }) => {
            this.renderChart2(data);
          });
    },

    renderChart1(data) {
      let counter = 0;
      data.forEach((item) => {
        this.chartOptions1.series[0]["data"][counter] = parseInt(item.count);

        this.chartOptions1.series[1]["data"][counter] = parseInt(
          item.batteryCount
        );

        this.chartOptions1.xaxis.categories[counter] = item.hour;
        counter++;
      });
      try {
        new ApexCharts(
          document.querySelector("#" + this.name),
          this.chartOptions1
        ).render();
        this.loading = false;
      } catch (error) {}
    },
    renderChart2(data) {
      try {
        this.chartOptions2.chart.height = this.height;
        this.chartOptions2.series = this.series;

        let counter = 0;

        this.chartOptions2.series = [
          {
            name: "Alarm",
            data: [],
          },

          {
            name: "Battery",
            data: [],
          },
        ];

        this.chartOptions2.xaxis = {
          categories: [],
        };
        data.forEach((item) => {
          this.chartOptions2.series[0]["data"][counter] = parseInt(item.count);

          this.chartOptions2.series[1]["data"][counter] = parseInt(
            item.batteryCount
          );

          this.chartOptions2.xaxis.categories[counter] =
            this.$dateFormat.format2(item.date);

          counter++;
        });
        this.loading = false;

        new ApexCharts(
          document.querySelector("#" + this.name),
          this.chartOptions2
        ).render();
      } catch (error) {}
    },
  },
};
</script>
