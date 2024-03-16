<template>
  <div v-if="can(`logs_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-row>
      <v-col>
        <v-card elevation="0" class="mt-2">
          <v-toolbar class="mb-2 white--text" color="white" dense flat>
            <v-toolbar-title>
              <span style="color: black"> Alarm Reports</span></v-toolbar-title
            >
            <span>
              <v-btn
                title="Reload"
                dense
                class="ma-0 px-0"
                x-small
                :ripple="false"
                @click="getDataFromApi()"
                text
              >
                <v-icon class="ml-2" dark>mdi mdi-reload</v-icon>
              </v-btn>
            </span>

            <v-spacer></v-spacer>
            <!-- <span>
              <v-select
                @change="getDataFromApi()"
                style="height: 30px; width: 230px; margin-right: 21px"
                label="Alarm Status"
                outlined
                dense
                small
                v-model="filter_alarm_status"
                item-text="name"
                item-value="value"
                :items="[
                  { name: `All`, value: null },
                  { name: `Alarm Detected`, value: 1 },
                  { name: `No Alarm `, value: 0 },

                  //{ name: `All Alarm - Normal`, value: 0 },
                ]"
                placeholder="Alarm Status"
              ></v-select>
            </span> -->
            <span>
              <v-autocomplete
                @change="getDataFromApi()"
                style="height: 30px; width: 180px; margin-right: 21px"
                label="Room/Device"
                outlined
                dense
                small
                v-model="filter_device_serial_number"
                item-text="name"
                item-value="serial_number"
                :items="[
                  { name: `All Rooms`, serial_number: null },
                  ...devices,
                ]"
                placeholder="Room/Device"
              ></v-autocomplete>
            </span>
            <span>
              <DateRangeComponent
                @filter-attr="handleDatesFilter"
                :defaultFilterType="1"
                :height="'40px'"
                style="margin-top: -7px; width: 100%"
              />
            </span>
          </v-toolbar>

          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>

          <v-data-table
            dense
            :headers="headers_table"
            :items="data"
            model-value="data.id"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [50, 100, 500, 1000],
            }"
            class="elevation-1"
            :server-items-length="totalRowsCount"
            fixed-header
            :height="tableHeight"
          >
            <template v-slot:item.sno="{ item, index }">
              {{
                currentPage
                  ? (currentPage - 1) * options.itemsPerPage +
                    (cumulativeIndex + data.indexOf(item))
                  : ""
              }}
            </template>

            <template v-slot:item.device_name="{ item }">
              {{ item.device.name }}
            </template>
            <template v-slot:item.category="{ item }">
              {{ item.device.category.name }}
            </template>
            <template v-slot:item.location="{ item }">
              {{ item.device.location }}
            </template>
            <template v-slot:item.response_minutes="{ item }">
              {{ $dateFormat.minutesToHHMM(item.response_minutes) }}
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>

<script>
import DateRangeComponent from "../../components/DateRangeComponent.vue";

