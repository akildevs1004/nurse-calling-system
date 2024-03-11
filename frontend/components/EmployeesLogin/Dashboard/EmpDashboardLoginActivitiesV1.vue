<template>
  <div>
    <v-row>
      <v-col md="10">
        <h4 class="pl-2">Web Login Activities</h4>
      </v-col>
      <v-col md="2" class="text-end">
        <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn dark-2 icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list width="120" dense>
            <v-list-item @click="viewLogs()">
              <v-list-item-title style="cursor: pointer">
                View Logs
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>

    <ComonPreloader icon="face-scan" v-if="loading" />

    <v-data-table
      dense
      :headers="headers"
      :items="logs.data"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [5, 50],
      }"
      :server-items-length="logs.total"
      hide-default-header
    >
      <template v-slot:item.employee.pic="{ item, index }">
        <v-row no-gutters>
          <v-col
            style="
              padding: 5px;
              padding-left: 0px;
              width: 30px;
              max-width: 30px;
            "
          >
            <v-img
              style="
                border-radius: 50%;
                height: auto;
                width: 30px;
                max-width: 30px;
              "
              :src="
                item.user.employee
                  ? item.user.employee.profile_picture
                  : '/no-profile-image.jpg'
              "
            >
            </v-img>
          </v-col>
        </v-row>
      </template>
      <template v-slot:item.employee.first_name="{ item, index }">
        {{ item.user.employee ? item.user.employee.first_name : "Admin" }}
        {{ item.user.employee ? item.user.employee.last_name : " " }}

        <div class="secondary-value">
          {{
            item.user.employee && item.user.employee.department
              ? caps(item.user.employee.department.name)
              : "---"
          }}
        </div>
      </template>

      <template v-slot:item.UserID="{ item }"> #{{ item.UserID }} </template>
      <template v-slot:item.employee.employee_id="{ item }">
        {{ item.employee && item.employee.employee_id }}
      </template>
      <template v-slot:item.LogTime="{ item }" style="color: green">
        <v-icon color="green" fill>mdi-clock-outline</v-icon>
        {{ item.date_time }}
      </template>

      <template v-slot:item.online="{ item }">
        <v-icon v-if="item.device.location" color="green" fill
          >mdi-map-marker-radius</v-icon
        >
        <v-icon v-else color="red" fill>mdi-map-marker-radius</v-icon>
      </template>
      <template v-slot:item.device.device_name="{ item }">
        <div :style="item.device.location ? 'color:green' : 'color: red;'">
          {{ item.device ? caps(item.device.name) : "---" }} <br />

          {{ item.device.location ? item.device.location : "---" }}
        </div>
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  props: ["branch_id"],
  data() {
    return {
      loading: false,
      items: [],
      emptyLogmessage: "",
      number_of_records: 5,
      logs: [],
      url: process.env.SOCKET_ENDPOINT,
      socket: null,
      totalRowsCount: 0,

      total: 0,
      options: {},
      headers: [
        {
          text: "Pic",
          align: "left",
          sortable: true,
          filterable: true,

          value: "employee.pic",
        },
        {
          text: "Employee Name",
          align: "left",
          sortable: true,
          filterable: true,

          value: "employee.first_name",
        },

        {
          text: "Time",
          align: "left",
          sortable: true,
          filterable: true,

          value: "LogTime", //edit purpose
        },
      ],
    };
  },
  watch: {
    branch_id() {
      this.loading = true;
      setTimeout(() => {
        this.$store.commit("dashboard/web_logins", null);
        this.getRecords();
        this.loading = false;
      }, 5000);
    },
  },
  created() {
    this.getRecords();
  },
  computed: {
    employees() {
      return this.$store.state.employeeList.map((e) => ({
        system_user_id: e.system_user_id,
        first_name: e.first_name,
        last_name: e.last_name,
        display_name: e.display_name,
      }));
    },
    devices() {
      if (this.$store.state.devices)
        return this.$store.state.devices.map((e) => e.device_id);
    },
  },
  methods: {
    viewLogs() {
      this.$router.push("/web_login_logs");
    },
    caps(str) {
      if (str == "" || str == null) {
        return "";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    getRecords() {
      if (this.$store.state.dashboard.web_logins) {
        this.logs = this.$store.state.dashboard.web_logins;
        return;
      }

      this.loading = true;

      this.$axios
        .get(`activity`, {
          params: {
            per_page: 5,
            company_id: this.$auth.user.company_id,
            user_id: this.$auth.user.id,
          },
        })
        .then(({ data }) => {
          this.logs = data;
          this.$store.commit("dashboard/web_logins", data);
          this.loading = false;
        });
    },
  },
};
</script>

<style>
.v-application--is-ltr .v-data-footer__pagination {
  margin: 0px;
}
</style>
