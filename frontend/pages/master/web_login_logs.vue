<template>
  <div v-if="can('master')">
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
        <v-row>
          <v-col cols="2">
            <div style="width: 250px">
              <v-autocomplete
                @change="getRecords()"
                class="pt-10 px-2"
                v-model="filters['company_id']"
                :items="[{ id: ``, name: `All Companies` }, ...companiesList]"
                dense
                placeholder="All Companies"
                outlined
                item-value="id"
                item-text="name"
              >
              </v-autocomplete>
            </div>
          </v-col>
          <v-col cols="2">
            <div style="width: 250px">
              <v-select
                @change="getRecords()"
                class="pt-10 px-2"
                v-model="filters['user_type']"
                :items="UserTypesList"
                dense
                placeholder="All User Types"
                outlined
                item-value="id"
                item-text="name"
              >
              </v-select>
            </div>
          </v-col>
          <v-col cols="2" style="padding-top: 56px; padding-left: 38px">
            <Calender
              @filter-attr="filterAttr"
              :defaultFilterType="1"
              :height="'40px'"
              :default_date_from="from_date"
              :default_date_to="to_date"
            />
          </v-col>
        </v-row>
      </v-toolbar>
      <v-data-table
        class="pt-5"
        dense
        :headers="headers"
        :items="logs"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [20, 50, 100],
        }"
        :server-items-length="totalRowsCount"
      >
        <template v-slot:item.sno="{ item, index }">
          {{
            currentPage
              ? (currentPage - 1) * perPage +
                (cumulativeIndex + logs.indexOf(item))
              : ""
          }}
        </template>
        <template v-slot:item.employee.pic="{ item, index }">
          <v-row no-gutters style="vertical-align: middle">
            <v-col
              style="
                padding-left: 0px;
                width: 50px;
                max-width: 50px;
                vertical-align: middle;
              "
            >
              <v-img
                v-if="item.model_type == 'employee'"
                style="border-radius: 50%; height: auto; width: 100%"
                :src="
                  item.user.employee
                    ? item.user.employee.profile_picture
                    : '/no-profile-image.jpg'
                "
              >
              </v-img>
              <v-img
                v-else-if="item.model_type == 'company'"
                style="
                  height: auto;

                  width: 100%;
                  margin: 0 auto;
                "
                :src="item.company ? item.company.logo : '/no-image.PNG'"
              >
              </v-img>
              <v-img
                v-else
                style="
                  height: auto;

                  width: 100%;
                  margin: 0 auto;
                "
                src="/no-image.PNG"
              >
              </v-img>
            </v-col>
          </v-row>
        </template>
        <template v-slot:item.company="{ item }">
          {{ item.company ? item.company.name : "---" }}
        </template>
        <template v-slot:item.user_type="{ item }">
          {{ item.user.user_type == null ? "MASTER" : item.user.user_type }}
        </template>

        <template v-slot:item.branch.name="{ item }">
          {{ (item.branch && item.name) || "---" }}
        </template>
        <template v-slot:item.employee.first_name="{ item, index }">
          <div v-if="item.user.employee">
            {{ item.user.employee.first_name }}
            {{ item.user.employee.last_name }}
            <div class="secondary-value">
              {{
                item.user.employee && item.user.employee.department
                  ? caps(item.user.employee.department.name)
                  : "---"
              }}
            </div>
          </div>
          <div v-else>
            {{ item.user.user_type == "master" ? "MASTER" : "ADMIN" }}
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
  layout: "master",
  data() {
    return {
      filterCompanyId: "",
      loading: false,
      items: [],
      emptyLogmessage: "",
      number_of_records: 5,
      logs: [],
      url: process.env.SOCKET_ENDPOINT,
      socket: null,
      totalRowsCount: 0,
      filters: [],
      total: 0,
      options: {},
      filterUserType: "",
      from_date: "",
      to_date: "",
      pagination: {
        current: 1,
        total: 0,
        per_page: 10,
      },
      loading: false,
      cumulativeIndex: 1,
      perPage: 10,
      currentPage: 1,
      totalRowsCount: 0,
      options: { perPage: 10 },
      UserTypesList: [
        { id: "", name: "All Logs" },

        ,
        { id: "company", name: "  Admin/Company Logs" },
        { id: "employee", name: "Employee Logs" },
        { id: "master", name: "Master Logs" },
      ],
      headers: [
        {
          text: "#",
          align: "left",
          sortable: false,
          filterable: true,

          value: "sno",
        },
        {
          text: "Logo",
          align: "left",
          sortable: false,
          filterable: true,

          value: "employee.pic",
        },

        {
          text: "Company ",
          align: "left",
          sortable: false,
          filterable: true,

          value: "company",
        },
        {
          text: "User Type",
          align: "left",
          sortable: false,
          filterable: true,

          value: "user_type",
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
      companiesList: [],
      isCompany: true,
      options: {},
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
    const today = new Date();

    this.from_date = today.toISOString().slice(0, 10);
    this.to_date = today.toISOString().slice(0, 10);
    this.filters["from_date"] = this.from_date;
    this.filters["to_date"] = this.to_date;
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
        value: "user.employee.branch.name",
        width: "300px",
        filterable: true,
        filterSpecial: true,
      },
    ];
    this.headers.splice(6, 0, ...branch_header);

    this.$axios.get(`company/list`).then(({ data }) => {
      this.companiesList = data;
    });
  },

  methods: {
    filterAttr(data) {
      if (data != null) {
        this.from_date = data.from;
        this.to_date = data.to;
      } else {
        this.from_date = null;
        this.to_date = null;
      }

      this.filters["from_date"] = this.from_date;
      this.filters["to_date"] = this.to_date;
      this.getRecords();
    },
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
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;
      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          pagination: true,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };
      this.currentPage = page;

      this.$axios.get(`activity`, options).then(({ data }) => {
        this.totalRowsCount = data.total;
        this.logs = data.data;
        // this.loading = false;
      });
    },
  },
};
</script>
