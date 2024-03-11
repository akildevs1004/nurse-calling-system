<template>
  <div v-if="data.length" id="element"></div>
  <NoRecordFound v-else :data="data" />
</template>

<script>
export default {
  data() {
    return {
      data: [],
      chartOptions: {
        title: {
          align: "center",
          margin: 0,
        },
        colors: ["#23bdb8", "#f48665", "#289cf5", "#8e4cf1"],

        series: [],
        chart: {
          toolbar: {
            show: false,
          },
          width: 350, //200 //275
          type: "pie",
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
    this.loading = false;
    // let options = {
    //   company_id: this.$auth.user.company_id,
    //   employee_id: this.$auth.user.employee.system_user_id,
    // };
    let options = {
      company_id: this.$auth.user.company_id,
      employee_id: this.$auth.user.employee.system_user_id,
      department_id: this.$auth.user.employee.department_id,
      shift_type_id: this.$auth.user.employee.schedule.shift_type_id,
    };
    this.$axios
      .get(`employee-statistics`, { params: options })
      .then(async ({ data }) => {
        this.data = data;
        this.chartOptions.labels = await data.map((e) => e.title);
        this.chartOptions.series = await data.map((e) => e.value);
        try {
          new ApexCharts(
            document.querySelector("#element"),
            this.chartOptions
          ).render();
        } catch (error) {}
      });

    this.loading = false;
  },
  methods: {},
};
</script>
