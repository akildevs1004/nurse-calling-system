<template>
  <v-tabs color="primary" :vertical="vertical" right>
    <v-tab>
      <v-icon> mdi-domain </v-icon>
    </v-tab>

    <v-tab>
      <v-icon> mdi-account </v-icon>
    </v-tab>
    <v-tab>
      <v-icon> mdi-earth </v-icon>
    </v-tab>

    <v-tab-item>
      <v-container class="mt-5">
        <v-row>
          <v-col cols="12" md="3">
            <div class="text-center">
              <v-img
                style="
                  width: 150px;
                  height: 150px;
                  border-radius: 50%;
                  margin: 0 auto;
                "
                :src="previewImage"
              ></v-img>

              <input
                required
                type="file"
                @change="attachment"
                style="display: none"
                accept="image/*"
                ref="attachment_input"
              />

              <span v-if="errors && errors.logo" class="text-danger mt-2">{{
                errors.logo[0]
              }}</span>
            </div>
          </v-col>
          <v-col class="mt-3" md="9" sm="12" cols="12" dense>
            <v-row>
              <v-col md="6" cols="12" sm="12" dense>
                <v-text-field
                  label="Building Name"
                  dense
                  outlined
                  type="text"
                  v-model="company_payload.name"
                  :hide-details="!errors.name"
                  :error-messages="errors && errors.name ? errors.name[0] : ''"
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <v-text-field
                  label="Email"
                  dense
                  outlined
                  type="text"
                  v-model="user_payload.email"
                  :hide-details="!errors.email"
                  :error-messages="
                    errors && errors.email ? errors.email[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <v-menu
                  v-model="dateMenu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      label="Member From"
                      :hide-details="!errors.member_from"
                      :error-messages="
                        errors && errors.member_from
                          ? errors.member_from[0]
                          : ''
                      "
                      v-model="company_payload.member_from"
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
                    v-model="company_payload.member_from"
                    no-title
                    @input="dateMenu = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <v-menu
                  v-model="dateMenu2"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      label="Expiry Date"
                      :hide-details="!errors.expiry"
                      :error-messages="
                        errors && errors.expiry ? errors.expiry[0] : ''
                      "
                      v-model="company_payload.expiry"
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
                    v-model="company_payload.expiry"
                    no-title
                    @input="dateMenu2 = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-col md="6" cols="12" sm="12" dense>
                <v-text-field
                  label="Management Company"
                  dense
                  outlined
                  type="text"
                  v-model="company_payload.management_company"
                  :hide-details="!errors.management_company"
                  :error-messages="
                    errors && errors.management_company
                      ? errors.management_company[0]
                      : ''
                  "
                ></v-text-field>
              </v-col>
            </v-row>
          </v-col>
         
        </v-row>
      </v-container>
    </v-tab-item>

    <v-tab-item>
      <v-container class="mt-5">
        <v-row v-if="contact_payload && contact_payload.id">
          <v-col md="6" cols="12" sm="12" dense>
            <v-text-field
              label="Person Name"
              dense
              outlined
              type="text"
              v-model="contact_payload.name"
              :hide-details="!errors.name"
              :error-messages="errors && errors.name ? errors.name[0] : ''"
            ></v-text-field>
          </v-col>

          <v-col md="6" cols="12" sm="12" dense>
            <v-text-field
              label="Person Number"
              dense
              outlined
              type="text"
              v-model="contact_payload.number"
              :hide-details="!errors.number"
              :error-messages="errors && errors.number ? errors.number[0] : ''"
            ></v-text-field>
          </v-col>

          <v-col md="6" cols="12" sm="12" dense>
            <v-text-field
              label="Position"
              dense
              outlined
              type="text"
              v-model="contact_payload.position"
              :hide-details="!errors.position"
              :error-messages="
                errors && errors.position ? errors.position[0] : ''
              "
            ></v-text-field>
          </v-col>

          <v-col md="6" cols="12" sm="12" dense>
            <v-text-field
              label="Whatsapp"
              dense
              outlined
              type="text"
              v-model="contact_payload.whatsapp"
              :hide-details="!errors.whatsapp"
              :error-messages="
                errors && errors.whatsapp ? errors.whatsapp[0] : ''
              "
            ></v-text-field>
          </v-col>
          
        </v-row>
      </v-container>
    </v-tab-item>

    <v-tab-item>
      <v-container class="mt-5">
        <v-row>
          <v-col md="6" cols="12" sm="12" dense>
            <v-text-field
              label="Lat"
              dense
              outlined
              type="text"
              v-model="geographic_payload.lat"
              :hide-details="!errors.lat"
              :error-messages="errors && errors.lat ? errors.lat[0] : ''"
            ></v-text-field>
          </v-col>

          <v-col md="6" cols="12" sm="12" dense>
            <v-text-field
              label="Lon"
              dense
              outlined
              type="text"
              v-model="geographic_payload.lon"
              :hide-details="!errors.lon"
              :error-messages="errors && errors.lon ? errors.lon[0] : ''"
            ></v-text-field>
          </v-col>

          <v-col md="12" cols="12" sm="12" dense>
            <v-textarea
              rows="3"
              label="Location"
              dense
              outlined
              type="text"
              v-model="geographic_payload.location"
              :hide-details="!errors.location"
              :error-messages="
                errors && errors.location ? errors.location[0] : ''
              "
            ></v-textarea>
          </v-col>
         
        </v-row>
      </v-container>
    </v-tab-item>
  </v-tabs>
</template>

<script>
export default {
  props: ["id"],
  layout({ $auth }) {
    let { user_type } = $auth.user;
    if (user_type == "master") {
      return "master";
    } else if (user_type == "employee") {
      return "employee";
    } else if (user_type == "master") {
      return "default";
    }
  },
  data: () => ({
    dialog: false,
    dateMenu: false,
    dateMenu2: false,
    enable_whatsapp_otp: "",
    whatsapp_instance_id: "",

    whatsapp_access_token: "",
    show_password: false,
    show_password_confirm: false,
    current_password_show: false,
    vertical: false,
    loading: false,
    preloader: true,
    upload: {
      name: "",
    },

    company_payload: {
      name: "",
      logo: "",
      member_from: "",
      expiry: "",
      max_branches: "",
      max_employee: "",
      max_devices: "",
      mol_id: "",
      p_o_box_no: "",
    },

    company_trade_license: {
      license_no: "",
      license_type: "",
      emirate: "",
      makeem_no: "",
      manager: "",
      issue_date: "",
      expiry_date: "",
    },

    contact_payload: {
      name: "",
      number: "",
      position: "",
      whatsapp: "",
    },
    user_payload: {
      password: "",
      password_confirmation: "",
    },
    geographic_payload: {
      lat: "",
      lon: "",
      location: "",
    },
    e1: 1,
    errors: [],
    previewImage: `/no-business_profile.png`,
    data: {},
    response: "",
    snackbar: false,
  }),
  async created() {
    this.getDataFromApi();
  },
  methods: {
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    getDataFromApi() {
      this.$axios.get(`company/${this.id}`).then(({ data }) => {
        let r = data.record;
        this.previewImage = r.logo;
        this.company_payload = r;
        this.contact_payload = r.contact;
        this.user_payload = r.user;

        let mf = this.formatted_date(r.member_from);
        let exp = this.formatted_date(r.expiry);
        this.company_payload.member_from = mf;
        this.company_payload.expiry = exp;

        this.whatsapp_access_token = this.company_payload.whatsapp_access_token;
        this.whatsapp_instance_id = this.company_payload.whatsapp_instance_id;

        this.enable_whatsapp_otp = this.company_payload.enable_whatsapp_otp;

        this.geographic_payload = {
          lat: this.company_payload.lat,
          lon: this.company_payload.lon,
          location: this.company_payload.location,
        };

        this.preloader = false;
      });
    },

    formatted_date(v) {
      let [year, month, date] = v.split("/");
      return `${year}-${month}-${date}`;
    },
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },

    attachment(e) {
      this.upload.name = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;
      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          this.previewImage = e.target.result;
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);
      }
    },

    update_company() {
      let payload = new FormData();

      payload.append("name", this.company_payload.name);
      if (this.upload.name) {
        payload.append("logo", this.upload.name);
      }
      payload.append("location", this.company_payload.location);
      payload.append("member_from", this.company_payload.member_from);
      payload.append("expiry", this.company_payload.expiry);

      payload.append("max_employee", 0);
      payload.append("max_branches", 0);
      payload.append("max_devices", 0);
      payload.append("mol_id", 0);
      payload.append("p_o_box_no", "00000");

      payload.append("email", this.user_payload.email);

      this.start_process(`/company/${this.id}/update`, payload, `Company`);
    },
    update_contact() {
      this.start_process(
        `/company/${this.id}/update/contact`,
        this.contact_payload,
        `Contact`
      );
    },
    update_whatsapp_settings() {
      this.start_process(
        `/company/${this.id}/update/whatsapp_settings`,
        {
          whatsapp_access_token: this.whatsapp_access_token,
          whatsapp_instance_id: this.whatsapp_instance_id,
          enable_whatsapp_otp: this.enable_whatsapp_otp,
        },
        `Contact`
      );
    },

    update_license() {
      this.start_process(
        `/company/${this.id}/trade-license`,
        this.company_trade_license,
        `Trade License`
      );
    },
    update_geographic() {
      this.start_process(
        `/company/${this.id}/update/geographic`,
        this.geographic_payload,
        `Geographic Info`
      );
    },
    update_user() {
      this.start_process(
        `/company/${this.id}/update/user`,
        this.user_payload,
        `User`
      );
    },
    start_process(url, payload, model) {
      this.loading = true;

      this.$axios
        .post(url, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.response = model + " updated successfully";
            this.$emit("success");
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
