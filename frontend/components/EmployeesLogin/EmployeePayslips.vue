<template>
  <div v-if="can(`payroll_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <div v-if="can(`employee_view`)">
      <v-row>
        <v-col>
          <!-- <Back class="primary white--text" /> -->

          <v-card class="mb-5 mt-2" elevation="0">
            <v-row class="pt-5">
              <v-col md="2" class="align-left">
                <v-icon
                  size="40"
                  @click="getDataFromApi(--year_display)"
                  style="cursor: pointer"
                >
                  mdi-less-than</v-icon
                ></v-col
              >
              <v-col md="8" class="text-center" style="font-size: 30px"
                ><v-icon size="40" fill>mdi-calendar-month</v-icon>
                {{ year_display }}
              </v-col>
              <v-col md="2" class="align-right text-end">
                <v-icon
                  size="40"
                  @click="getDataFromApi(++year_display)"
                  style="cursor: pointer"
                >
                  mdi-greater-than</v-icon
                ></v-col
              >
            </v-row>
            <!-- <v-toolbar class="mb-2" dense flat>
              <v-toolbar-title style="color: black"
                ><span> Payslips</span></v-toolbar-title
              >

               
              <v-btn
                dense
                class="ma-0 px-0"
                x-small
                :ripple="false"
                text
                title="Reload"
              >
                <v-icon class="ml-2" @click="clearFilters" dark
                  >mdi mdi-reload</v-icon
                >
              </v-btn>
             
              <v-btn
                dense
                class="ma-0 px-0"
                x-small
                :ripple="false"
                text
                title="Filter By Department"
              >
                <v-icon class="ml-2" @click="dialogVisible = true" dark
                  >mdi-filter
                </v-icon>
              </v-btn>
               
              <v-btn
                v-btn
                v-if="selectedItems.length"
                @click="generateNewpayslipsSelected"
                small
                class="primary toolbar-button-design1"
                color="primary"
                title="Payslips For selected"
              >
                Payslips For selected
              </v-btn>

              <v-col cols="4" class="text-right">
                 
                <v-btn
                  dense
                  class="ma-0 px-0"
                  x-small
                  :ripple="false"
                  text
                  title="Generate Payslips by Department"
                >
                  <v-icon @click="openPayslipDialog" class="mx-1 white--text"
                    >mdi-briefcase-outline</v-icon
                  >
                </v-btn>
                
                <v-btn
                  dense
                  class="ma-0 px-0"
                  x-small
                  :ripple="false"
                  text
                  v-if="downloadAllDisplayStatus"
                  download
                  :href="payslipsDownloadAllURL"
                  small
                  title="Download All Payslips"
                >
                  <v-icon
                    @click="showFilters = !showFilters"
                    class="mx-1 white--text"
                    >mdi mdi-download</v-icon
                  >
                </v-btn>
                 
              </v-col>
            </v-toolbar> -->

            <!-- <v-data-table
                v-model="data"
                show-select
                item-key="id"
                :headers="headers_table"
                :items="data"
              >
                <template v-slot:item.imgUrl="{ item }">
                  <v-img
                    style="
                      border-radius: 50%;
                      height: 50px;
                      width: 50px !important;
                    "
                    :src="item.profile_picture || '/no-profile-image.jpg'"
                  ></v-img>
                </template>
              </v-data-table>
              <v-row>
                <v-col lg="12" class="float-right">
                  <div class="float-right">
                    <v-pagination
                      v-model="pagination.current"
                      :length="pagination.total"
                      @input="onPageChange"
                      :total-visible="5"
                    ></v-pagination>
                  </div>
                </v-col>
              </v-row>
   -->

            <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
              {{ snackText }}

              <template v-slot:action="{ attrs }">
                <v-btn v-bind="attrs" text @click="snack = false">
                  Close
                </v-btn>
              </template>
            </v-snackbar>

            <v-data-table
              dense
              v-model="selectedItems"
              :headers="headers_table"
              :items="data"
              model-value="data.id"
              :loading="loading"
              class="elevation-1"
              hide-default-footer
              disable-pagination
            >
              <template v-slot:item.sno="{ item, index }">
                {{
                  currentPage
                    ? (currentPage - 1) * perPage +
                      (cumulativeIndex + data.indexOf(item))
                    : ""
                }}
              </template>
              <template v-slot:item.year_month="{ item }">
                {{ item.payroll_month }} / {{ item.payroll_year }}
              </template>

              <template v-slot:item.basic_salary="{ item }">
                {{ item.basic_salary }}
              </template>

              <template v-slot:item.net_salary="{ item }">
                {{ item.net_salary }}
              </template>
              <template v-slot:item.payslip="{ item }">
                <span
                  v-if="item?.payslip_status"
                  @click="navigateToViewPDF(item.id)"
                  style="
                    font-size: 25px;
                    vertical-align: inherit;
                    cursor: pointer;
                  "
                >
                  <v-icon small class="primary--text">mdi-eye</v-icon>
                </span>
                <a
                  v-if="item?.payslip_status"
                  :href="getdownloadLink(item.employee_table_id)"
                  style="
                    font-size: 25px;
                    vertical-align: inherit;
                    cursor: pointer;
                  "
                >
                  <v-icon small class="primary--text">mdi-download</v-icon>
                </a>
              </template>

              <template v-slot:item.actions="{ item }">
                <v-menu bottom left>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn dark-2 icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list width="120" dense>
                    <v-list-item @click="editItem(item)">
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="secondary" small>
                          mdi-information
                        </v-icon>
                        View
                      </v-list-item-title>
                    </v-list-item>
                    <!-- <v-list-item @click="res(item.id)">
                          <v-list-item-title style="cursor:pointer">
                            <v-icon color="primary" small>
                              mdi-eye
                            </v-icon>
                            Select
                          </v-list-item-title>
                        </v-list-item> -->
                  </v-list>
                </v-menu>
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>
      <div></div>
    </div>
    <NoAccess v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  props: ["system_user_id", "employee_id", "table_id"],
  data: () => ({
    year_display: "",
    payroll: {},
    cumulativeIndex: 1,
    perPage: 12,
    currentPage: 1,
    branchesList: [],
    filterLoader: false,
    filters: {},
    isFilter: false,
    totalRowsCount: 0,
    //server_datatable_totalItems: 1000,
    dialogVisible: false,
    server_datatable_totalItems: 1000,
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    downloadAllDisplayStatus: true,
    dialogPayslipsResults: false,
    payslipsResultsmessages: [],
    payslipsDownloadAllEmployeeidsArray: [],
    payslipsDownloadAllURL: "",
    items: [],
    dataYears: [],
    tab: null,
    pagination: {
      current: 1,
      total: 0,
      per_page: 100,
    },
    options: {},
    Model: "Payroll",
    endpoint: "employee",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    paymentMethod: ["Bank Transfer", "Cash", "Cheque"],
    Allowance: [
      "Transport",
      "Travel",
      "Entertainment",
      "Housing",
      "Uniform",
      "Uniform",
      "Medical/health",
    ],
    headers: [
      { text: "#" },
      { text: "E.ID" },
      { text: "Name" },
      { text: "Month/Year" },
      { text: "Designation" },
      { text: "Department" },
      { text: "Basic Salary" },
      { text: "Net Salary" },
      { text: "Payslip" },
    ],
    headers_table: [
      {
        text: "#",
        align: "left",
        sortable: false,
        key: "year_month",
        filterable: false,
        value: "sno",
        filterSpecial: false,
      },
      {
        text: "Month/Year",
        align: "left",
        sortable: false,
        key: "year_month",
        filterable: false,
        value: "year_month",
        filterSpecial: false,
      },
      {
        text: "Basic Salary",
        align: "left",
        sortable: true,
        filterable: true,
        key: "payrollbasic",
        value: "basic_salary",
        filterSpecial: false,
      },
      {
        text: "Net Salary",
        align: "left",
        sortable: true,
        filterable: true,
        key: "net_salary",
        value: "net_salary",
      },
      {
        text: "Payslip",
        align: "left",
        sortable: false,
        filterable: false,
        key: "payslip",
        value: "payslip",
        filterSpecial: false,
      },
    ],
    datatable_search_textbox: "",
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
    departments: [],
    department_id: "",
    department_idPopup: "",
    work: {
      first_name: "",
      last_name: "",
      department: "",
      sub_department: "",
      designation: "",
      role: "",
      employee_id: "",
      system_user_id: "",
      user: "",
      profile_picture: "",
      phone_number: "",
      whatsapp_number: "",
      joining_date: "",
    },
    BankInfo: {
      bank_name: "",
      account_no: "",
      account_title: "",
      iban: "",
      address: "",
      remark: "",
      company_id: "",
      employee_id: "",
    },
    salary: {
      basic_salary: "",
      payment_method: "",
      remark: "",
    },
    allowance: {
      name: "",
      amount: "",
      remark: "",
    },
    generatePayslipDialog: false,
    payslip_year: new Date().getFullYear(),
    payslip_month: new Date().getMonth(),
    payslip_year_Popup: new Date().getFullYear(),
    payslip_month_Popup: new Date().getMonth(),

    selectedItems: [],
    allSelected: false,
    monthNames: [
      { value: 1, label: "January" },
      { value: 2, label: "February" },
      { value: 3, label: "March" },
      { value: 4, label: "April" },
      { value: 5, label: "May" },
      { value: 6, label: "June" },
      { value: 7, label: "July" },
      { value: 8, label: "August" },
      { value: 9, label: "September" },
      { value: 10, label: "October" },
      { value: 11, label: "November" },
      { value: 12, label: "December" },
    ],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
    selectAll() {
      return this.selectedItems.length === this.items.length;
    },
    indeterminate() {
      return (
        this.selectedItems.length > 0 &&
        this.selectedItems.length < this.items.length
      );
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    department_id() {
      this.pagination.current = 1;
      this.getDataFromApi();
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    let dt = new Date();
    this.year_display = dt.getFullYear();
    this.loading = true;
    this.getDepartments();
    this.lastTenYears();
    this.getbranchesList();
  },
  mounted() {
    this.getDataFromApi();
  },

  methods: {
    // getBasicSalaryJSON(json) {
    //   let jsonArray = JSON.parse(json);

    //   return jsonArray.basic_salary;
    // },
    // getBasicNetSalaryJSON(json) {
    //   let jsonArray = JSON.parse(json);

    //   return jsonArray.net_salary;
    // },
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
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    applyFilters() {
      this.getDataFromApi();
    },
    fitleredMonthNames() {
      let dt = new Date();
      let py = this.payslip_year;
      let mns = this.monthNames;
      return mns.slice(0, py == dt.getFullYear() ? dt.getMonth() : 12);
    },
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },
    navigatetoEmployeepage() {
      this.$router.push("/employees");
    },
    downloadAllPayslipsError() {
      this.snackbar = true;

      this.response =
        "Payslips are not available. Please generate and try again";
      return false;
    },
    getdownloadLink(employee_id) {
      const baseURL = this.$axios.defaults.baseURL;
      const id = this.$auth.user.company_id;
      const month = this.payslip_month;
      const year = this.payslip_year;

      return `${baseURL}/render-payslip-by-employee?company_id=${id}&employee_id=${employee_id}&month=${month}&year=${year}`;
    },
    handleFilters() {
      this.selectedItems = [];
      this.allSelected = false;
      this.fitleredMonthNames();
      this.getDataFromApi();
    },

    navigateToViewPDF(id) {
      let path = `/payroll/salary/${id}_${this.payslip_month}_${this.payslip_year}`;
      this.$router.push(path);
    },

    lastTenYears() {
      const year = new Date().getFullYear();
      this.dataYears = Array.from({ length: 10 }, (_, i) => year - i);
    },
    toggleSelectAll() {
      this.selectedItems = this.allSelected ? this.data.map((e) => e.id) : [];
    },
    onPageChange() {
      this.getDataFromApi();
    },
    caps(str) {
      if (str == "" || str == null) {
        return "";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    
    res(id) {
      this.$axios.get(`employee/${id}`).then(({ data }) => {
        this.work = { ...data };
        this.getBankInfo(data.employee_id);
      });
    },
    getBankInfo(id) {
      this.$axios.get(`bankinfo/${id}`).then(({ data }) => {
        this.BankInfo = {
          ...data,
        };
      });
    },

    getDepartments() {
      let options = {
        params: {
          per_page: 100,
          //department_ids: this.$auth.user.assignedDepartments,
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
        this.departments.unshift({ name: "All Departments", id: "" });
      });
    },
    generateNewpayslipsByDepartment() {
      //   let url = this.endpoint;
      //   let options = {
      //     params: {
      //       company_id: this.$auth.user.company_id,
      //       department_id: this.department_idPopup,
      //       month: this.payslip_month_Popup,
      //       year: this.payslip_year_Popup,
      //     },
      //   };
      //   //localhost:8001/api/payslip-by-department/80
      //   this.$axios.get(`payslip-by-department`, options).then(({ data }) => {
      //     this.payslipsResultsmessages = [];
      //     this.data = data.data;
      //     this.dialogPayslipsResults = true;
      //     this.payslipsResultsmessages = data;
      //     this.snackbar = true;
      //     //data.status;
      //     //this.ids = [];
      //     this.response = "Payslips will be Genereated by selected department.";
      //     // this.pagination.current = data.current_page;
      //     // this.pagination.total = data.last_page;
      //     // this.loading = false;
      //     this.getDataFromApi();
      //   });
    },

    generateNewpayslipsSelected() {
      let checkedIdArray = [];
      this.selectedItems.forEach((element) => {
        checkedIdArray.push(element.id);
      });
      let url = this.endpoint;
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          employee_ids: checkedIdArray,

          year: this.payslip_year,
          month: this.payslip_month,
        },
      };
      this.$axios
        .get(`/generate-payslips-with-employeeids`, options)
        .then(({ data }) => {
          this.payslipsResultsmessages = [];
          this.dialogPayslipsResults = true;
          this.payslipsResultsmessages = data;

          this.snackbar = true;

          this.response = "Payslips Generated successfully";
          //this.selectedItems = [];
          //this.allSelected = false;
          this.getDataFromApi();
        });
    },
    getDataFromApi(url = this.endpoint, search_column_name = "") {
      this.loading = true;

      let department_id = this.department_id;
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      // if (this.filters) {
      //   page = 1;
      // }
      //console.log(this.$auth.user);

      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          // department_id: department_id,
          year: this.year_display,
          employee_id: this.employee_id, //this.$auth.user.employee.employee_id,
          employee_table_id: this.table_id, //this.$auth.user.employee.id,

          //month: this.payslip_month,
          ////department_ids: this.$auth.user.assignedDepartments,
          ...this.filters,
        },
      };

      this.$axios
        .get("get-payslip-by-employee-year", options)
        .then(({ data }) => {
          this.data = data;
          //this.payroll = data.payroll;
          this.loading = false;
          this.selectedItems = [];
          this.payslipsDownloadAllEmployeeidsArray = [];
          this.data.forEach((element) => {
            if (element.payslip_status)
              this.payslipsDownloadAllEmployeeidsArray.push(
                element.employee_id
              );
          });

          if (this.payslipsDownloadAllEmployeeidsArray.length > 0) {
            this.payslipsDownloadAllURL =
              this.$axios.defaults.baseURL +
              "/generate-payslips-zip?company_id=" +
              this.$auth.user.company_id +
              "&employee_ids=" +
              this.payslipsDownloadAllEmployeeidsArray +
              "&month=" +
              this.payslip_month +
              "&year=" +
              this.payslip_year;

            this.downloadAllDisplayStatus = true;
          } else {
            this.downloadAllDisplayStatus = false;
          }
        });

      this.loading = true;

      // options = {
      //   params: {
      //     per_page: this.pagination.per_page,
      //     company_id: this.$auth.user.company_id,
      //     department_id: department_id,
      //     year: this.payslip_year,
      //     month: this.payslip_month,
      //   },
      // };

      // this.$axios
      //   .get("generate-payslips-companyid/8", options)
      //   .then(({ data }) => {
      //     this.payslipsResultsmessages = data;
      //   });
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },
    datatable_searchById(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length >= 1) {
        this.getDataFromApi(
          `${this.endpoint}/searchby_emp_table_salary/${e}`,
          "employee_id"
        );
      }
    },
    datatable_searchByName(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length >= 1) {
        this.getDataFromApi(
          `${this.endpoint}/searchby_emp_table_salary/${e}`,
          "display_name"
        );
      }
    },
    datatable_searchByDepartmentName(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length >= 1) {
        this.getDataFromApi(
          `${this.endpoint}/searchby_emp_table_salary/${e}`,
          "search_department_name"
        );
      }
    },
    datatable_searchByDesignationName(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length >= 1) {
        this.getDataFromApi(
          `${this.endpoint}/searchby_emp_table_salary/${e}`,
          "search_designation_name"
        );
      }
    },
    datatable_searchBybasic_salary(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length >= 1) {
        this.getDataFromApi(
          `${this.endpoint}/searchby_emp_table_salary/${e}`,
          "searchBybasic_salary"
        );
      }
    },

    datatable_searchBynet_salary(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length >= 1) {
        this.getDataFromApi(
          `${this.endpoint}/searchby_emp_table_salary/${e}`,
          "searchBynet_salary"
        );
      }
    },

    editItem(item) {
      //this.$router.push(`/employees/${item.id}?id=${item.id}`);

      this.$router.push({ path: "/employees?id=1", params: { id: item.id } });
    },

    delteteSelectedRecords() {
      let just_ids = this.ids.map((e) => e.id);
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
            ids: just_ids,
          })
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
          })
          .catch((err) => console.log(err));
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
            }
          })
          .catch((err) => console.log(err));
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      let salary = {
        name: this.editedItem.name.toLowerCase(),
        company_id: this.$auth.user.company_id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, salary)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              this.data.splice(index, 1, {
                id: this.editedItem.id,
                name: this.editedItem.name,
              });
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, salary)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }
    },

    openPayslipDialog() {
      this.generatePayslipDialog = true;
    },
    closePopup() {},
  },
};
</script>
