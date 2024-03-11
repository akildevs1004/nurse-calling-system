<template>
  <div v-if="can(`logs_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row justify="center">
      <v-dialog v-model="generateLogsDialog" max-width="700px">
        <v-card>
          <v-card-title dark class="popup_background">
            <span dense> Manual Log </span>
            <v-spacer></v-spacer>
            <v-icon dark @click="generateLogsDialog = false" outlined>
              mdi mdi-close-circle
            </v-icon>
          </v-card-title>
          <v-card-text>
            <GenerateLog
              @close-popup="generateLogsDialog = false"
              @update-data-table="getDataFromApi"
            />
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
    <v-row>
      <v-col>
        <v-card elevation="0" class="mt-2">
          <v-toolbar class="mb-2 white--text" color="white" dense flat>
            <v-toolbar-title>
              <span style="color: black">
                Attendances Logs</span
              ></v-toolbar-title
            >
            <!-- <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              title="Reload"
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              @click="getRecords"
              text
            >
              <v-icon class="ml-2" dark>mdi mdi-reload</v-icon>
            </v-btn>
            <!-- </template>
              <span>Reload</span>
            </v-tooltip> -->

            <!-- <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              title="Filter"
              x-small
              :ripple="false"
              text
              @click="toggleFilter"
            >
              <v-icon dark white>mdi-filter</v-icon>
            </v-btn>
            <!-- </template>
              <span>Filter</span>
            </v-tooltip> -->

            <v-spacer></v-spacer>
            <!-- <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              v-if="can(`logs_create`)"
              title="Attendance Log"
              x-small
              :ripple="false"
              text
              @click="generateLogsDialog = true"
            >
              <v-icon class="">mdi mdi-plus-circle</v-icon>
            </v-btn>
            <!-- </template>
              <span> Attendance Log</span>
            </v-tooltip> -->
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
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
            :server-items-length="totalRowsCount"
            fixed-header
            :height="tableHeight"
          >
            <template v-slot:header="{ props: { headers } }">
              <tr v-if="isFilter">
                <td v-for="header in headers" :key="header.text">
                  <v-container>
                    <v-text-field
                      clearable
                      :hide-details="true"
                      v-if="header.filterable && !header.filterSpecial"
                      v-model="filters[header.key]"
                      :id="header.value"
                      @input="applyFilters(header.key, $event)"
                      outlined
                      dense
                      autocomplete="off"
                    ></v-text-field>
                    <v-select
                      :id="header.key"
                      :hide-details="true"
                      v-if="
                        header.filterSpecial &&
                        header.value == 'employee.branch.branch_name'
                      "
                      clearable
                      outlined
                      dense
                      small
                      v-model="filters[header.key]"
                      item-text="branch_name"
                      item-value="id"
                      :items="[
                        { branch_name: `All Branches`, id: `` },
                        ...branchesList,
                      ]"
                      placeholder="Branch"
                      solo
                      flat
                      @change="applyFilters(header.key, id)"
                    ></v-select>
                    <v-select
                      :id="header.key"
                      :hide-details="true"
                      v-if="
                        header.filterSpecial &&
                        header.value == 'department.name.id'
                      "
                      clearable
                      outlined
                      dense
                      small
                      v-model="filters[header.key]"
                      item-text="name"
                      item-value="id"
                      :items="[
                        { name: `All Departments`, id: `` },
                        ...departments,
                      ]"
                      placeholder="Department"
                      solo
                      flat
                      @change="applyFilters(header.key, id)"
                    ></v-select>
                    <!-- <v-menu v-if="header.filterSpecial && header.value == 'LogTime'" ref="from_menu_filter"
                    v-model="from_menu_filter" :close-on-content-click="false" transition="scale-transition" offset-y
                    min-width="auto">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field :hide-details="!from_date_filter" outlined dense v-model="filters[header.value]"
                        readonly v-bind="attrs" v-on="on" placeholder="Select Date"></v-text-field>
                    </template>
                    <v-date-picker style="height: 350px" v-model="filters[header.value]" no-title scrollable
                      @input="applyFilters()">
                      <v-spacer></v-spacer>

                      <v-btn text color="primary"
                        @click="filters[header.value] = ''; from_menu_filter = false; applyFilters()">
                        Clear
                      </v-btn>
                    </v-date-picker>
                  </v-menu> -->
                    <DateRangePicker
                      :disabled="false"
                      :header="header"
                      column="date_range"
                      @selected-dates="handleDatesFilter"
                    />
                    <v-select
                      :id="header.key"
                      :hide-details="true"
                      v-if="
                        header.filterSpecial && header.value == 'device.name'
                      "
                      outlined
                      dense
                      small
                      v-model="filters[header.key]"
                      item-text="name"
                      item-value="device_id"
                      :items="[
                        { name: `All Devices`, device_id: `` },
                        { name: `Manual`, device_id: `Manual` },

                        ...devices,
                      ]"
                      placeholder="Device Name"
                      solo
                      flat
                      @change="applyFilters(header.key, id)"
                    ></v-select>
                    <v-select
                      :id="header.key"
                      :hide-details="true"
                      v-if="
                        header.filterSpecial &&
                        header.value == 'device.location'
                      "
                      outlined
                      dense
                      small
                      v-model="filters[header.key]"
                      item-text="location"
                      item-value="location"
                      :items="[{ location: `All Locations` }, ...devices]"
                      placeholder="Location"
                      solo
                      flat
                      @change="applyFilters(header.key, id)"
                    ></v-select>
                  </v-container>
                </td>
              </tr>
            </template>
            <template v-slot:item.UserID="{ item }">
              <strong> {{ item.UserID ? item.UserID : "---" }}</strong>
              <br />
              {{
                item.employee && item.employee.employee_id
                  ? item.employee.employee_id
                  : "---"
              }}
            </template>
            <template v-slot:item.employee.first_name="{ item, index }">
              <v-row no-gutters>
                <v-col
                  style="
                    padding: 5px;
                    padding-left: 0px;
                    width: 50px;
                    max-width: 50px;
                  "
                >
                  <v-img
                    style="
                      border-radius: 50%;
                      height: auto;
                      width: 50px;
                      max-width: 50px;
                    "
                    :src="
                      item.employee && item.employee.profile_picture
                        ? item.employee.profile_picture
                        : '/no-profile-image.jpg'
                    "
                  >
                  </v-img>
                </v-col>
                <v-col style="padding: 10px">
                  <strong>
                    {{ item.employee ? item.employee.first_name : "---" }}
                    {{
                      item.employee ? item.employee.last_name : "---"
                    }}</strong
                  >
                  <div>
                    {{
                      item.employee && item.employee.designation
                        ? caps(item.employee.designation.name)
                        : "---"
                    }}
                  </div>
                </v-col>
              </v-row>
            </template>
            <template v-slot:item.department.name.id="{ item }">
              <strong>{{
                item.employee && item.employee.department
                  ? caps(item.employee.department.name)
                  : "---"
              }}</strong>
              <div>
                {{
                  item.employee && item.employee.sub_department
                    ? caps(item.employee.sub_department.name)
                    : "---"
                }}
              </div>
            </template>
            <template v-slot:item.LogTime="{ item }">
              {{ item.LogTime }}
            </template>
            <template v-slot:item.device.name="{ item }">
              {{ item.device ? caps(item.device.name) : "---" }}
            </template>
            <template v-slot:item.gps_location="{ item }">
              {{ item.gps_location || "---" }}
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
    <!-- <v-row class="mt-5">
      <v-col cols="12">
        <v-data-table v-model="ids" item-key="id" :headers="headers" :items="data" :server-items-length="total"
          :loading="loading" :options.sync="options" :footer-props="{
            itemsPerPageOptions: [50, 100, 500, 1000],
          }"></v-data-table>
      </v-col>
    </v-row> -->
  </div>
  <NoAccess v-else />
