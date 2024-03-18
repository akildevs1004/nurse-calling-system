<template>
  <div style="padding: 0px; width: 100%; height: auto">
    <v-row class="pt-0 pb-0">
      <v-col cols="6" class="text-right pb-0"></v-col>
      <v-col cols="6" class="text-right pb-0">
        <v-select
          @change="applyFilter()"
          class="pt-0 px-2"
          v-model="filter1"
          :items="[
            { id: `categories`, name: `Categories` },
            { id: `devices`, name: `Devices` },
          ]"
          dense
          outlined
          item-value="id"
          item-text="name"
          label="Categories/Devices"
        >
        </v-select
      ></v-col>
    </v-row>

    <v-row class="pt-0 mt-0">
      <v-col cols="12" class="text-center pt-0">
        <div
          id="visitors"
          name="visitors"
          style="width: 300px; margin: 0 auto; text-align: center"
        ></div>
      </v-col>
    </v-row>

    <div
      v-if="categories.length == 0"
      style="
        padding: 0px;
        margin: auto;
        text-align: center;
        vertical-align: middle;
        height: auto;
        padding-top: 36%;
      "
    >
      No Data available
    </div>
    <div>
      <v-row class="bold" style="height: auto">
        <v-col cols="1">#</v-col>
        <v-col cols="6">Category</v-col>
        <v-col cols="5">Alarm Events count</v-col>
      </v-row>
      <div style="height: 160px; overflow-y: scroll; overflow-x: hidden">
        <v-row v-for="(category, index) in categories">
          <v-col cols="1">{{ index + 1 }}</v-col>
          <v-col cols="7"
            ><v-icon :color="options?.colors[index]">mdi mdi-square</v-icon
            >{{ category.category }}</v-col
          >
          <v-col cols="3" class="text-center">{{ category.count }}</v-col>
        </v-row>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["height", "date_from", "date_to", "width"],
  data() {
    return {
      //   items: [
      //     { title: "Title1", value: 20 },
      //     { title: "Title2", value: 30 },
      //     { title: "Title3", value: 40 },
      //     { title: "Title4", value: 50 },
      //   ],
      totalCount: 0,
      filter1: "categories",
      categories: [],
      options: {
        noData: {
          text: "There's no data",
          align: "center",
          verticalAlign: "middle",
          offsetX: 0,
          offsetY: 0,
        },

        title: {
          //text: "Alarm Events - Categories",
          align: "left",
          margin: 0,
        },
        //colors: ["#033F9B", "#DC7633", "#02B64B", "#ff0000", "#808080", ""],
        colors: [
          "#033F9B",
          "#02B64B",
          "#ffb600",
          "#ff0000",
          "#808080",
          "#800080",
          "#00FFFF",
          "#FF00FF",
          "#FFFF00",
          "#008080",
          "#800000",
          "#033F9B",
          "#02B64B",
          "#ffb600",
          "#ff0000",
          "#808080",
          "#800080",
          "#00FFFF",
          "#FF00FF",
          "#FFFF00",
          "#008080",
          "#800000",
        ],

        series: [],
        chart: {
          toolbar: {
            show: false,
          },

          type: "donut",
        },
        customTotalValue: 0,
        plotOptions: {
          pie: {
            donut: {
              labels: {
                show: true,
                name: {
                  show: true,
                  fontSize: "22px",
                  fontFamily: "Rubik",
                  color: "#dfsda",
                  offsetY: -10,
                },
                value: {
                  show: true,
                  fontSize: "16px",
                  fontFamily: "Helvetica, Arial, sans-serif",
                  color: undefined,
                  offsetY: 16,
                  formatter: function (val) {
                    return val;
                  },
                },
                total: {
                  show: true,
                  label: "Total Alerts",
                  color: "#373d3f",
                  formatter: function (val) {
                    return val.config.customTotalValue;
                  },
                },
              },
            },
          },
        },
        labels: [],
        // plotOptions: {
        //   pie: {
        //     startAngle: -90,
        //     endAngle: 270,
        //   },
        // },
        dataLabels: {
          enabled: true,
          style: {
            fontSize: "10px",
          },
        },
        legend: {
          show: false,
          fontSize: "10px",
        },
        responsive: [
          {
            breakpoint: 480,
            options: {
              chart: {
                toolbar: {
                  show: false,
                },
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
    this.loadCategoriesStatistics();
  },
  methods: {
    applyFilter() {
      if (this.filter1 == "categories") {
        this.loadCategoriesStatistics();
      } else if (this.filter1 == "devices") {
        this.loadDevicesStatistics();
      }
    },
    loadDevicesStatistics() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,

          date_from: this.date_from,
          date_to: this.date_to,
        },
      };

      this.$axios
        .get(`/alarm_dashboard_get_devices_data`, options)
        .then(({ data }) => {
          this.categories = data;
          this.renderChart1(data);
        });
    },
    loadCategoriesStatistics() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,

          date_from: this.date_from,
          date_to: this.date_to,
        },
      };

      this.$axios
        .get(`/alarm_dashboard_get_categories_data`, options)
        .then(({ data }) => {
          this.categories = data;
          this.renderChart1(data);
        });
    },
    renderChart1(data) {
      let counter = 0;
      let total = 0;
      data.forEach((item) => {
        if (item.count > 0) {
          this.options.labels[counter] = item.category;
          this.options.series[counter] = item.count;
          counter++;
          total = total + item.count;
        }
      });

      this.options.customTotalValue = total; //this.items.ExpectingCount;

      setTimeout(() => {
        try {
          new ApexCharts(
            document.querySelector("#visitors"),
            this.options
          ).render();
        } catch (error) {}
      }, 1000);
    },
  },
  created() {
    // try {
    //   this.items.forEach((element) => {
    //     this.totalCount += element.value;
    //   });
    //   this.options.labels = this.items.map((e) => e.title);
    //   this.options.series = this.items.map((e) => e.value);
    //   new ApexCharts(document.querySelector("#pie2"), this.options).render();
    // } catch (error) {}
  },
};
</script>
