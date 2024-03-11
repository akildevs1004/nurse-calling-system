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
          <div class="form-group">
            <label class="col-form-label">{{ caps("nationality") }}</label>
            <input
              v-model="personalItem.nationality"
              class="form-control"
              type=""
            />
            <span
              v-if="errors && errors.nationality"
              class="text-danger mt-2"
              >{{ errors.nationality[0] }}</span
            >
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("Date of birth") }}</label>
            <input
              min="0"
              v-model="personalItem.date_of_birth"
              class="form-control"
              type="date"
            />
            <span
              v-if="errors && errors.date_of_birth"
              class="text-danger mt-2"
              >{{ errors.date_of_birth[0] }}</span
            >
          </div>
        </v-col>
        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("religion") }}</label>
            <input
              v-model="personalItem.religion"
              class="form-control"
              type=""
            />
            <span v-if="errors && errors.religion" class="text-danger mt-2">{{
              errors.religion[0]
            }}</span>
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("marital status") }}</label>
            <select
              v-model="personalItem.marital_status"
              class="form-control"
              aria-label="Default select"
            >
              <option value="">select...</option>
              <option value="1">Married</option>
              <option value="2">Single</option>
            </select>
            <span
              v-if="errors && errors.marital_status"
              class="text-danger mt-2"
              >{{ errors.marital_status[0] }}</span
            >
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{
              caps("Employment of Spouse")
            }}</label>
            <input
              min="0"
              v-model="personalItem.no_of_spouse"
              class="form-control"
              type="text"
            />
            <span
              v-if="errors && errors.no_of_spouse"
              class="text-danger mt-2"
              >{{ errors.no_of_spouse[0] }}</span
            >
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("no of children") }}</label>
            <input
              min="0"
              v-model="personalItem.no_of_children"
              class="form-control"
              type="number"
            />
            <span
              v-if="errors && errors.no_of_children"
              class="text-danger mt-2"
              >{{ errors.no_of_children[0] }}</span
            >
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("father name") }}</label>
            <input
              min="0"
              v-model="personalItem.father_name"
              class="form-control"
              type="text"
            />
            <span
              v-if="errors && errors.father_name"
              class="text-danger mt-2"
              >{{ errors.father_name[0] }}</span
            >
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("mother name") }}</label>
            <input
              min="0"
              v-model="personalItem.mother_name"
              class="form-control"
              type="text"
            />
            <span
              v-if="errors && errors.mother_name"
              class="text-danger mt-2"
              >{{ errors.mother_name[0] }}</span
            >
          </div>
        </v-col>

        <v-col cols="6">
          <div class="form-group">
            <label class="col-form-label">{{ caps("gender") }}</label>
            <select
              v-model="personalItem.gender"
              class="form-select"
              aria-label="Default select"
            >
              <option value="">Select...</option>
              <option value="1">Male</option>
              <option value="2">Female</option>
            </select>
            <span v-if="errors && errors.gender" class="text-danger mt-2">{{
              errors.gender[0]
            }}</span>
          </div>
        </v-col>

        <v-col cols="12">
          <a
            href="javascrip:void(0)"
            @click="add_other_personal_info = !add_other_personal_info"
            >{{
              caps(`${add_other_personal_info ? "hide" : "show"} other field`)
            }}</a
          >
        </v-col>
        <v-row v-if="add_other_personal_info">
          <v-col cols="6">
            <div class="form-group">
              <label class="col-form-label">{{ caps("other text") }}</label>
              <input v-model="personalItem.other_text" class="form-control" />
              <span v-if="errors && errors.other_text" class="text-danger">{{
                errors.other_text[0]
              }}</span>
            </div>
          </v-col>

          <v-col cols="6">
            <div class="form-group">
              <label class="col-form-label">{{ caps("other value") }}</label>
              <input v-model="personalItem.other_value" class="form-control" />
              <span
                v-if="errors && errors.other_value"
                class="text-danger mt-2"
                >{{ errors.other_value[0] }}</span
              >
            </div>
          </v-col>
        </v-row>
        <span v-if="errors && errors.length" class="error--text">{{
          errors
        }}</span>
      </v-row>
      <v-row>
        <v-col cols="12">
          <v-btn class="error" small @click="close_personal_info">
            Cancel
          </v-btn>
          <v-btn class="primary" small @click="save_personal_info">Save</v-btn>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import KeyValueTable from "./KeyValueTable.vue";

export default {
  components: { KeyValueTable },
  props: ["employeeId"],
  data() {
    return {
      endpoint: "personalinfo",
      personal_info: false,
      add_other_personal_info: false,
      snackbar: false,
      response: "",
      errors: [],
      personalItem: {},
      table_data: {},
    };
  },

  created() {
    this.getInfo();
  },

  methods: {
    async getInfo() {
      try {
        const id = this.employeeId;
        const endpoint = this.endpoint;
        const { data } = await this.$axios.get(`${endpoint}/${id}`);

        // Prepare the data for the table
        this.table_data = {
          Nationality: data.nationality,
          Religion: data.religion,
          "Marital Status": data.marital_status == 1 ? "Married" : "Single",
          "Date of Birth": data.date_of_birth,
          "No. of Children": data.no_of_children,
          Father: data.father_name,
          Mother: data.mother_name,
          Gender: data.gender == 1 ? "Male" : "Female",
          "Employment of Spouse": data.no_of_spouse,
        };

        // Add the "other_text" and "other_value" properties to the table_data object
        if (data.other_text && data.other_value) {
          this.table_data[caps(data.other_text)] = caps(data.other_value);
        }
      } catch (error) {
        console.error("Error retrieving personal information:", error);
        // Handle the error appropriately (e.g., show an error message)
      }
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
    close_personal_info() {
      this.personal_info = false;
      this.errors = [];
      setTimeout(() => {
        this.$emit("close-popup");
      }, 1000);
    },
    save_personal_info() {
      let payload = {
        ...this.personalItem,
        company_id: this.$auth?.user?.company?.id,
        employee_id: this.employeeId,
      };

      this.$axios
        .post(`personalinfo`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_personal_info();
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>

<style scoped>
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
</style>
