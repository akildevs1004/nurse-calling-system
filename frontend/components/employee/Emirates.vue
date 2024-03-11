<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-container>
      <v-row>
        <v-col cols="6">
          <v-row>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{ caps("emirate id") }}</label> -->
                <v-text-field
                  label="Emirate ID"
                  :hide-details="!errors.emirate_id"
                  :error-messages="errors.emirate_id && errors.emirate_id[0]"
                  dense
                  outlined
                  v-model="emirateItems.emirate_id"
                ></v-text-field>
              </div>
            </v-col>
            <v-col cols="6">
              <div class="form-group">
                <!-- <label class="col-form-label">{{ caps("nationality") }}</label> -->
                <v-text-field
                  label="Nationality"
                  :hide-details="!errors.nationality"
                  :error-messages="errors.nationality && errors.nationality[0]"
                  dense
                  outlined
                  v-model="emirateItems.nationality"
                ></v-text-field>
              </div>
            </v-col>

            <v-col cols="6">
              <!-- <label class="col-form-label"
            >Issue Date <span class="text-danger">*</span></label
          > -->
              <div>
                <v-menu
                  v-model="issueDateMenuOpen"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      label="Issue Date"
                      :hide-details="!errors.issue"
                      :error-messages="errors.issue && errors.issue[0]"
                      v-model="emirateItems.issue"
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
                    v-model="emirateItems.issue"
                    no-title
                    @input="issueDateMenuOpen = false"
                  ></v-date-picker>
                </v-menu>
              </div>
            </v-col>

            <v-col cols="6">
              <!-- <label class="col-form-label"
            >Expiry Date <span class="text-danger">*</span></label
          > -->
              <div>
                <v-menu
                  v-model="expiryDateMenuOpen"
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
                      :error-messages="errors.expiry && errors.expiry[0]"
                      v-model="emirateItems.expiry"
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
                    v-model="emirateItems.expiry"
                    no-title
                    @input="expiryDateMenuOpen = false"
                  ></v-date-picker>
                </v-menu>
              </div>
            </v-col>

            <v-col cols="6">
              <!-- <label class="col-form-label"
            >Date of Birth <span class="text-danger">*</span></label
          > -->
              <div>
                <v-menu
                  v-model="dobDateMenuOpen"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      label="Date of Birth"
                      :hide-details="!errors.date_of_birth"
                      :error-messages="
                        errors.date_of_birth && errors.date_of_birth[0]
                      "
                      v-model="emirateItems.date_of_birth"
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
                    v-model="emirateItems.date_of_birth"
                    no-title
                    @input="dobDateMenuOpen = false"
                  ></v-date-picker>
                </v-menu>
              </div>
            </v-col>

            <v-col cols="12">
              <span v-if="errors && errors.length" class="error--text">{{
                errors
              }}</span>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" class="text-right">
              <v-btn class="primary" small @click="save_emirate_info"
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
      issueDateMenuOpen: false,
      expiryDateMenuOpen: false,
      dobDateMenuOpen: false,
      emirate_info: false,
      errors: [],
      snackbar: false,
      response: "",
      emirateItems: {},
    };
  },
  created() {
    this.getInfo(this.employeeId);
  },
  methods: {
    getInfo(id) {
      this.$axios.get(`emirate/${id}`).then(({ data }) => {
        this.emirateItems = {
          ...data,
          employee_id: id,
        };
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

    can(item) {
      return true;
    },

    close_emirate_info() {
      this.emirate_info = false;
      this.errors = [];
      setTimeout(() => {
        this.$emit("close-popup");
      }, 1000);
    },

    save_emirate_info() {
      let payload = {
        ...this.emirateItems,
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.emirateItems.employee_id,
      };

      this.$axios
        .post(`emirate`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Emirates Info has been added";
            this.$emit("eventFromchild");
            this.close_emirate_info();
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
