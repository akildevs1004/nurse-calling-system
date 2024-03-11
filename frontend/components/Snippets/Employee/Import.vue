<template>
  <div>
    <v-snackbar v-model="snackbar" small top="top" :color="color">
      {{ response }}
    </v-snackbar>
    <v-dialog persistent v-model="dialog" max-width="500px">
      <template v-slot:activator="{ on, attrs }">
        <v-icon
          title="Import File"
          @click="dialog = true"
          right
          dark
          color="black"
          size="x-large"
          v-bind="attrs"
          v-on="on"
          >mdi-file-import</v-icon
        >
      </template>
      <v-card>
        <v-card-title dense class="popup_background">
          Import Employee
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col>
                <v-select
                  :hide-details="true"
                  clearable
                  item-value="id"
                  item-text="name"
                  v-model="import_branch_id"
                  outlined
                  dense
                  :items="branches_list"
                ></v-select>
              </v-col>
              <v-col cols="12">
                <v-file-input
                  accept="text/csv"
                  v-model="files"
                  placeholder="Upload your file"
                  label="File"
                  prepend-icon="mdi-paperclip"
                >
                  <template v-slot:selection="{ text }">
                    <v-chip v-if="text" small label color="primary">
                      {{ text }}
                    </v-chip>
                  </template>
                </v-file-input>
                <br />
                <a href="/employees.csv" download> Download Sample</a>
                <br />
                <span v-if="errors && errors.length > 0" class="error--text">{{
                  errors[0]
                }}</span>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="error" small @click="close"> Cancel </v-btn>

          <v-btn
            class="primary"
            :loading="btnLoader"
            small
            @click="importEmployee"
            >Save</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Array,
    },
  },
  data: () => ({
    btnLoader: false,
    dialog: false,
    snackbar: false,
    response: null,
    errors: [],
    import_branch_id: "",
    branches_list: [],
    files: "",
    text: null,
    color: "background",
  }),
  async created() {
    this.branches_list = await this.$store.dispatch("branches_list");
  },
  methods: {
    importEmployee() {
      if (this.import_branch_id > 0) {
        let payload = new FormData();
        payload.append("employees", this.files);
        payload.append("company_id", this.$auth?.user?.company?.id);
        payload.append("branch_id", this.import_branch_id);
        let options = {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        };
        this.btnLoader = true;
        this.$axios
          .post("/employee/import", payload, options)
          .then(({ data }) => {
            this.btnLoader = false;
            if (!data.status) {
              this.errors = data.errors;
              payload.delete("employees");
            } else {
              this.errors = [];
              this.snackbar = true;
              this.response = "Employees imported successfully";
              this.$emit("success");
              this.close();
            }
          })
          .catch((e) => {
            if (e.toString().includes("Error: Network Error")) {
              this.errors = [
                "File is modified.Please cancel the current file and try again",
              ];
              this.btnLoader = false;
            }
          });
      } else {
        alert("Select Branch");
      }
    },
    close() {
      this.dialog = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
  },
};
</script>

<style scoped>
/* Add any custom styling here */
</style>
