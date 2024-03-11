<template>
  <div v-if="can('employee_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!loading">
      <v-dialog persistent v-model="DialogBox" width="900">
        <v-card>
          <v-toolbar class="popup_background" flat>
            {{ formAction }} Room

            <v-spacer></v-spacer>
            <span>
              <v-icon class="ml-2" @click="DialogBox = false" dark>
                mdi mdi-close-circle</v-icon
              >
            </span>
          </v-toolbar>
          <v-container>
            <v-row>
              <v-col cols="6">
                <v-text-field
                  label="Room Number"
                  :disabled="disabled"
                  v-model="payload.room_number"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.room_number"
                  :error-messages="
                    errors && errors.room_number ? errors.room_number[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-autocomplete
                  label="Floor Number"
                  outlined
                  :disabled="disabled"
                  v-model="payload.floor_id"
                  :items="floors"
                  dense
                  item-text="floor_number"
                  item-value="id"
                  :hide-details="!errors.floor_id"
                  :error-messages="
                    errors && errors.floor_id ? errors.floor_id[0] : ''
                  "
                >
                </v-autocomplete>
              </v-col>

              <v-col cols="6">
                <v-autocomplete
                  label="Category"
                  outlined
                  :disabled="disabled"
                  v-model="payload.room_category_id"
                  :items="room_categories"
                  dense
                  item-text="name"
                  item-value="id"
                  :hide-details="!errors.room_category_id"
                  :error-messages="
                    errors && errors.room_category_id
                      ? errors.room_category_id[0]
                      : ''
                  "
                >
                </v-autocomplete>
              </v-col>

              <v-col cols="6">
                <v-autocomplete
                  label="Status"
                  outlined
                  :disabled="disabled"
                  v-model="payload.status_id"
                  :items="[
                    { id: 1, status: `Active` },
                    { id: 2, status: `InActive` },
                  ]"
                  dense
                  item-text="status"
                  item-value="id"
                  :hide-details="!errors.status"
                  :error-messages="
                    errors && errors.status ? errors.status[0] : ''
                  "
                >
                </v-autocomplete>
              </v-col>
            </v-row>
          </v-container>
          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <div class="text-right">
              <v-btn small color="grey white--text" @click="DialogBox = false">
                Close
              </v-btn>

              <v-btn
                v-if="can('employee_create') && formAction == 'Create'"
                small
                :loading="loading"
                color="primary"
                @click="submit"
              >
                Submit
              </v-btn>
              <v-btn
                v-else-if="can('employee_create') && formAction == 'Edit'"
                small
                :loading="loading"
                color="primary"
                @click="update_data"
              >
                Update
              </v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
      <div v-if="can(`employee_view`)">
        <v-card elevation="0">
          <v-toolbar class="mb-2" dense flat>
            <v-toolbar-title
              ><span>{{ Model }}s </span></v-toolbar-title
            >
            <span>
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
            </span>
            <v-spacer></v-spacer>
            <span>
              <v-btn
                dense
                small
                class="primary"
                text
                title="Add Company"
                @click="addItem"
              >
                Create Room
                <v-icon right dark>mdi-plus-circle-outline</v-icon>
              </v-btn>
            </span>
          </v-toolbar>
          <v-data-table
            dense
            :headers="headers"
            :items="rooms"
            :loading="loadinglinear"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [100, 500, 1000],
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
                      @click:clear="
                        filters[header.value] = '';
                        applyFilters();
                      "
                      :hide-details="true"
                      v-if="header.filterable && !header.filterSpecial"
                      v-model="filters[header.value]"
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

            <template v-slot:item.status_id="{ item }">
              {{ item.status_id == 1 ? `Active` : `InActive` }}
            </template>

            <template v-slot:item.options="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <v-list-item @click="viewItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-eye </v-icon>
                      View
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="editItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small> mdi-pencil </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="deleteItem(item)">
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
      </div>
    </div>
    <Preloader v-else />
  </div>

  <NoAccess v-else />
</template>

<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";

