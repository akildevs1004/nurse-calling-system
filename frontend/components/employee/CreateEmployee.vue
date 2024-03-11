<template>
  <div>
    <v-snackbar v-model="response.status" small top="top" color="background">
      {{ response.message }}
    </v-snackbar>

    <v-dialog persistent v-model="dialogCropping" width="500">
      <v-card style="padding-top: 20px">
        <v-card-text>
          <VueCropper
            v-show="selectedFile"
            ref="cropper"
            :src="selectedFile"
            alt="Source Image"
            :aspectRatio="1"
            :autoCropArea="0.9"
            :viewMode="3"
          ></VueCropper>
        </v-card-text>

        <v-card-actions>
          <div col="6" md="6" class="col-sm-12 col-md-6 col-12 pull-left">
            <v-btn
              dark
              class="danger btn btn-danger text-left"
              text
              @click="closeCropperPopup()"
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

    <v-card>
      <v-card-title dark class="primary white--text background">
        Create {{ Model }}
        <v-spacer></v-spacer>
        <v-icon @click="employeeDialog = false" outlined dark color="white">
          mdi mdi-close-circle
        </v-icon>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col md="6" sm="12" cols="12" dense>
            <v-row>
              <v-col md="6" sm="12" cols="12">
                <label class="col-form-label"
                  >Title <span class="text-danger">*</span></label
                >
                <v-select
                  v-model="employee.title"
                  :items="titleItems"
                  :hide-details="!errors.title"
                  :error="errors.title"
                  :error-messages="
                    errors && errors.title ? errors.title[0] : ''
                  "
                  dense
                  outlined
                ></v-select>
              </v-col>
              <v-col md="6" sm="12" cols="12">
                <label class="col-form-label"
                  >Joining Date <span class="text-danger">*</span></label
                >
                <div>
                  <v-menu
                    v-model="joiningDateMenuOpen"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        :hide-details="!errors.joining_date"
                        :error-messages="
                          errors && errors.joining_date
                            ? errors.joining_date[0]
                            : ''
                        "
                        v-model="employee.joining_date"
                        persistent-hint
                        append-icon="mdi-calendar"
                        readonly
                        outlined
                        dense
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      style="min-height: 320px"
                      v-model="employee.joining_date"
                      no-title
                      @input="joiningDateMenuOpen = false"
                    ></v-date-picker>
                  </v-menu>
                </div>
              </v-col>
              <v-col md="12" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >Display Name <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  :hide-details="!errors.display_name"
                  type="text"
                  v-model="employee.display_name"
                  :error="errors.display_name"
                  :error-messages="
                    errors && errors.display_name ? errors.display_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >First Name <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  :hide-details="!errors.first_name"
                  type="text"
                  v-model="employee.first_name"
                  :error="errors.first_name"
                  :error-messages="
                    errors && errors.first_name ? errors.first_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >Last Name <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  :hide-details="!errors.last_name"
                  type="text"
                  v-model="employee.last_name"
                  :error="errors.last_name"
                  :error-messages="
                    errors && errors.last_name ? errors.last_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="6" cols="6" sm="6" dense>
                <label class="col-form-label"
                  >Employee ID <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="employee.employee_id"
                  :hide-details="!errors.employee_id"
                  :error="errors.employee_id"
                  :error-messages="
                    errors && errors.employee_id ? errors.employee_id[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="6" cols="6" sm="6" dense>
                <label class="col-form-label"
                  >Employee Device Id<span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="employee.system_user_id"
                  :hide-details="!errors.system_user_id"
                  :error="errors.system_user_id"
                  :error-messages="
                    errors && errors.system_user_id
                      ? errors.system_user_id[0]
                      : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label class="col-form-label">Branch </label>

                <v-select
                  label="Branch "
                  v-model="employee.branch_id"
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
              <v-col cols="12">
                <label class="col-form-label">Department </label>
                <v-autocomplete
                  :items="departments"
                  item-text="name"
                  item-value="id"
                  placeholder="Select"
                  v-model="employee.department_id"
                  :hide-details="!errors.department_id"
                  :error="errors.department_id"
                  :error-messages="
                    errors && errors.department_id
                      ? errors.department_id[0]
                      : ''
                  "
                  dense
                  outlined
                ></v-autocomplete>
              </v-col>

              <v-col md="12" cols="12" sm="12" dense>
                <label class="col-form-label">Email (optional)</label>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="employee.email"
                  :hide-details="!errors.email"
                  :error="errors.email"
                  :error-messages="
                    errors && errors.email ? errors.email[0] : ''
                  "
                ></v-text-field>
              </v-col>
            </v-row>
          </v-col>
          <v-col class="col-sm-6">
            <div class="form-group pt-15" style="margin: 0 auto; width: 200px">
              <v-img
                style="
                  width: 100%;
                  height: 200px;
                  border: 1px solid #5fafa3;
                  border-radius: 50%;
                  margin: 0 auto;
                "
                :src="previewImage || '/no-profile-image.jpg'"
              ></v-img>
              <br />
              <v-btn
                small
                class="form-control primary"
                @click="onpick_attachment"
                >{{ !upload.name ? "Upload" : "Change" }}
                Profile Image
                <v-icon right dark>mdi-cloud-upload</v-icon>
              </v-btn>
              <input
                required
                type="file"
                @change="attachment"
                style="display: none"
                accept="image/*"
                ref="attachment_input"
              />
              <Camera @captured-image="handleImage" />

              <span
                v-if="errors && errors.profile_picture"
                class="text-danger mt-2"
                >{{ errors.profile_picture[0] }}</span
              >
            </div>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <!-- <v-btn small color="grey white--text" @click="employeeDialog = false">
              Close
            </v-btn> -->

        <v-btn small :loading="loading" color="primary" @click="store_data">
          Submit
        </v-btn>
      </v-card-actions>
    </v-card>
  </div>
</template>

<script>
import VueCropper from "vue-cropperjs";
import Camera from "../Snippets/Camera.vue";

export default {
  components: {
    VueCropper,
    Camera,
  },
  data: () => ({
    Model: "Employee",
    titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],
    departments: [],
    joiningDateMenuOpen: false,
    previewImage: null,
    loading: false,
    openDialog: false,
    upload: {
      name: "",
    },
    dialogCropping: false,
    selectedFile: "",

    errors: [],
    imageData: null,
    employee: {
      title: "Mr",
      email: "",
      display_name: "test",
      employee_id: Math.floor(Math.random() * 9000) + 1000,
      system_user_id: Math.floor(Math.random() * 9000) + 1000,
      department_id: 2,
      joining_date: "2023-08-11",
      first_name: "fsdsfdf",
      last_name: "dfsdfds",
      company_id: "1",
    },
    blob: false,
    response: {
      status: false,
      message: "",
    },
    branchesList: [],
  }),
  created() {
    this.previewImage = null;
    this.getDepartments();
    this.getbranchesList();
  },
  methods: {
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
    handleImage() {},
    saveCroppedImageStep2() {
      this.cropedImage = this.$refs.cropper.getCroppedCanvas().toDataURL();

      this.image_name = this.cropedImage;
      this.previewImage = this.cropedImage;

      this.dialogCropping = false;
    },

    closeCropperPopup() {
      //croppingimagestep5
      this.$refs.attachment_input.value = null;
      this.dialogCropping = false;
    },
    closeDialog() {
      this.$emit("closeDialog", false);
    },
    getDepartments() {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
        // this.departments.unshift({ name: "All Departments", id: "" });
      });
    },
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },
    attachment(e) {
      const input = this.$refs.attachment_input;
      const file = input.files[0];

      if (!file) {
        return;
      }

      this.upload.name = file.name || "";

      if (file.size > 1024 * 1024) {
        this.errors["profile_picture"] = [
          "File too big (> 1MB). Upload less than 1MB",
        ];
        return;
      }

      const reader = new FileReader();
      reader.onload = (event) => {
        this.selectedFile = event.target.result;
        this.$refs.cropper.replace(this.selectedFile);
      };
      reader.readAsDataURL(file);

      console.log(reader);

      this.$emit("input", file);

      this.dialogCropping = true;
    },

    async store_data() {
      const employee = new FormData();
      employee.append("title", this.employee.title);
      employee.append("email", this.employee.email);
      employee.append("display_name", this.employee.display_name);
      employee.append("employee_id", this.employee.employee_id);
      employee.append("system_user_id", this.employee.system_user_id);
      employee.append("department_id", this.employee.department_id);
      employee.append("joining_date", this.employee.joining_date);
      employee.append("first_name", this.employee.first_name);
      employee.append("last_name", this.employee.last_name);
      employee.append("company_id", this.employee.company_id);
      employee.append("department_id", this.employee.department_id);

      const blob = new Promise((resolve) => {
        this.$refs.cropper.getCroppedCanvas().toBlob((blob) => {
          resolve(blob);
        });
      });

      employee.append("profile_picture", await blob, "cropped_image.jpg");

      this.$axios
        .post("/employee-store", employee)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.$emit("handleResult", "Employee has been created");
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
