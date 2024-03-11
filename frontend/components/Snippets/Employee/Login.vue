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
        <v-dialog persistent v-model="dialogCropping" width="400">
          <v-card style="padding-top: 20px">
            <v-card-text>
              <!-- <img :src="imageUrl" alt="Preview Image" /> -->
              <!-- Cropping image step1 -->
              <VueCropper
                v-show="selectedFile"
                ref="cropper"
                :src="selectedFile"
                alt="Source Image"
                :aspectRatio="1"
                :autoCropArea="0.9"
                :viewMode="3"
              ></VueCropper>

              <!-- <div class="cropper-preview"></div> -->
            </v-card-text>

            <v-card-actions>
              <div col="6" md="6" class="col-sm-12 col-md-6 col-12 pull-left">
                <v-btn
                  class="danger btn btn-danger text-left"
                  text
                  @click="closePopup()"
                  style="float: left"
                  >Cancel</v-btn
                >
              </div>
              <div col="6" md="6" class="col-sm-12 col-md-6 col-12 text-right">
                <v-btn
                  class="primary btn btn-danger text-right"
                  @click="saveCroppedImageStep2(), (dialog = false)"
                  >Crop</v-btn
                >
              </div>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-row>
          <v-col md="6" sm="6" cols="6" dense>
            <label class="col-form-label"
              >Employee Roll(Admin App Access)
            </label>
            <v-autocomplete
              :items="[{ name: 'No Access', id: 0 }, ...roles]"
              item-text="name"
              item-value="id"
              placeholder="Select"
              v-model="employee.employee_role_id"
              :hide-details="!errors.employee_role_id"
              :error="errors.employee_role_id"
              :error-messages="
                errors && errors.employee_role_id
                  ? errors.employee_role_id[0]
                  : ''
              "
              dense
              outlined
            ></v-autocomplete>
          </v-col>
        </v-row>
        <v-row>
          <v-col md="6" sm="6" cols="6" dense>
            <label class="col-form-label"
              >Email<span class="text-danger">*</span></label
            >
            <v-text-field
              dense
              outlined
              :hide-details="!errors.email"
              type="text"
              v-model="employee.email"
              :error="errors.email"
              :error-messages="errors && errors.email ? errors.email[0] : ''"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col md="6" sm="6" cols="6" dense>
            <label class="col-form-label">Password</label>
            <v-text-field
              dense
              outlined
              :hide-details="!errors.password"
              type="text"
              v-model="employee.password"
              :error="errors.password"
              :error-messages="
                errors && errors.password ? errors.password[0] : ''
              "
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col md="6" sm="6" cols="4" dense>
            <label class="col-form-label">Confirm Password</label>
            <v-text-field
              dense
              outlined
              :hide-details="!errors.password_confirmation"
              type="text"
              v-model="employee.password_confirmation"
              :error="errors.password_confirmation"
              :error-messages="
                errors && errors.password_confirmation
                  ? errors.password_confirmation[0]
                  : ''
              "
            ></v-text-field>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" class="text-right">
            <v-btn
              v-if="can('employee_create')"
              small
              :loading="loading"
              color="primary"
              @click="submit"
            >
              Submit
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </div>
</template>
<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";
export default {
  components: {
    VueCropper,
  },
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
      this.$axios
        .post(`/employee-login-update/${this.employee.id || 0}`, this.employee)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Employees Updated successfully";
            this.$emit("eventFromchild");
            this.$emit("close-popup");
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
