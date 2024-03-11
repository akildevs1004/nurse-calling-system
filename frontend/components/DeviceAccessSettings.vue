<template>
  <div style="background-color: #fff !important">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="dialogManualInput" width="300px">
      <v-card>
        <v-card-title dense class="popup_background">
          <span>Select Hour Range</span>
          <v-spacer></v-spacer>
          <v-icon @click="dialogManualInput = false" outlined>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>

        <v-card-text>
          <v-container style="min-height: 100px">
            <v-row>
              <v-col md="6">
                <v-select
                  height="20px"
                  outlined
                  v-model="dialog_time_start"
                  dense
                  :items="seasons"
                ></v-select>
              </v-col>
              <v-col md="6">
                <v-select
                  outlined
                  v-model="dialog_time_end"
                  dense
                  :items="seasons"
                ></v-select>
              </v-col>

              <v-spacer></v-spacer>
              <v-btn dark color="violet" fill @click="selectTimeRange()"
                >Update</v-btn
              >
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-row no-gutters>
      <v-col md="6"></v-col>

      <v-col
        md="6"
        align-self="end"
        style="float: right; text-align: right"
        class="mb-3"
      >
        <v-btn dense dark color="violet" fill @click="clearSelection()">
          Cancel
        </v-btn>
        <v-btn
          class="ml-3 mr-3"
          dense
          dark
          color="violet"
          fill
          @click="updateSettings()"
          >Update</v-btn
        >

        <CustomFilter
          class="mt-2"
          @filter-attr="filterAttr"
          :defaultFilterType="1"
          style="float: right"
          :height="35"
        />
      </v-col>
    </v-row>
    <table style="width: 100%">
      <thead>
        <tr>
          <th></th>
          <th v-for="slot in timeSlots" :key="slot" class="settings-time">
            {{ slot }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(day, index) in days" :key="index">
          <td>{{ day }}</td>
          <td
            v-for="(slot, slotIndex) in timeSlots"
            :key="slot"
            @click="toggleCellBackground(index, slotIndex)"
            :class="
              selectedCells.has(index + '-' + slotIndex)
                ? 'tdcell selected'
                : 'tdcell un-selected'
            "
            :id="`cell_${index}_${slotIndex}`"
          ></td>
          <td>
            <img
              @click="manualINputSettings(index)"
              title="Manual Input"
              src="/../../icons/always_open.png"
              style="width: 33px"
            />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
// import TimePickerCommon from "../../../components/Snippets/TimePickerCommon.vue";
export default {
  props: ["device_id"],
  //   components: {
  //     TimePickerCommon,
  //   },
  data() {
    return {
      snackbar: false,
      response: "",
      dialog_time_start: "",

      dialog_time_end: "",
      dialogManualInput: false,
      seasons: [
        0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19,
        20, 21, 22, 23, 24,
      ],
      span_time_minutes: 30,
      days: [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ],
      timeSlots: this.generateTimeSlots(24), // Generate time slots for 24 hours with 30-minute intervals
      selectedCells: new Set(),
      array: [],

      date_from: "",
      date_to: "",
      filterType: "",
      day_index: "",
    };
  },
  mounted() {},
  created() {
    const today = new Date();

    this.date_from = today.toISOString().slice(0, 10);
    this.date_to = today.toISOString().slice(0, 10);
    this.getDataFromApi();
  },
  methods: {
    manualINputSettings(day_index) {
      this.day_index = day_index;
      this.dialogManualInput = true;
    },
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;

      this.getDataFromApi();
      //this.filterType = "Monthly";
    },

    updateSettings() {
      console.log(this.selectedCells);

      const myArray = Array.from(this.selectedCells);

      const jsonString = JSON.stringify(myArray);

      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          selected_matrix: jsonString,
          input_days: this.days,
          input_time_slots: this.timeSlots,
          date_from: this.date_from,
          date_to: this.date_to,
          span_time_minutes: this.span_time_minutes,
        },
      };
      this.$axios
        .post(
          `/update_devices_active_settings/${this.device_id}`,
          options.params
        )
        .then(({ data }) => {
          if (data.status) {
            this.$emit("closepopup");
          } else {
            this.snackbar = true;
            this.response = "Error Occured";
          }
        });
    },

    generateTimeSlots(hours) {
      let interval = 30; // this.span_time_minutes;
      this.span_time_minutes = 30;
      const timeSlots = [];
      for (let hour = 0; hour < hours; hour++) {
        for (let minute = 0; minute < 60; minute += interval) {
          const formattedHour = `${hour.toString().padStart(2, "0")}:${minute
            .toString()
            .padStart(2, "0")}`;
          timeSlots.push(formattedHour);
        }
      }
      return timeSlots;
    },
    generateTimeSlotsRange(start, end) {
      let interval = 30; // this.span_time_minutes;
      this.span_time_minutes = 30;
      const timeSlots = [];
      for (let hour = start; hour < end; hour++) {
        for (let minute = 0; minute < 60; minute += interval) {
          const formattedHour = `${hour.toString().padStart(2, "0")}:${minute
            .toString()
            .padStart(2, "0")}`;
          timeSlots.push(formattedHour);
        }
      }
      return timeSlots;
    },
    getDataFromApi() {
      if (!this.device_id) return;
      //  let device_id = this.$route.params.id;
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          input_time_slots: this.timeSlots,
          date_from: this.date_from,
          date_to: this.date_to,
        },
      };
      this.$axios
        .get(`/get_device_active_settings/${this.device_id}`, options)
        .then(({ data }) => {
          data.forEach((element) => {
            console.log(element);
            const myArray = element.split("-");
            this.toggleCellBackground(myArray[0], myArray[1]);
          });
        });
    },
    selectTimeRange() {
      let timeArray = this.generateTimeSlotsRange(
        this.dialog_time_start,
        this.dialog_time_end
      );
      timeArray.forEach((element) => {
        let columnIndex = this.timeSlots.findIndex((item) => item == element);

        this.toggleCellBackground(this.day_index, columnIndex, true);
      });
      this.dialogManualInput = false;
    },
    // updateTimeRange() {
    //   let timeArray = this.generateTimeSlotsRange(
    //     this.dialog_time_start,
    //     this.dialog_time_end + 1
    //   );
    //   timeArray.forEach((element) => {
    //     let columnIndex = this.timeSlots.findIndex((item) => item == element);

    //     this.toggleCellBackground(this.day_index, columnIndex, true);
    //   });
    //   this.dialogManualInput = false;
    // },
    toggleCellBackground(rowIndex, columnIndex, isPopup = false) {
      const refName = `cell_${rowIndex}_${columnIndex}`;
      const printableContent = document.getElementById(refName);

      const key = `${rowIndex}-${columnIndex}`;

      if (this.selectedCells.has(key)) {
        if (!isPopup) {
          this.selectedCells.delete(key);
          if (printableContent) {
            printableContent.classList.add("un-selected");
            printableContent.classList.remove("selected");
          }

          // printableContent.style.backgroundColor = "#DDD";
        }
      } else {
        this.selectedCells.add(key);

        if (printableContent) {
          printableContent.classList.add("selected");
          printableContent.classList.remove("un-selected");
        }
      }
    },
    isSelected(rowIndex, columnIndex) {
      return this.selectedCells.has(`${rowIndex}-${columnIndex}`);
    },
    clearSelection() {
      const elementsArray = document.getElementsByClassName("tdcell");

      elementsArray.forEach((element, index, array) => {
        element.classList.remove("selected");
        element.classList.add("un-selected");
      });
      this.selectedCells = new Set();
    },
  },
};
</script>

<style scoped>
table {
  border-collapse: collapse;
  width: 100%;
  overflow: auto;
}
th {
  font-size: 13px;
  font-weight: 300;
}
th:nth-child(even) {
  font-weight: bold;
}
th,
td {
  border: 1px solid #dddddd;
  text-align: center;
}

th {
  background-color: #f2f2f2;
}

.un-selected {
  background-color: #ddd;
  border: 1px solid #fff;
}
.selected {
  background-color: #60ad60;
  border: 1px solid #fff;
}
.selected-cell {
  background-color: green; /* Change this color to the desired highlight color */
}

/*#60ad60 */

.settings-time {
  font-size: 10px;
}
.v-input__control .v-input__slot,
.v-input__slot,
input {
  min-height: auto !important;
  display: flex !important;
  align-items: center !important;
}
</style>