</template>

<script>
import DateRangePicker from "../../components/Snippets/Filters/DateRangePicker.vue";

export default {
  components: {
    DateRangePicker,
  },
  data: () => ({
    branchesList: [],
    tableHeight: 750,
    id: "",
    from_menu_filter: "",
    from_date_filter: "",

    showFilters: false,
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
    endpoint: "attendance_logs",

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
    headers: [
      {
        text: "UserID",
        align: "center",
        sortable: false,
        value: "UserID",
      },
      { text: "DeviceID", align: "center", sortable: false, value: "DeviceID" },
      // {
      //   text: "Device Name",
      //   align: "center",
      //   sortable: false,
      //   value: "device.name",
      // },
      { text: "LogTime", align: "center", sortable: false, value: "LogTime" },
    ],
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
      itemsPerPage: 10,
    },
    errors: [],
    response: "",
    snackbar: false,
    headers_table: [
      {
        text: "User Id /Emp.Id ",
        align: "left",
        sortable: true,
        key: "UserID",
        value: "UserID",
        width: "150px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Employee",
        align: "left",
        sortable: true,
        key: "employee_first_name", //sorting
        value: "employee.first_name", //edit purpose
        width: "300px",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Department",
        align: "left",
        sortable: false,
        key: "department", //sorting
        value: "department.name.id", //edit purpose
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Date Range",
        align: "left",
        sortable: false,
        key: "date_range",
        value: "LogTime",
        fieldType: "date_range_picker",
      },
      // {
      //   text: "Log Time",
      //   align: "left",
      //   sortable: true,
      //   key: "LogTime", //sorting
      //   value: "LogTime", //edit purpose
      //   filterable: true,
      //   filterSpecial: true
      // },
      {
        text: "Device Name",
        align: "left",
        sortable: true,
        key: "device",
        value: "device.name",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Gps Location",
        align: "left",
        sortable: true,
        key: "gps_location",
        value: "gps_location",
        filterable: true,
        filterSpecial: true,
      },
    ],
  }),

  mounted() {
    this.tableHeight = window.innerHeight - 270;
    window.addEventListener("resize", () => {
      this.tableHeight = window.innerHeight - 270;
    });
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
    this.getDepartments();
    this.getbranchesList();
  },
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
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
      if (dates.length > 1) {
        this.getDataFromApi(this.endpoint, "dates", dates);
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
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
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
    datatable_save() {},
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
      //this.datatable_search_textbox = '';
    },
    getDeviceList() {
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`/device_list`, payload).then(({ data }) => {
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
    
    getRecords(filter_column = "", filter_value = "") {
      this.filters = {};
      this.isFilter = false;
      if (filter_value != "" && filter_value.length <= 2) {
        this.snack = true;
        this.snackColor = "error";
        this.snackText = "Minimum 3 Characters to filter ";
        this.loading = false;
        return false;
      }
      this.getDataFromApi(this.endpoint, filter_column, filter_value);
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      this.payloadOptions = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          ////department_ids: this.$auth.user.assignedDepartments,
          ...this.payload,
          ...this.filters,
        },
      };
      if (filter_column != "")
        this.payloadOptions.params[filter_column] = filter_value;
      this.loading = true;
      this.$axios.get(url, this.payloadOptions).then(({ data }) => {
        // if (filter_column != "" && data.data.length == 0) {
        //   this.snack = true;
        //   this.snackColor = "error";
        //   this.snackText = "No Results Found";
        //   this.loading = false;
        //   return false;
        // }
        //this.server_datatable_totalItems = data.total;
        this.data = data.data;
        this.total = data.total;
        this.loading = false;
        this.totalRowsCount = data.total;
      });
    },
    searchIt() {
      this.payload.from_date_txt = this.payload.from_date;
      this.payload.to_date_txt = this.payload.to_date;

      this.getDataFromApi();
      this.from_menu = false;
      this.to_menu = false;
      return false;
      let UserID = this.payload.UserID;
      let DeviceID = this.payload.DeviceID;

      if (UserID && UserID.length == 0 && DeviceID && DeviceID.length == 0) {
        this.getDataFromApi();
      } else {
        this.getDataFromApi(
          `${this.endpoint}/search/${this.$auth.user.company_id}`
        );
      }
    },
  },
};
</script>