export default {
  components: {
    DateRangeComponent,
  },
  data: () => ({
    cumulativeIndex: 1,
    filter_alarm_status: null,
    filter_device_serial_number: null,
    filter_from_date: null,
    filter_to_date: null,

    currentPage: 1,
    totalRowsCount: 0,
    branchesList: [],
    tableHeight: 700,
    id: "",
    from_menu_filter: "",
    from_date_filter: "",

    filters: {},
    isFilter: false,
    generateLogsDialog: false,
    totalRowsCount: 0,
    //server_datatable_totalItems: 10,
    datatable_search_textbox: "",
    datatable_searchById: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    departments: [],
    Model: "Log",
    endpoint: "alarm_reports",

    from_date: null,
    from_menu: false,
    to_date: null,
    to_menu: false,

    payload: {},

    loading: true,

    date: null,
    menu: false,

    loading: false,
    time_menu: false,

    log_payload: {
      user_id: 41,
      device_id: "OX-8862021010100",
      date: null,
      time: null,
    },

    ids: [],

    data: [],
    devices: [],
    total: 0,
    pagination: {
      current: 1,
      total: 0,
      itemsPerPage: 1000,
    },
    payloadOptions: {},
    options: {
      current: 1,
      total: 0,
      itemsPerPage: 100,
    },
    errors: [],
    response: "",
    snackbar: false,
    headers_table: [
      {
        text: "#",
        align: "left",
        sortable: false,
        key: "sno",
        value: "sno",
      },
      {
        text: "Room",
        align: "left",
        sortable: false,
        key: "device_name",
        value: "device_name",

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Location",
        align: "left",
        sortable: false,
        key: "location",
        value: "location",

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Category",
        align: "center",
        sortable: true,
        key: "category",
        value: "category",

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Start Date",
        align: "center",
        sortable: true,
        key: "alarm_start_datetime",
        value: "alarm_start_datetime",

        filterable: true,
        filterSpecial: false,
      },

      {
        text: "End Date",
        align: "center",
        sortable: true,
        key: "alarm_end_datetime",
        value: "alarm_end_datetime",

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Response Time",
        align: "center",
        sortable: true,
        key: "response_minutes",
        value: "response_minutes",

        filterable: true,
        filterSpecial: false,
      },
    ],
    filterApplied: true,
  }),

  mounted() {
    this.tableHeight = window.innerHeight - 270;
    window.addEventListener("resize", () => {
      this.tableHeight = window.innerHeight - 270;
    });

    setInterval(() => {
      if (this.$route.name == "alarm-temperaturelogs") {
        this.getDataFromApi();
      }
    }, 1000 * 60 * 2);
  },
  created() {
    if (this.$auth.user.branch_id == null) {
      let branch_header = [
        {
          text: "Branch",
          align: "left",
          sortable: true,
          key: "branch_id", //sorting
          value: "employee.branch.branch_name", //edit purpose
          width: "300px",
          filterable: true,
          filterSpecial: true,
        },
      ];
      this.headers_table.splice(1, 0, ...branch_header);
    }
    this.firstLoad();
    // this.getDepartments();
    // this.getbranchesList();
  },
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },

    filter_device_serial_number: {
      handler() {
        this.filterApplied = true;
      },
      deep: true,
    },
    filter_from_date: {
      handler() {
        this.filterApplied = true;
      },
      deep: true,
    },
    filter_to_date: {
      handler() {
        this.filterApplied = true;
      },
      deep: true,
    },
    filter_alarm_status: {
      handler() {
        this.filterApplied = true;
      },
      deep: true,
    },
  },
  methods: {
    getPriorityColor(status) {
      if (status == 1) {
        return "color:red";
      } else return "color:#DDD";
    },
    getbranchesList() {
      this.payloadOptions = {
        params: {
          company_id: this.$auth.user.company_id,

          branch_id: this.$auth.user.branch_id,
        },
      };

      this.$axios.get(`branches_list`, this.payloadOptions).then(({ data }) => {
        this.branchesList = data;
      });
    },
    handleDatesFilter(dates) {
      //console.log(dates);
      //if (dates.length > 1)
      {
        this.filter_from_date = dates.from; // dates[0];
        this.filter_to_date = dates.to; // dates[1];

        this.getDataFromApi(this.endpoint, "dates", [dates.from, dates.to]);

        // this.payloadOptions.params["from_date"] = filter_value[0];
        // this.payloadOptions.params["to_date"] = filter_value[1];
      }
    },
    getDepartments() {
      let options = {
        params: {
          per_page: 10,
          company_id: this.$auth.user.company_id,
          //department_ids: this.$auth.user.assignedDepartments,
        },
      };
      this.$axios.get(`department-list`, options).then(({ data }) => {
        this.departments = data;
        this.departments.unshift({ name: "All Departments", id: "" });
      });
    },
    // applyFilter() {
    //   this.getDataFromApi();
    //   this.from_menu_filter = false;
    //   this.to_menu_filter = false;
    // },
    applyFilters() {
      this.getDataFromApi();
      this.from_menu_filter = false;
      this.to_menu_filter = false;
    },
    toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    firstLoad() {
      this.loading = true;

      this.payload.from_date = this.getDate();
      this.payload.to_date = this.getDate();
      this.payload.from_date_txt = this.getDate();
      this.payload.to_date_txt = this.getDate();
      this.getDeviceList();
      this.getDataFromApi();
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    getDeviceList() {
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`/device_list_not_manual`, payload).then(({ data }) => {
        this.devices = data;
      });
    },
    getDate() {
      const date = new Date();
      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, "0");
      const day = date.getDate().toString().padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (this.filterApplied) {
        this.currentPage = 1;
        this.options.page = 1;
        this.filterApplied = false;
      }
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      this.currentPage = page == undefined ? 1 : page;

      this.payloadOptions = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          device_serial_number: this.filter_device_serial_number,
          from_date: this.filter_from_date,
          to_date: this.filter_to_date,
          filter_alarm_status: this.filter_alarm_status,
        },
      };

      this.loading = true;
      this.$axios.get(url, this.payloadOptions).then(({ data }) => {
        this.currentPage = page == undefined ? 1 : page;

        this.data = data.data;
        this.total = data.total;
        this.loading = false;
        this.totalRowsCount = data.total;
      });
    },
  },
};
</script>
./alarmlogs.vue