export default {
  props: ["id"],
  components: {
    VueCropper,
  },

  data: () => ({
    disabled: false,
    room_categories: [],
    floors: [],
    payload: {
      room_number: null,
      floor_id: 1,
      room_category_id: 1,
      status_id: 1,
      company_id: 0,
    },

    tab: null,

    totalRowsCount: 0,
    filters: {},
    isFilter: false,
    sortBy: "id",
    sortDesc: false,
    snack: false,
    snackColor: "",
    snackText: "",
    loadinglinear: true,
    displayErrormsg: false,
    image: "",
    mime_type: "",
    cropedImage: "",
    cropper: "",
    autoCrop: false,
    dialogCropping: false,
    tabMenu: [],
    tab: "0",
    employeeId: 0,
    employeeObject: {},
    attrs: [],
    dialog: false,
    editDialog: false,
    viewDialog: false,
    selectedFile: "",
    DialogBox: false,
    m: false,
    expand: false,
    expand2: false,
    boilerplate: false,
    right: true,
    rightDrawer: false,
    drawer: true,
    tab: null,
    selectedItem: 1,
    on: "",
    files: "",
    search: "",
    loading: false,
    //total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 1000,
    ListName: "",
    color: "background",
    response: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    employee: {
      title: "Mr",
      display_name: "",
      employee_id: "",
      system_user_id: "",
    },
    upload: {
      name: "",
    },
    previewImage: null,

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    Model: "Room",
    endpoint: "room",
    search: "",
    snackbar: false,
    ids: [],
    loading: false,
    //total: 0,
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    rooms: [],
    errors: [],

    headers: [
      {
        text: "Room No",
        align: "left",
        sortable: true,
        key: "room_number",
        value: "room_number",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Floor No",
        align: "left",
        sortable: true,
        key: "floor.floor_number",
        value: "floor.floor_number",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Category",
        align: "left",
        sortable: true,
        key: "room_category.name",
        value: "room_category.name",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Status",
        align: "left",
        sortable: true,
        key: "status_id",
        value: "status_id",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Details",
        align: "left",
        sortable: false,
        key: "options",
        value: "options",
      },
    ],
    formAction: "Create",
  }),

  async created() {
    this.loading = false;
    this.boilerplate = true;

    this.getDataFromApi();
  },

  mounted() {},
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    async getDropDowns() {
      let { data: room_categories } = await this.$axios.get(`room-category`, {
        params: { company_id: this.id },
      });
      this.room_categories = room_categories.data;

      let { data: floors } = await this.$axios.get(`floor`, {
        params: { company_id: this.id },
      });
      this.floors = floors.data;
    },
    closeViewDialog() {
      this.viewDialog = false;
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    closePopup() {
      //croppingimagestep5
      this.$refs.attachment_input.value = null;
      this.dialogCropping = false;
    },
    close() {
      this.dialog = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    onPageChange() {
      this.getDataFromApi();
    },
    applyFilters() {
      this.getDataFromApi();
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
    getDataFromApi() {
      //this.loading = true;
      this.loadinglinear = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage, //this.pagination.per_page,
          company_id: this.id,
          ...this.filters,
        },
      };

      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.rooms = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;

        this.totalRowsCount = data.total;

        this.rooms.length == 0
          ? (this.displayErrormsg = true)
          : (this.displayErrormsg = false);

        this.loadinglinear = false;
      });
    },
    async addItem() {
      this.disabled = false;
      this.formAction = "Create";
      this.DialogBox = true;
      this.payload = {};
      await this.getDropDowns();
    },
    editItem(item) {
      this.disabled = false;
      this.formAction = "Edit";
      this.DialogBox = true;
      this.payload = item;
      this.previewImage = item.logo;
    },
    viewItem(item) {
      this.disabled = true;
      this.formAction = "View";
      this.DialogBox = true;
      this.payload = item;
    },
    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`${this.endpoint}/${item.id}`)
          .then(({ data }) => {
            this.getDataFromApi();
            this.snackbar = true;
            this.response = "Record deleted successfully";
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

    submit() {
      this.payload.company_id = this.id;
      this.$axios
        .post(this.endpoint, this.payload)
        .then(({ data }) => {
          this.errors = [];
          this.snackbar = true;
          this.response = "Room inserted successfully";
          this.getDataFromApi();
          this.DialogBox = false;
          this.dialog = true;
        })
        .catch(({ response }) => {
          if (!response) {
            return false;
          }
          let { status, data, statusText } = response;

          if (status && status == 422) {
            this.errors = data.errors;
            return;
          }

          this.snackbar = true;
          this.response = statusText;
        });

      // }
    },

    update_data() {
      this.payload.company_id = this.id;
      this.$axios
        .put(this.endpoint + "/" + this.payload.id, this.payload)
        .then(({ data }) => {
          this.errors = [];
          this.snackbar = true;
          this.response = "Room updated successfully";
          this.getDataFromApi();
          this.DialogBox = false;
        })
        .catch(({ response }) => {
          if (!response) {
            return false;
          }
          let { status, data, statusText } = response;

          if (status && status == 422) {
            this.errors = data.errors;
            return;
          }

          this.snackbar = true;
          this.response = statusText;
        });

      // }
    },
    // processImage(file) {
    //   const canvas = this.$refs.cropper.getCroppedCanvas();
    //   this.cropedImage = canvas.toDataURL();

    //   canvas.toBlob((blob) => {
    //     return { value: blob, file };
    //   }, "image/jpeg");
    // },
  },
};
</script>
