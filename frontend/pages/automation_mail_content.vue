<template>
  <div v-if="can(`automation_contnet_access`)">
    <v-dialog v-model="dialogNew" width="60%">
      <v-card>
        <v-card-title dense class="popup_background">
          Modify Mail Content
          <v-spacer></v-spacer>
          <v-icon @click="dialogNew = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-row class="pt-5">
            <v-col md="6"
              ><v-text-field
                dense
                small
                outlined
                v-model="editedItem.name"
                label="Name or Title"
                disabled
              >
              </v-text-field
            ></v-col>
            <v-col class="text-end" md="6">
              <v-btn color="primary" @click="save"> Update </v-btn>
            </v-col>
            <v-col md="12">
              <ClientOnly v-if="editedItem.name == 'email'">
                <tiptap-vuetify
                  class="tiptap-icon"
                  v-model="editedItem.content"
                  :extensions="extensions"
                  v-scroll.self="onScroll"
                  height="400"
                  :toolbar-attributes="{
                    color: 'background red--text',
                  }"
                />
                <template #placeholder> Loading... </template>
              </ClientOnly>

              <v-textarea
                v-else
                v-model="editedItem.content"
                outlined
                :label="editedItem.name"
                value=" "
              ></v-textarea>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-row>
      <v-col md="12">
        <v-data-table
          v-model="ids"
          item-key="id"
          :headers="headers"
          :items="data"
          :loading="loading"
          :options.sync="options"
          :footer-props="{
            itemsPerPageOptions: [50, 100, 500, 1000],
          }"
          class="elevation-1"
        >
          <template v-slot:top>
            <v-card class="mb-5 rounded-md mt-3" elevation="0">
              <v-toolbar
                class="rounded-md"
                style="border-radius: 5px 5px 0px 0px"
                dense
                flat
              >
                <span> Mail Content List </span>
                <!-- <v-tooltip top color="primary">
                  <template v-slot:activator="{ on, attrs }"> -->
                <v-btn
                  dense
                  class="ma-0 px-0"
                  x-small
                  :ripple="false"
                  text
                  title="Add Role"
                  @click="getDataFromApi()"
                >
                  <v-icon class="ml-2" dark>mdi mdi-reload</v-icon>
                </v-btn>
                <!-- </template>
                  <span>Reload</span>
                </v-tooltip> -->
                <v-spacer></v-spacer>
                <v-toolbar-items>
                  <v-col> </v-col>
                </v-toolbar-items>
              </v-toolbar>
            </v-card>
          </template>
          <template v-slot:item.sno="{ item }">
            {{
              currentPage
                ? (currentPage - 1) * perPage +
                  (cumulativeIndex + data.indexOf(item))
                : ""
            }}</template
          >
          <template v-slot:item.content="{ item }">
            <div v-html="item.content" class="text-break-dot1"></div>
          </template>
          <template v-slot:item.updated_at="{ item }">
            {{ $dateFormat.format1(item.updated_at) }}
          </template>
          <template
            v-if="can(`automation_contnet_edit`)"
            v-slot:item.action="{ item }"
          >
            <v-icon
              v-if="can('settings_roles_edit')"
              color="secondary"
              small
              class="mr-2"
              @click="editItem(item)"
            >
              mdi-pencil
            </v-icon>
          </template>
          <template v-slot:no-data>
            <!-- <v-btn color="background" @click="initialize">Reset</v-btn> -->
          </template>
        </v-data-table></v-col
      >
    </v-row>
  </div>

  <NoAccess v-else />
</template>
<script>
import {
  TiptapVuetify,
  Heading,
  Bold,
  Italic,
  Strike,
  Underline,
  Paragraph,
  BulletList,
  OrderedList,
  ListItem,
  Blockquote,
  History,
} from "tiptap-vuetify";

export default {
  components: {
    TiptapVuetify,
  },
  data: () => ({
    extensions: [
      History,
      Blockquote,
      Underline,
      Strike,
      Italic,
      ListItem,
      BulletList,
      OrderedList,
      [
        Heading,
        {
          options: {
            levels: [1, 2, 3],
          },
        },
      ],
      Bold,
      Paragraph,
    ],
    endpoint: "mail_content",
    editedItem: {
      content: "",
    },
    perPage: 10,
    cumulativeIndex: 1,
    currentPage: 1,
    totalRowsCount: 0,
    dialogNew: false,
    options: {},
    Model: "Role",
    endpoint: "mail_content",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      {
        text: "#",
        align: "left",
        sortable: true,
        key: "sno",
        value: "sno",
      },
      {
        text: "Title",
        align: "left",
        sortable: true,
        key: "name",
        value: "name",
      },
      {
        text: "Content",
        align: "left",
        sortable: true,
        key: "content",
        value: "content",
      },
      {
        text: "Created",
        align: "left",
        sortable: true,
        key: "updated_at",
        value: "updated_at",
      },
      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: { name: "", description: "" },
    defaultItem: { name: "", description: "" },
    response: "",
    data: [],
    errors: [],
  }),
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  created() {
    this.loading = true;

    //permissions
    this.getDataFromApi();
  },

  methods: {
    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id,
        },
      };
      this.currentPage = page;
      this.perPage = itemsPerPage;
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.total = data.total;
        this.loading = false;
        this.totalRowsCount = data.total;
      });
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    onScroll() {
      this.scrollInvoked++;
    },
    editItem(item) {
      this.dialogNew = true;
      this.editedItem = item;
      this.editedIndex = item.id;
    },
    save() {
      let payload = {
        name: this.editedItem.name,
        content: this.editedItem.content,
        branch_id: 0,
        company_id: this.$auth.user.company.id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              // const index = this.data.findIndex(
              //   (item) => item.id == this.editedItem.id
              // );
              // this.data.splice(index, 1, {
              //   id: this.editedItem.id,
              //   name: this.editedItem.name,
              // });
              this.snackbar = data.status;
              this.response = data.message;
              this.dialogNew = false;
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.errors = [];
              this.search = "";
              this.dialogNew = false;
            }
          })
          .catch((res) => console.log(res));
      }
    },
  },
};
</script>
<style>
.tiptap-vuetify-editor__content {
  min-height: 400px !important;
}

.ProseMirror .ProseMirror-focused {
  height: 400px !important;
}

.tiptap-icon .v-icon {
  color: white !important;
}

.tiptap-icon .v-btn--icon {
  color: white !important;
}

.breakthewords {
  display: -webkit-box;
  font-size: 12px !important;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
