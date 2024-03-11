<template>
  <div class="mt-8">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-container>
      <v-row class="pl-1 mt-0 mb-0">
        <v-col cols="12">
          <v-card class="mb-5 rounded-md" elevation="0">
            <v-toolbar
              elevation="1"
              class="rounded-md"
              style="border-radius: 5px 5px 0px 0px"
              dense
            >
              <span> Documents List</span>

              <v-spacer></v-spacer>
              <v-toolbar-items>
                <v-col class="toolbaritems-button-design">
                  <v-btn dark small class="primary" @click="addDocumentInfo">
                    Document&nbsp; <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </v-col>
              </v-toolbar-items>
            </v-toolbar>
            <v-data-table
              dense
              :headers="headers_table"
              :items="document_list"
              model-value="data.id"
              :loading="loading"
              :footer-props="{
                itemsPerPageOptions: [10, 50, 100, 500, 1000],
              }"
              class="elevation-1"
            >
              <template v-slot:item.sno="{ item, index }">
                {{ index + 1 }}
              </template>
              <template v-slot:item.title="{ item }">
                {{ item.title }}
              </template>
              <template v-slot:item.download="{ item }">
                <a
                  title="Download Profile Picture"
                  :href="getDonwloadLink(item.employee_id, item.attachment)"
                  ><v-icon color="violet">mdi-arrow-down-bold-circle</v-icon></a
                >
              </template>
              <template v-slot:item.delete="{ item }">
                <v-icon color="error" @click="delete_document(item.id)">
                  mdi-delete
                </v-icon>
              </template>
            </v-data-table>

            <!-- <table class="employee-table" style="border: 1px solid #ddd">
              <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                color="primary"></v-progress-linear>
              <tr>
                <th>Title</th>
                <th class="text-center">Download</th>
                <th class="text-center">Delete</th>
              </tr>
              <tr v-for="(d, index) in document_list" :key="index">
                <td>
                  <span>{{ d.title }}</span>
                </td>
                <td class="text-center">
                  <a :href="d.attachment" download target="_blank">
                    <v-icon color="primary"> mdi-download </v-icon>
                  </a>
                </td>
                <td class="text-center">
                  <v-icon color="error" @click="delete_document(d.id)">
                    mdi-delete
                  </v-icon>
                </td>
              </tr>
            </table> -->
          </v-card>
        </v-col>
      </v-row>
      <v-dialog persistent v-model="dialogUploadDocuments" width="800px">
        <v-card>
          <v-card-title dense class="popup_background">
            Documents
            <v-spacer></v-spacer>
            <v-icon @click="dialogUploadDocuments = false" outlined dark fill>
              mdi mdi-close-circle
            </v-icon>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" class="text-right"> </v-col>

                <v-col cols="12" class="text-right">
                  <v-form
                    v-if="displayForm"
                    class="mt-5"
                    ref="form"
                    method="post"
                    v-model="valid"
                    lazy-validation
                  >
                    <v-row v-for="(d, index) in Document.items" :key="index">
                      <v-col cols="5">
                        <!-- <label for="" class="py-2"
                        >Title <span color="error"></span
                      ></label> -->

                        <v-text-field
                          small
                          type="text"
                          class="form-control"
                          label="Title"
                          dense
                          outlined
                          v-model="d.title"
                          :rules="TitleRules"
                        ></v-text-field>
                        <span
                          v-if="errors && errors.title"
                          class="text-danger mt-2"
                          >{{ errors.title[0] }}</span
                        >
                      </v-col>
                      <v-col cols="5">
                        <div class="form-group">
                          <!-- <label for="" class="px-8 py-2"
                          >Select File <span color="error"></span
                        ></label> -->
                          <v-file-input
                            label="Attachment"
                            dense
                            outlined
                            v-model="d.file"
                            placeholder="Upload your file"
                            :rules="FileRules"
                          >
                            <template v-slot:selection="{ text }">
                              <v-chip v-if="text" small label color="primary">
                                {{ text }}
                              </v-chip>
                            </template>
                          </v-file-input>

                          <span
                            v-if="errors && errors.attachment"
                            class="text-danger mt-2"
                            >{{ errors.attachment[0] }}</span
                          >
                        </div>
                      </v-col>
                      <v-col cols="2">
                        <div class="form-group">
                          <v-icon
                            v-if="Document.items.length - 1 == index"
                            class="mt-1"
                            size="40"
                            color="primary"
                            @click="addDocumentFile"
                            >mdi-plus-circle</v-icon
                          >
                          <v-btn
                            v-else
                            dark
                            class="error mt-2"
                            fab
                            @click="removeItem(index)"
                            x-small
                          >
                            <v-icon>mdi-delete</v-icon>
                          </v-btn>
                        </div>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" class="text-right">
                        <v-btn
                          :disabled="!Document.items.length"
                          class="primary"
                          small
                          @click="save_document_info"
                          >Save</v-btn
                        >
                      </v-col>
                    </v-row>
                  </v-form>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-container>
  </div>
