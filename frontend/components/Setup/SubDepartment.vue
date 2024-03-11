<template>
  <div v-if="can(`department_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div>
      <v-row>
        <v-dialog
          persistent
          v-model="dialogFormDesignation"
          :fullscreen="false"
          width="500px"
        >
          <v-card>
            <v-card-title dense class="popup_background">
              <span>New Designation</span>
              <v-spacer></v-spacer>
              <v-icon @click="dialogFormDesignation = false" outlined>
                mdi mdi-close-circle
              </v-icon>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row class="mt-2">
                  <v-col cols="12">
                    <v-text-field
                      v-model="new_Designation_name"
                      placeholder="Designation"
                      outlined
                      dense
                    ></v-text-field>
                    <span v-if="errors && errors.name" class="error--text">{{
                      errors.name[0]
                    }}</span>
                  </v-col>
                  <v-col cols="12">
                    <v-autocomplete
                      v-model="new_designation_department_id"
                      :items="departments"
                      item-text="name"
                      item-value="id"
                      placeholder="Select Departments"
                      outlined
                      dense
                    >
                    </v-autocomplete>
                    <span
                      v-if="errors && errors.department_id"
                      class="error--text"
                      >{{ errors.department_id[0] }}</span
                    >
                  </v-col>
                  <v-col
                    md="12"
                    lg="12"
                    class="text-right"
                    style="padding: 0px"
                  >
                    <v-btn class="primary" @click="savenewDesignation">Save</v-btn>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
          </v-card>
        </v-dialog>
        <v-dialog
          persistent
          v-model="dialogFormSubdepartment"
          :fullscreen="false"
          width="500px"
        >
          <v-card>
            <v-card-title dense class="popup_background">
              <span>New Sub Department</span>
              <v-spacer></v-spacer>
              <v-icon @click="dialogFormSubdepartment = false" outlined>
                mdi mdi-close-circle
              </v-icon>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row class="mt-2">
                  <v-col cols="12">
                    <v-text-field
                      v-model="New_sub_DepartmentName"
                      placeholder="Sub Department"
                      outlined
                      dense
                    ></v-text-field>
                    <span v-if="errors && errors.name" class="error--text">{{
                      errors.name[0]
                    }}</span>
                  </v-col>
                  <v-col cols="12">
                    <v-autocomplete
                      v-model="Newdepartment_id"
                      :items="departments"
                      item-text="name"
                      item-value="id"
                      placeholder="Select Departments"
                      outlined
                      dense
                    >
                    </v-autocomplete>
                    <span
                      v-if="errors && errors.department_id"
                      class="error--text"
                      >{{ errors.department_id[0] }}</span
                    >
                  </v-col>

                  <v-card-actions>
                    <v-col md="6" lg="6" style="padding: 0px">
                      <!-- <v-btn class="error" @click="close">
                          Cancel
                        </v-btn> -->
                    </v-col>
                    <v-col
                      md="6"
                      lg="6"
                      class="text-right"
                      style="padding: 0px"
                    >
                      <v-btn class="primary" @click="saveSubDepartment"
                        >Save</v-btn
                      >
                    </v-col>
                  </v-card-actions>
                </v-row>
              </v-container>
            </v-card-text>
          </v-card>
        </v-dialog>
        <v-dialog
          persistent
          v-model="dialogForm"
          :fullscreen="false"
          width="350px"
        >
          <v-card>
            <v-card-title dense class="popup_background">
              <span>{{ formTitle }} {{ Model }}</span>
              <v-spacer></v-spacer>
              <v-icon @click="dialogForm = false" outlined>
                mdi mdi-close-circle
              </v-icon>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row class="">
                  <v-col md="12" sm="12" cols="12" small dense class="pb-0">
                    <v-select
                      label="Branch "
                      v-model="editedItem.branch_id"
                      :items="branchesList"
                      dense
                      placeholder="Branch"
                      outlined
                      item-value="id"
                      item-text="branch_name"
                      :error="errors.branch_id"
                      :error-messages="
                        errors && errors.branch_id ? errors.branch_id[0] : ''
                      "
                    >
                    </v-select>
                  </v-col>
                  <v-col md="12" sm="12" cols="12" small dense class="pt-0">
                    <v-text-field
                      label="Department Name"
                      dense
                      outlined
                      :hide-details="!errors.name"
                      type="text"
                      v-model="editedItem.name"
                      :error="errors.name"
                      :error-messages="
                        errors && errors.name ? errors.name[0] : ''
                      "
                      placeholder="Department Name"
                    ></v-text-field>
                  </v-col>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-col md="12" sm="12" cols="12" class="pa-0 text-right">
                      <!-- <v-btn small dark class="background" @click="close">
                          Cancel
                        </v-btn> -->
                      <v-btn small class="primary" @click="save">Save</v-btn>
                    </v-col>
                  </v-card-actions>
                </v-row>
              </v-container>
            </v-card-text>
          </v-card>
        </v-dialog>
        <v-col md="12" lg="12">
          <!-- <Back color="primary" /> -->

          <v-card class="mb-5 mt-2 rounded-md" elevation="0">
            <v-toolbar class="rounded-md" dense flat>
              <v-toolbar-title
                ><span> {{ Model }} List</span></v-toolbar-title
              >
              <!-- 
                <v-tooltip top color="primary">
                  <template v-slot:activator="{ on, attrs }"> -->
              <v-btn
                dense
                class="ma-0 px-0"
                x-small
                :ripple="false"
                text
                title="Reload"
                @click="clearFilters()"
              >
                <v-icon class="ml-2" dark>mdi mdi-reload</v-icon>
              </v-btn>
              <!-- </template>
                  <span>Reload</span>
                </v-tooltip> -->

              <!-- <v-tooltip top color="primary">
                  <template v-slot:activator="{ on, attrs }"> -->
              <v-btn
                x-small
                :ripple="false"
                text
                title="Filter"
                @click="toggleFilter"
              >
                <v-icon dark white>mdi-filter</v-icon>
              </v-btn>
              <!-- </template>
                  <span>Filter</span>
                </v-tooltip> -->

              <v-spacer></v-spacer>
              &nbsp;
              <v-btn
                v-if="can(`sub_department_create`)"
                @click="newSubDepartment"
                small
                class="primary"
              >
                Sub Department +
              </v-btn>
            </v-toolbar>
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
                    </v-container>
                  </td>
                </tr>
              </template>
              <template v-slot:item.id="{ item }">
                {{ caps(item.id) }}
              </template>
              <template v-slot:item.name="{ item }">
                {{ caps(item.name) }}
              </template>
              <template v-slot:item.branch_name="{ item }">
                {{ (item.branch && item.branch.branch_name) || "---" }}
              </template>
              <template v-slot:item.sub_dep.name="{ item }">
                <span
                  v-for="(sub_dep, index) in item.children.slice(0, 3)"
                  :key="index"
                >
                  <div small class="ma-1">
                    {{ caps(sub_dep.name) }}
                  </div>
                  <br />
                </span>

                <div
                  small
                  class="ma-1"
                  style="color: black"
                  @click="gotoSubdepartments(item)"
                  v-if="item.children.length > 3"
                >
                  View all..
                </div>
              </template>
              <template v-slot:item.designations="{ item }">
                <span
                  v-for="(designation, index) in item.designations.slice(0, 3)"
                  :key="index"
                >
                  <div class="ma-1">
                    {{ caps(designation.name) }}
                  </div>
                  <br />
                </span>
              </template>
              <template v-slot:item.options="{ item }">
                <v-menu bottom left>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn dark-2 icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list width="120" dense>
                    <v-list-item
                      v-if="can(`department_view`)"
                      @click="gotoSubdepartments(item)"
                    >
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="primary" small> mdi-view-list </v-icon>
                        View
                      </v-list-item-title>
                    </v-list-item>

                    <v-list-item
                      v-if="can(`department_edit`)"
                      @click="editItem(item)"
                    >
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="secondary" small> mdi-pencil </v-icon>
                        Edit
                      </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      v-if="can(`department_delete`)"
                      @click="deleteItem(item)"
                    >
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="error" small> mdi-delete </v-icon>
                        Delete
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    show1: false,
    dialogFormDesignation: false,
    showFilters: false,
    options: {},
    filters: {},
    isFilter: false,
    generateLogsDialog: false,
    totalRowsCount: 0,
    new_Designation_name: "",
    new_designation_department_id: "",
    departments: [],

    New_sub_DepartmentName: "",
    Newdepartment_id: "",
    dialogFormSubdepartment: false,
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    dialogForm: false,
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    Model: "Departments",
    options: {},
    endpoint: "departments",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,

    editedIndex: -1,
    editedItem: {
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
    },
    defaultItem: {
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
    },
    response: "",
    data: [],
    errors: [],
    headers_table: [
      // {
      //   text: "Department Code",
      //   align: "left",
      //   sortable: true,
      //   value: "id",
      //   width: "150px",
      //   key: "id",
      //   filterable: true,
      // },
      // {
      //   key: "name",
      //   text: "Department",
      //   align: "left",
      //   sortable: true,
      //   value: "name",
      //   filterable: true,
      // },

      // {
      //   key: "branch_name",
      //   text: "Branch",
      //   align: "left",
      //   sortable: true,
      //   value: "branch_name",
      //   filterable: true,
      // },

      {
        text: "Sub Department",
        align: "left",
        sortable: false,
        value: "sub_dep.name",
      },
      // {
      //   text: "Designations",
      //   align: "left",
      //   sortable: false,
      //   value: "designations",
      // },

      { text: "Options", align: "left", sortable: false, value: "options" },
    ],
    branchesList: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },

    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
  },

  created() {
    this.loading = true;
    this.getDataFromApi();
    this.getbranchesList();
  },

  methods: {
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    newItem() {
      this.dialogForm = true;
    },
    newSubDepartment() {
      this.dialogFormSubdepartment = true;
    },
    newDesignation() {
      this.dialogFormDesignation = true;
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    onPageChange() {
      this.getDataFromApi();
    },
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
    getbranchesList() {
      this.payloadOptions = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(`branches_list`, this.payloadOptions).then(({ data }) => {
        this.branchesList = data;
        if (this.$auth.user.branch_id) {
          this.branch_id = this.$auth.user.branch_id;
        } else {
          // this.branchesList = [
          //   { branch_name: `All Branches`, id: `` },
          //   ,
          //   ...this.branchesList,
          // ];
          this.branch_id = "";
        }
      });
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (url == "") url = this.endpoint;
      this.loading = true;
      this.loading = true;

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
          //department_ids: this.$auth.user.assignedDepartments,

          ...this.filters,
        },
      };
      if (filter_column != "") {
        this.payloadOptions.params[filter_column] = filter_value;
      }
      this.$axios
        .get(`${url}?page=${page}`, this.payloadOptions)
        .then(({ data }) => {
          if (filter_column != "" && data.data.length == 0) {
            this.snack = true;
            this.snackColor = "error";
            this.snackText = "No Results Found";
            this.loading = false;
            return false;
          }
          this.data = data.data;
          this.departments = data.data;
          this.pagination.current = data.current_page;
          this.pagination.total = data.last_page;
          this.loading = false;
          this.totalRowsCount = data.total;
        });
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      //this.dialog = true;
      this.dialogForm = true;
    },
    gotoSubdepartments(item) {
      this.$router.push(`/sub-department?id=${item.id}`);
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
          .then((res) => {
            if (!res.data.status) {
              this.errors = res.data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = res.data.status;
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
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },

    close() {
      //this.dialog = false;
      this.dialogForm = false;
      this.dialogFormSubdepartment = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    savenewDesignation() {
      let payload = {
        name: this.new_Designation_name,
        department_id: this.new_designation_department_id,
        company_id: this.$auth.user.company_id,
      };

      this.$axios
        .post("designation", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.getDataFromApi();
            this.snackbar = data.status;
            this.response = data.message;
            this.dialogForm = false;
            this.dialogFormDesignation = false;
            this.close();
            this.errors = [];
            this.search = "";
            this.new_Designation_name = "";
            this.new_designation_department_id = "";
          }
        })
        .catch((res) => console.log(res));
    },
    saveSubDepartment() {
      let payload = {
        name: this.New_sub_DepartmentName,
        department_id: this.Newdepartment_id,
        company_id: this.$auth.user.company_id,
      };

      this.$axios
        .post("sub-departments", payload)
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
            this.New_sub_DepartmentName = "";
            this.Newdepartment_id = "";
            his.dialogFormSubdepartment = false;
          }
        })
        .catch((res) => console.log(res));
    },
    save() {
      let payload = {
        name: this.editedItem.name,
        company_id: this.$auth.user.company_id,
        branch_id: this.editedItem.branch_id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.dialogForm = false;
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
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
              this.dialogForm = false;
            }
          })
          .catch((res) => console.log(res));
      }
    },
  },
};
</script>
