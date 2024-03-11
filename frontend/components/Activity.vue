<template>
  <div v-if="data.length">
    <!-- <v-toolbar dense class="primary" dark>
      <div>Application Activity</div>
      <v-spacer />
      <v-tooltip left>
        <template v-slot:activator="{ on, attrs }">
          <v-icon dark v-bind="attrs" v-on="on">mdi-eye</v-icon>
        </template>
        <span>See All</span>
      </v-tooltip>
    </v-toolbar> -->
    <v-timeline align-top dense>
      <v-timeline-item
        v-for="(item, index) in data"
        :key="index"
        color="primary"
        small
      >
        <v-row>
          <v-col>
            <strong>{{ item.action }}</strong>
            <div class="text-caption">
              <strong class="primary--text">{{
                item.employee && item.employee.display_name
              }}</strong>
              {{ item.date_time }}
            </div>
          </v-col>
        </v-row>
      </v-timeline-item>
    </v-timeline>
  </div>
  <NoRecordFound v-else :data="data" />
</template>
<script>
export default {
  data: () => ({
    data: [],
  }),
  custom_options: {},

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    this.loading = true;

    let custom_options = {
      params: {
        per_page: 5,
        company_id: this.$auth.user.company_id,
      },
    };
    this.$axios.get(`activity`, custom_options).then(({ data }) => {
      this.data = data.data;
      this.loading = false;
    });
  },
};
</script>
