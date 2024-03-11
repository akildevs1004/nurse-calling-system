<template>
  <div v-if="can(`web_logs_access`)">
    <v-card class="mb-5 mt-2 rounded-md" elevation="0">
      <v-toolbar class="rounded-md" dense flat>
        <v-toolbar-title><span> Web user Logins</span></v-toolbar-title>
        <span>
          <v-btn
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            title="Reload"
          >
            <v-icon class="ml-2" @click="getRecords()" dark>mdi-reload</v-icon>
          </v-btn>
        </span>
        <div v-if="isCompany" style="width: 250px">
          <v-select
            @change="getRecords()"
            class="pt-10 px-2"
            v-model="branch_id"
            :items="[{ id: ``, branch_name: `Select All` }, ...branchesList]"
            dense
            placeholder="Select Branch"
            outlined
            item-value="id"
            item-text="branch_name"
          >
          </v-select>
        </div>
      </v-toolbar>
      <v-data-table
        class="pt-5"
        dense
        :headers="headers"
        :items="logs"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [10, 50, 100],
        }"
        :server-items-length="totalRowsCount"
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
        <template v-slot:item.branch.branch_name="{ item }">
          {{ (item.branch && item.branch_name) || "---" }}
        </template>
        <template v-slot:item.employee.first_name="{ item, index }">
          {{ item.user.employee ? item.user.employee.first_name : "Admin" }}
          {{ item.user.employee ? item.user.employee.last_name : " " }}

          <div>
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
          <v-icon color="green" fill>mdi-clock-outline</v-icon
          >{{ item.date_time }}
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
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
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
          sortable: false,
          filterable: true,

          value: "employee.pic",
        },
        {
          text: "Employee Name",
          align: "left",
          sortable: false,
          filterable: true,

          value: "employee.first_name",
        },
        {
          text: "Page",
          align: "left",
          sortable: false,
          filterable: true,

          value: "action",
        },
        {
          text: "User ",
          align: "left",
          sortable: false,
          filterable: true,

          value: "user.user_type",
        },

        {
          text: "Email",
          align: "left",
          sortable: false,
          filterable: true,

          value: "user.email",
        },

        {
          text: "Time",
          align: "left",
          sortable: false,
          filterable: true,

          value: "LogTime", //edit purpose
        },
      ],
      branch_id: null,
      branchesList: [],
      isCompany: true,
    };
  },
  watch: {
    options: {
      handler() {
        this.getRecords();
      },
      deep: true,
    },
  },
  async created() {
    if (this.$auth.user.branch_id) {
      this.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
      return;
    }

    const branch_header = [
      {
        text: "Branch",
        align: "left",
        sortable: true,
        key: "branch_id",
        value: "user.employee.branch.branch_name",
        width: "300px",
        filterable: true,
        filterSpecial: true,
      },
    ];
    this.headers.splice(1, 0, ...branch_header);

    try {
      const { data } = await this.$axios.get(`branches_list`, {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company_id,
        },
      });
      this.branchesList = data;
    } catch (error) {
      // Handle the error
      console.error("Error fetching branch list", error);
    }
  },

  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    caps(str) {
      if (str == "" || str == null) {
        return "";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    getRecords(filter_column = "", filter_value = "") {
      //let filter_value = this.datatable_search_textbox;
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      if (page == 1) this.loading = true;
      // if (this.filters) {
      //   page = 1;
      // }
      let itemsPerPage1 = itemsPerPage;
      if (!itemsPerPage1) itemsPerPage1 = 5;
      let options = {
        params: {
          branch_id: this.branch_id,
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage1,
          filter_column: filter_value,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };

      if (filter_column != "") options.params[filter_column] = filter_value;
      this.$axios.get(`activity`, options).then(({ data }) => {
        this.totalRowsCount = data.total;
        this.logs = data.data;
        this.loading = false;
      });
    },
  },
};
</script>
