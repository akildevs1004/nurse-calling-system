<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-autocomplete
          @change="getRecord"
          label="Select Page"
          v-model="page"
          :items="[`dashboard1`, `dashboard2`]"
        >
        </v-autocomplete>
      </v-col>
      <v-col
        v-for="(card, index) in cardData"
        :key="index"
        :cols="card.cols"
        :sm="card.sm"
        :md="card.md"
      >
        <v-card
          :color="card.color"
          dark
          dense
          style="border-radius: 15px !important"
        >
          <div class="text-right px-2">
            <v-icon small @click="editCard(index)">mdi-pencil</v-icon>
            <v-icon small @click="deleteCard(index)">mdi-delete</v-icon>
          </div>
          <div class="text-center pa-5">
            <h1>{{ card.value }}</h1>
            <p>{{ card.title }}</p>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card
          @click="addCard"
          :color="'background'"
          dark
          dense
          style="border-radius: 15px !important"
        >
          <div class="text-right px-2">
            <v-icon disabled color="background" small>mdi-pencil</v-icon>
          </div>
          <div class="text-center pa-5">
            <h1><v-icon>mdi-plus-circle-outline</v-icon></h1>
            <p>Add New Card</p>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col>
        <v-dialog persistent v-model="editDialog" max-width="900">
          <v-card>
            <v-card-title
              >Edit Card <v-spacer></v-spacer>
              <v-icon color="black" @click="closeEdit">mdi-close</v-icon>
              <v-icon color="black" @click="saveEdit">mdi-database</v-icon>
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col>
                  <v-text-field
                    @input="reflectChange"
                    v-model="editedCard.cols"
                    label="Default Col"
                  ></v-text-field>
                </v-col>
                <v-col>
                  <v-text-field
                    @input="reflectChange"
                    v-model="editedCard.md"
                    label="Medium Col"
                  ></v-text-field>
                </v-col>
                <v-col>
                  <v-text-field
                    @input="reflectChange"
                    v-model="editedCard.sm"
                    label="Small Col"
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-row>
                <v-col>
                  <v-text-field
                    @input="reflectChange"
                    v-model="editedCard.title"
                    label="Title"
                  ></v-text-field>
                </v-col>
                <v-col>
                  <!-- <v-text-field
                    @input="reflectChange"
                    v-model="editedCard.value"
                    label="Value"
                  ></v-text-field> -->

                  <v-autocomplete
                    @input="reflectChange"
                    v-model="editedCard.value"
                    item-text="title"
                    item-value="value"
                    :items="[
                      {
                        title: `Total In`,
                        value: `totalIn`,
                      },
                      {
                        title: `Total Out`,
                        value: `totalOut`,
                      },
                      {
                        title: `Emplopyee Count`,
                        value: `employeeCount`,
                      },
                      {
                        title: `Present`,
                        value: `presentCount`,
                      },
                      {
                        title: `Absent`,
                        value: `absentCount`,
                      },
                      {
                        title: `Missing`,
                        value: `missingCount`,
                      },

                      {
                        title: `Week Off`,
                        value: `offCount`,
                      },
                      {
                        title: `Holiday`,
                        value: `holidayCount`,
                      },
                      {
                        title: `Leave`,
                        value: `leaveCount`,
                      },
                      {
                        title: `Vaccation`,
                        value: `vaccationCount`,
                      },
                    ]"
                  >
                  </v-autocomplete>
                </v-col>
              </v-row>

              <v-row>
                <v-col>
                  Color Picker

                  <v-color-picker
                    @input="reflectChange"
                    v-model="editedCard.color"
                    dot-size="20"
                    show-swatches
                    mode="hexa"
                    swatches-max-height="250"
                  ></v-color-picker>
                </v-col>
                <v-col>
                  Card Preview
                  <v-card
                    :color="editedCard.color"
                    dark
                    dense
                    style="border-radius: 15px !important"
                  >
                    <div class="text-right px-2">
                      <v-icon disabled small>mdi-pencil</v-icon>
                      <v-icon disabled small>mdi-delete</v-icon>
                    </div>
                    <div class="text-center pa-5">
                      <h1>[{{ editedCard.value }}]</h1>
                      <p>{{ editedCard.title }}</p>
                    </div>
                  </v-card>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-dialog>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  props: ["propPage"],
  data() {
    return {
      page: null,
      editDialog: false,
      editedCard: {
        title: "New Card",
        value: "employeeCount",
        color: "#5fafa3",
        icon: "mdi mdi-account",
        cols: "12",
        sm: "6",
        md: "3",
      },
      editIndex: null,

      cardData: [],
      displayValueCount: 0,
    };
  },
  created() {
    this.page = this.propPage;
    this.getRecord();
  },

  methods: {
    getRecord() {
      let payload = {
        page: this.page,
        type: "card",
        company_id: this.$auth.user.company_id,
      };

      let options = {
        params: payload,
      };

      this.$axios
        .get("theme", options)
        .then(({ data }) => {
          this.cardData = data;
        })
        .catch((e) => console.log(e));
    },
    addCard() {
      this.cardData.push(this.editedCard);
    },

    editCard(index) {
      this.editDialog = true;
      this.editIndex = index;
      this.editedCard = { ...this.cardData[index] };
    },
    deleteCard(index) {
      this.cardData.splice(index, 1);
    },
    reflectChange() {
      this.cardData[this.editIndex] = { ...this.editedCard };
    },
    reflectCount() {
      let payload = {
        value: this.editedCard.value,
        company_id: this.$auth.user.company_id,
      };

      this.$axios
        .get("theme_count", { params: payload })
        .then(({ data }) => {
          this.displayValueCount = data;
        })
        .catch((e) => console.log(e));
    },
    saveEdit() {
      this.reflectChange();

      let payload = {
        page: this.page,
        type: "card",
        style: this.cardData,
        company_id: this.$auth.user.company_id,
      };

      this.$axios
        .post("theme", payload)
        .then(({ data }) => {
          alert("Data inserted");
          this.getRecord();
          this.closeEdit();
        })
        .catch((e) => console.log(e));
    },
    closeEdit() {
      this.editDialog = false;
      this.editedCard = {
        title: "New Card",
        value: "0",
        color: "#4A79DBED",
        icon: "mdi mdi-account",
        cols: "12",
        sm: "6",
        md: "3",
      };
      this.editIndex = null;
    },
  },
};
</script>
