<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-container>
      <v-row>
        <v-col md="6" sm="12" cols="12" dense>
          <h4>Contact Details</h4>
          <br />
          <v-row>
            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Phone Number</label> -->
              <v-text-field
                label="Phone Number"
                dense
                outlined
                :hide-details="!errors.phone_number"
                type="text"
                v-model="contactItem.phone_number"
                :error="errors.phone_number"
                :error-messages="
                  errors && errors.phone_number ? errors.phone_number[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Whatsapp Number ( ex: 971XXXX)</label> -->
              <v-text-field
                label="Whatsapp Number ( ex: 971XXXX)"
                dense
                outlined
                :hide-details="!errors.whatsapp_number"
                type="text"
                v-model="contactItem.whatsapp_number"
                :error="errors.whatsapp_number"
                :error-messages="
                  errors && errors.whatsapp_number
                    ? errors.whatsapp_number[0]
                    : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Alternate Email</label> -->
              <v-text-field
                label="Alternate Email"
                dense
                outlined
                :hide-details="!errors.local_email"
                type="text"
                v-model="contactItem.local_email"
                :error="errors.local_email"
                :error-messages="
                  errors && errors.local_email ? errors.local_email[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Phone Relative Number</label> -->
              <v-text-field
                label="Phone Relative Number"
                dense
                outlined
                :hide-details="!errors.phone_relative_number"
                type="text"
                v-model="contactItem.phone_relative_number"
                :error="errors.phone_relative_number"
                :error-messages="
                  errors && errors.phone_relative_number
                    ? errors.phone_relative_number[0]
                    : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Relation</label> -->
              <v-text-field
                label="Relation"
                dense
                outlined
                :hide-details="!errors.relation"
                type="text"
                v-model="contactItem.relation"
                :error="errors.relation"
                :error-messages="
                  errors && errors.relation ? errors.relation[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Local Address</label> -->
              <v-text-field
                label="Local Address"
                dense
                outlined
                :hide-details="!errors.local_address"
                type="text"
                v-model="contactItem.local_address"
                :error="errors.local_address"
                :error-messages="
                  errors && errors.local_address ? errors.local_address[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Local City</label> -->
              <v-text-field
                label="Local City"
                dense
                outlined
                :hide-details="!errors.local_city"
                type="text"
                v-model="contactItem.local_city"
                :error="errors.local_city"
                :error-messages="
                  errors && errors.local_city ? errors.local_city[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Local Country</label> -->
              <v-text-field
                label="Local Country"
                dense
                outlined
                :hide-details="!errors.local_country"
                type="text"
                v-model="contactItem.local_country"
                :error="errors.local_country"
                :error-messages="
                  errors && errors.local_country ? errors.local_country[0] : ''
                "
              ></v-text-field>
            </v-col>

            <!-- <v-col cols="12">
              <v-btn
                class="primary--text"
                text
                @click="other_info = !other_info"
              >
                {{ other_info ? "hide" : "show" }} Home Country Details
              </v-btn>
            </v-col> -->
          </v-row>
        </v-col>
        <v-col
          md="6"
          sm="12"
          cols="12"
          dense
          style="border-left: 1px solid #ddd"
        >
          <h4>Home Country Details</h4>
          <br />
          <v-row>
            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Address</label> -->
              <v-text-field
                label="Address"
                dense
                outlined
                :hide-details="!errors.home_address"
                type="text"
                v-model="contactItem.home_address"
                :error="errors.home_address"
                :error-messages="
                  errors && errors.home_address ? errors.home_address[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Tel</label> -->
              <v-text-field
                label="Tel"
                dense
                outlined
                :hide-details="!errors.home_tel"
                type="text"
                v-model="contactItem.home_tel"
                :error="errors.home_tel"
                :error-messages="
                  errors && errors.home_tel ? errors.home_tel[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Mobile</label> -->
              <v-text-field
                label="Mobile"
                dense
                outlined
                :hide-details="!errors.home_mobile"
                type="text"
                v-model="contactItem.home_mobile"
                :error="errors.home_mobile"
                :error-messages="
                  errors && errors.home_mobile ? errors.home_mobile[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Fax</label> -->
              <v-text-field
                label="Fax"
                dense
                outlined
                :hide-details="!errors.home_fax"
                type="text"
                v-model="contactItem.home_fax"
                :error="errors.home_fax"
                :error-messages="
                  errors && errors.home_fax ? errors.home_fax[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">City</label> -->
              <v-text-field
                label="City"
                dense
                outlined
                :hide-details="!errors.home_city"
                type="text"
                v-model="contactItem.home_city"
                :error="errors.home_city"
                :error-messages="
                  errors && errors.home_city ? errors.home_city[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">State</label> -->
              <v-text-field
                label="State"
                dense
                outlined
                :hide-details="!errors.home_state"
                type="text"
                v-model="contactItem.home_state"
                :error="errors.home_state"
                :error-messages="
                  errors && errors.home_state ? errors.home_state[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Country</label> -->
              <v-text-field
                label="Country"
                dense
                outlined
                :hide-details="!errors.home_country"
                type="text"
                v-model="contactItem.home_country"
                :error="errors.home_country"
                :error-messages="
                  errors && errors.home_country ? errors.home_country[0] : ''
                "
              ></v-text-field>
            </v-col>

            <v-col md="6" sm="12" cols="12" dense>
              <!-- <label class="col-form-label">Personal Email</label> -->
              <v-text-field
                label="Personal Email"
                dense
                outlined
                :hide-details="!errors.home_email"
                type="text"
                v-model="contactItem.home_email"
                :error="errors.home_email"
                :error-messages="
                  errors && errors.home_email ? errors.home_email[0] : ''
                "
              ></v-text-field>
            </v-col>

            <span v-if="errors && errors.length" class="error--text">{{
              errors
            }}</span>
          </v-row>

          <v-row>
            <v-col cols="12" class="text-right">
              <v-btn class="primary" small @click="save_contact_info"
                >Save</v-btn
              >
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
export default {
  props: ["employeeId"],
  data() {
    return {
      other_info: false,
      contact_info: false,
      response: "",
      snackbar: false,
      errors: [],
      contactItem: {},
    };
  },
  created() {
    this.getInfo();
  },
  methods: {
    getInfo() {
      this.$axios
        .get(
          `employee/${this.employeeId}?company_id=${this.$auth.user.company_id}`
        )
        .then(({ data }) => {
          this.contactItem = data;
        });
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    close_contact_info() {
      this.contact_info = false;
      this.errors = [];
      setTimeout(() => {
        this.$emit("close-popup");
      }, 1000);
    },

    can(item) {
      return true;
    },

    save_contact_info() {
      let payload = {
        ...this.contactItem,
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.employeeId,
      };
      this.$axios
        .post(`employee/update/contact`, payload)
        .then(({ data }) => {
          this.loading = false;
          this.$emit("eventFromchild");
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.getInfo();
            this.close_contact_info();
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>

<!-- <style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #fbfdff;
}
</style> -->
