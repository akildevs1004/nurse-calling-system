<template>
  <div style="padding: 0px; width: 100%">
    <div id="pie2"></div>
  </div>
</template>

<script>
export default {
  props: ["items"],
  data() {
    return {
      options: {
        title: {
          text: "Devices Status",
          align: "left",
          margin: 0,
        },
        colors: ["#009d00", "#ff0000"],

        series: [],
        chart: {
          toolbar: {
            show: false,
          },
          width: 253, //200 //275
          type: "donut",
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
          show: true,
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
    this.options.labels = this.items.map((e) => e.title);
    this.options.series = this.items.map((e) => e.value);
    new ApexCharts(document.querySelector("#pie2"), this.options).render();
  },
  methods: {},
};
</script>

<style scoped>
/* .apexcharts-legend-series {
  margin: 0px 100px 2px 0px !important;
} */
/* #pie .apexcharts-legend-series {
  margin: 0px 50px 2px 0px !important;
} */

/* foreignObject {
  max-width: 280px !important;
} */
#pie .apexcharts-legend-series {
  margin: 0px 50px 2px 0px !important;
}
</style>
