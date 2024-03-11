<template>
  <div>
    <v-dialog persistent  v-model="dialog" max-width="900px">
      <template v-slot:activator="{ on, attrs }">
        <v-icon color="black lighten-2" v-bind="attrs" v-on="on">
          mdi-account-plus mdi-flip-h
        </v-icon>
      </template>

      <v-card>
        <v-card-title dark class="popup_background" style="font-weight: 400">
          Add {{ Model }}
          <v-spacer></v-spacer>

          <v-icon
            outlined
            dark
            color="black"
            class="mr-5"
            title="Save Employee"
            :loading="loading"
            @click="store_data"
          >
            mdi mdi-content-save-all
          </v-icon>
          <v-icon
            title="Close"
            @click="dialog = false"
            outlined
            dark
            color="black"
          >
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
                      errors && errors.display_name
                        ? errors.display_name[0]
                        : ''
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

                <v-col md="6" cols="6" sm="6" dense>
                  <label class="col-form-label"
                    >Mobile Number <span class="text-danger">*</span></label
                  >
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="employee.phone_number"
                    :hide-details="!errors.phone_number"
                    :error="errors.phone_number"
                    :error-messages="
                      errors && errors.phone_number
                        ? errors.phone_number[0]
                        : ''
                    "
                  ></v-text-field>
                </v-col>
                <v-col md="6" cols="6" sm="6" dense>
                  <label class="col-form-label"
                    >Whatsapp <span class="text-danger">*</span> ( ex:
                    971XXXX)</label
                  >
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="employee.whatsapp_number"
                    :hide-details="!errors.whatsapp_number"
                    :error="errors.whatsapp_number"
                    :error-messages="
                      errors && errors.whatsapp_number
                        ? errors.whatsapp_number[0]
                        : ''
                    "
                  ></v-text-field>
                </v-col>
                <v-col v-if="isCompany" cols="6">
                  <label class="col-form-label"
                    >Branch <span class="text-danger">*</span></label
                  >

                  <v-select
                    @change="filterDepartmentsByBranch(employee.branch_id)"
                    v-model="employee.branch_id"
                    :items="branches_list"
                    dense
                    placeholder="Select Branch"
                    outlined
                    item-value="id"
                    item-text="name"
                    :error="errors.branch_id"
                    :error-messages="
                      errors && errors.branch_id ? errors.branch_id[0] : ''
                    "
                  >
                  </v-select>
                </v-col>

                <v-col cols="6">
                  <label class="col-form-label"
                    >Department <span class="text-danger">*</span></label
                  >
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
              </v-row>
            </v-col>
            <v-col class="col-sm-6">
              <div
                class="form-group pt-15"
                style="margin: 0 auto; width: 200px"
              >
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
                  >{{ !upload.name ? "Upload" : "Change" }} Profile Image
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

          <v-btn small :loading="loading" color="primary" @click="store_data">
            Submit
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
              @click="saveCroppedImageStep2(), (dialogCropping = false)"
              >Crop</v-btn
            >
          </div>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";

export default {
  components: { VueCropper },
  data() {
    return {
      isCompany: true,
      loading: false,
      branches_list: [],
      departments: [],
      errors: [],
      branch_id: null,
      Model: "Employee",
      dialog: false,
      dialogCropping: false,
      employee: {},
      titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],
      joiningDateMenuOpen: false,
      previewImage: null,
      selectedFile: null,
      upload: {
        name: "",
      },
    };
  },
  // watch: {
  //   async dialog(newValue) {
  //     if (newValue) {
  //       if (this.$auth.user.branch_id) {
  //         await this.getDepartments(this.$auth.user.branch_id);
  //       } else {
  //         await this.getDepartments(null);
  //       }
  //       // Dialog is now open, you can perform actions here
  //       console.log("Dialog opened");
  //       // You can trigger additional actions or emit events if needed
  //     } else {
  //       // Dialog is now closed, you can perform actions here
  //       console.log("Dialog closed");
  //       // You can trigger additional actions or emit events if needed
  //     }
  //   },
  // },
  async created() {
    this.branches_list = await this.$store.dispatch("branches_list");
    console.log("sfdsfsdf");
    if (this.$auth.user.branch_id) {
      await this.getDepartments(this.$auth.user.branch_id);
    } else {
      await this.getDepartments(null);
    }
  },
  methods: {
    async getDepartments(filterBranchId) {
      let options = {
        endpoint: "department-list",
        isFilter: true,
        params: {
          company_id: this.$auth.user.company_id,
          branch_id: filterBranchId,
        },
      };
      this.departments = await this.$store.dispatch("department_list", options);
    },
    async getTimezone(filterBranchId) {
      let options = {
        endpoint: "timezone-list",
        isFilter: true,
        params: {
          company_id: this.$auth.user.company_id,
          branch_id: filterBranchId,
        },
      };
      this.timezones = await this.$store.dispatch("timezone_list", options);
    },
    async filterDepartmentsByBranch(filterBranchId) {
      await this.getDepartments(filterBranchId);
      await this.getTimezone(filterBranchId);
    },
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },
    attachment(e) {
      this.upload.name = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;

      if (file[0].size > 1024 * 1024) {
        e.preventDefault();
        this.errors["profile_picture"] = [
          "File too big (> 1MB). Upload less than 1MB",
        ];
        return;
      }

      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          //croppedimage step6
          // this.previewImage = e.target.result;

          this.selectedFile = event.target.result;

          this.$refs.cropper.replace(this.selectedFile);
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);

        this.dialogCropping = true;
      }
    },
    mapper(obj) {
      let employee = new FormData();

      for (let x in obj) {
        employee.append(x, obj[x]);
      }
      employee.append("profile_picture", this.upload.name);
      employee.append("company_id", this.$auth.user.company_id);

      return employee;
    },
    store_data() {
      let final = Object.assign(this.employee);
      let employee = this.mapper(final);
      this.saveToAPI(employee);

      // //croppedimageStep3
      // if (this.$refs.attachment_input.files[0]) {
      //   this.cropedImage = this.$refs.cropper.getCroppedCanvas().toDataURL();

      //   this.$refs.cropper.getCroppedCanvas().toBlob((blob) => {
      //     // Create a FormData object and append the Blob as a file
      //     //const formData = new FormData();
      //     employee.append("profile_picture", blob, "cropped_image.jpg");
      //     employee.append("attachment_input", blob, "cropped_image.jpg");

      //     //croppedimagesptep4 //push to API in blob method only
      //     this.saveToAPI(employee);
      //   }, "image/jpeg");
      // } else {
      //   this.saveToAPI(employee);
      // }
    },
    saveToAPI(employee) {
      this.$axios
        .post("/employee-store", employee)
        .then(({ data }) => {
          //this.loading = false;

          if (!data.status) {
            this.errors = [];
            if (data.errors) this.errors = data.errors;
            else {
              this.snackbar = true;
              this.response = data.message;
            }
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Employees inserted successfully";
            this.$emit("success");
            this.dialog = false;
          }
        })
        .catch((e) => console.log(e));
    },
    closePopup() {
      //croppingimagestep5
      this.$refs.attachment_input.value = null;
      this.dialogCropping = false;
    },
    saveCroppedImageStep2() {
      this.cropedImage = this.$refs.cropper.getCroppedCanvas().toDataURL();

      this.previewImage = this.cropedImage;

      this.dialogCropping = false;
    },
  },
};
</script>

<style scoped>
/* Add any custom styling here */
</style>
