<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
    </div>
    <v-card>
      <v-progress-linear
        :active="loading"
        color="primary"
        indeterminate
      ></v-progress-linear>
      <v-card-text>
        <v-row>
          <v-col cols="6"
            ><v-row>
              <v-col md="12" sm="12" cols="12" dense>
                <!-- <label class="col-form-label">RFID Card Number </label> -->
                <v-text-field
                  label="RFID Card Number"
                  type="number"
                  dense
                  outlined
                  v-model="employee.rfid_card_number"
                ></v-text-field>
              </v-col>

              <v-col md="12" sm="12" cols="12" dense>
                <!-- <label class="col-form-label">Password(4 to 8 Digits)</label> -->
                <v-text-field
                  label="Password(min 4 to 8 Digits)"
                  dense
                  type="number"
                  outlined
                  v-model="employee.rfid_card_password"
                ></v-text-field>
              </v-col>

              <v-col cols="12" class="text-right">
                <v-btn
                  v-if="can('employee_edit')"
                  small
                  :loading="loading"
                  color="primary"
                  @click="submit"
                >
                  Submit
                </v-btn>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </div>
</template>
<script>
export default {
  props: ["employeeId"],
  data: () => ({
    image: "",
    mime_type: "",
    cropedImage: "",
    cropper: "",
    autoCrop: false,
    dialogCropping: false,
    selectedFile: "",
    upload_edit: {
      name: "",
    },

    attrs: [],
    dialog: false,
    editDialog: false,
    tab: null,
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
    color: "background",
    files: "",
    Model: "Employee",
    endpoint: "employee",
    search: "",

    total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 8,
    response: "",
    ListName: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    employee: {
      email: "",
    },
    upload: {
      name: "",
    },
    previewImage: null,
    snackbar: false,
    ids: [],
    loading: false,
    total: 0,
    titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],
    editedIndex: -1,
    response: "",
    data: [],
    errors: [],
    departments: [],
    department_id: "",
    roles: [],
    payloadOptions: {},
  }),

  created() {
    this.payloadOptions = {
      params: {
        per_page: 10,
        company_id: this.$auth.user.company_id,
      },
    };
    this.getInfo(this.employeeId);
    this.getRoles();
  },
  mounted() {
    //this.getDataFromApi();
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    async getInfo(id) {
      this.loading = true;
      await this.$axios
        .get(`employee-single/${id}`)
        .then(({ data }) => {
          this.employee = data.user;
          this.employee.employee_id = id;
          this.employee.company_id = this.$auth.user.company_id;
          this.employee.rfid_card_number = data.rfid_card_number;
          this.employee.rfid_card_password = data.rfid_card_password;

          this.loading = false;
        })
        .catch((err) => console.log(err));
    },
    can() {
      return true;
    },
    getRoles() {
      this.payloadOptions.params.role_type = "employee";

      this.$axios.get(`role`, this.payloadOptions).then(({ data }) => {
        this.roles = data.data;
      });
    },
    close() {
      this.dialog = false;
    },
    submit() {
      this.loading = true;
      let payload = {
        params: {
          rfid_card_number: this.employee.rfid_card_number,
          rfid_card_password: this.employee.rfid_card_password,
          employee_id: this.employee.employee_id,
        },
      };
      this.$axios
        .post(`/employee-rfid-update/${this.employee.id || 0}`, payload.params)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.color = "red";
            this.errors = data.errors;
            this.snackbar = true;
            this.response = data.message;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Employees Updated successfully";
            setTimeout(() => {
              this.color = "background";
              this.$emit("eventFromchild");
              this.$emit("close-popup");
            }, 1000);
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
