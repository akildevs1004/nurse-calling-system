<template>
  <div>
    <div class="text-center">
      <v-dialog persistent v-model="dialog" width="500">
        <template v-slot:activator="{ on, attrs }">
          <v-btn color="red lighten-2" dark v-bind="attrs" v-on="on">
            Click Me
          </v-btn>
        </template>

        <v-card class="mx-auto" max-width="500">
          <v-toolbar class="background white--text" flat>
            Get started quickly with pre-filled defaults

            <!-- "Save time by using default values"
                "Get started quickly with pre-filled defaults"
                "Effortlessly populate fields with default values"
                "Skip manual entry with default values"
                "Reduce data entry with pre-set defaults"
                "Accelerate form completion with default values"
                "Simplify data entry with default values"
                "Make form filling easier with pre-populated defaults"
                "Streamline data input with default values"  -->
          </v-toolbar>
        

          <v-window v-model="step" class="mx-2">
            <v-window-item :value="1">
              <v-card-text>
                <v-text-field
                  label="Department 1"
                  value="Department 1"
                ></v-text-field>
                 <v-text-field
                  label="Department 2"
                  value="Department 2"
                ></v-text-field>
                 <v-text-field
                  label="Department 3"
                  value="Department 3"
                ></v-text-field>
                <span class="text-caption grey--text text--darken-1"> </span>
              </v-card-text>
            </v-window-item>

            <v-window-item :value="2">
              <v-card-text>
                <v-text-field
                  label="Sub Department 1"
                  value="Sub Department 1"
                ></v-text-field>
                 <v-text-field
                  label="Sub Department 2"
                  value="Sub Department 2"
                ></v-text-field>
                 <v-text-field
                  label="Sub Department 3"
                  value="Sub Department 4"
                ></v-text-field>

                <span class="text-caption grey--text text--darken-1"> </span>
              </v-card-text>
            </v-window-item>

            <v-window-item :value="3">
              <v-card-text>
                <v-text-field
                  label="Designation 1"
                  value="Designation 1"
                ></v-text-field>

                <v-text-field
                  label="Designation 2"
                  value="Designation 2"
                ></v-text-field>

                <v-text-field
                  label="Designation 3"
                  value="Designation 3"
                ></v-text-field>

                <span class="text-caption grey--text text--darken-1"> </span>
              </v-card-text>
            </v-window-item>

            <v-window-item :value="4">
              <v-card-text>
                <v-text-field label="Role 1" value="Role 1"></v-text-field>
                <v-text-field label="Role 2" value="Role 2"></v-text-field>
                <v-text-field label="Role 3" value="Role 3"></v-text-field>

                <span class="text-caption grey--text text--darken-1"> </span>
              </v-card-text>
            </v-window-item>

            <!-- <v-window-item :value="3">
              <div class="pa-4 text-center">
                <v-img
                  class="mb-4"
                  contain
                  height="128"
                  src="https://cdn.vuetifyjs.com/images/logos/v.svg"
                ></v-img>
                <h3 class="text-h6 font-weight-light mb-2">
                  Welcome to Vuetify
                </h3>
                <span class="text-caption grey--text"
                  >Thanks for signing up!</span
                >
              </div>
            </v-window-item> -->
          </v-window>

          <v-card-actions>
            <v-btn :disabled="step === 1" text @click="step--">
              Back
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
              :disabled="step === 4"
              color="primary"
              depressed
              @click="step++"
            >
              Next
            </v-btn>

            <v-btn v-if="step === 5" color="primary" depressed @click="step">
              Next
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    dialog: false,
    step: 1
  }),

  computed: {
    currentTitle() {
      switch (this.step) {
        case 1:
          return "Department";
        case 2:
          return "Sub department";
        case 3:
          return "Designation";
        default:
          return "Role";
      }
    }
  }
};
</script>