</template>

<script>
export default {
  props: ["employeeId"],
  data() {
    return {
      dialogUploadDocuments: false,
      loading: false,
      snackbar: false,
      valid: false,
      displayForm: false,
      documents: false,
      response: "",
      errors: [],
      FileRules: [
        (value) =>
          !value ||
          value.size < 200000 ||
          "File size should be less than 200 KB!",
      ],
      TitleRules: [(v) => !!v || "Title is required"],
      Document: {
        items: [{ title: "", file: "" }],
      },
      document_list: [],
      headers_table: [
        {
          text: "#",
          align: "left",
          sortable: false,
          key: "sno",
          value: "sno",
        },
        {
          text: "Title",
          align: "left",
          sortable: false,
          key: "title",
          value: "title",
        },
        {
          text: "Download",
          align: "left",
          sortable: false,
          key: "frequency",
          value: "download",
        },
        {
          text: "Delete",
          align: "left",
          sortable: false,
          key: "time",
          value: "delete",
        },
      ],
    };
  },
  created() {
    this.getInfo(this.employeeId);
  },
  methods: {
    getInfo(id) {
      this.loading = true;
      this.$axios.get(`documentinfo/${id}`).then(({ data }) => {
        this.document_list = data;
        this.loading = false;
      });
    },
    can(item) {
      return true;
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    addDocumentInfo() {
      this.dialogUploadDocuments = true;
      // this.Document.items.push({
      //   title: "",
      //   file: "",
      // });
      this.valid = true;
      this.Document.items = [{ title: "", file: "" }];
      this.displayForm = true;
    },

    addDocumentFile() {
      this.Document.items.push({
        title: "",
        file: "",
      });
    },

    save_document_info() {
      if (!this.$refs.form.validate()) {
        alert("Enter required fields!");
        return;
      }
      this.loading = true;
      let options = {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      };
      let payload = new FormData();

      this.Document.items.forEach((e) => {
        payload.append(`items[][title]`, e.title);
        payload.append(`items[][file]`, e.file || {});
      });

      payload.append(`company_id`, this.$auth?.user?.company?.id);
      payload.append(`employee_id`, this.employeeId);

      this.$axios
        .post(`documentinfo`, payload, options)
        .then(({ data }) => {
          this.dialogUploadDocuments = false;
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Document saved successfully"; //data.message;
            this.getDocumentInfo(this.employeeId);

            // this.close_document_info();
            // this.displayForm = false;
            this.loading = false;
          }
        })
        .catch((e) => console.log(e));
    },
    getDonwloadLink(pic, file_name) {
      return (
        process.env.BACKEND_URL +
        "/download-emp-documents/" +
        pic +
        "/" +
        file_name
      );
    },
    getDocumentInfo(id) {
      this.loading = true;
      this.$axios.get(`documentinfo/${id}`).then(({ data }) => {
        this.document_list = data;
        this.documents = false;
        this.loading = false;
      });
    },

    close_document_info() {
      this.documents = false;
      this.errors = [];
      this.$emit("close-popup");
    },

    removeItem(index) {
      this.Document.items.splice(index, 1);
      //this.displayForm = false;
    },

    delete_document(id) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`documentinfo/${id}`)
          .then(({ data }) => {
            this.loading = false;

            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.errors = [];
              this.snackbar = true;
              this.response = data.message;
              this.getDocumentInfo(this.employeeId);
              this.close_document_info();
            }
          })
          .catch((e) => console.log(e));
    },
  },
};
</script>
