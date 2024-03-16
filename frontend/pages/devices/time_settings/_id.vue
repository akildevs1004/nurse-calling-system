<template>
  <div>
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
                  outlined
                  v-model="dialog_time_start"
                  dense
                  :items="seasons"
                ></v-select>
                <!-- <TimePickerCommon
                  label="Start Time"
                  @getTime="(value) => (dialog_time_start = value)"
                />-->
              </v-col>
              <v-col md="6">
                <v-select
                  outlined
                  v-model="dialog_time_end"
                  dense
                  :items="seasons"
                ></v-select>
                <!-- <TimePickerCommon
                  label="End Time"
                  @getTime="(value) => (dialog_time_end = value)"
                /> -->
              </v-col>
              <!-- <v-range-slider
                  :tick-labels="seasons"
                  :value="[0, 1]"
                  min="0"
                  max="24"
                  ticks="always"
                  tick-size="4"
                >
                  <template v-slot:thumb-label="props">
                    <v-icon dark> props.value </v-icon>
                  </template>
                </v-range-slider> -->

              <v-spacer></v-spacer>
              <v-btn dark color="violet" fill @click="updateTimeRange()"
                >Update</v-btn
              >
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-row no-gutters>
      <v-col md="6"></v-col>

      <v-col md="6" align-self="end" style="float: right">
        <CustomFilter
          @filter-attr="filterAttr"
          :defaultFilterType="1"
          style="float: right"
        />
      </v-col>
    </v-row>
    <table style="width: 50%">
      <thead>
        <tr>
          <th></th>
          <th v-for="slot in timeSlots" :key="slot">{{ slot }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(day, index) in days" :key="index">
          <td>{{ day }}</td>
          <td
            v-for="(slot, slotIndex) in timeSlots"
            :key="slot"
            @click="toggleCellBackground(index, slotIndex)"
            :style="{
              backgroundColor: selectedCells.has(index + '-' + slotIndex)
                ? 'green'
                : 'red',
            }"
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

    <v-row>
      <v-col md="12" class="text-end"
        ><v-btn @click="updateSettings()">Update</v-btn></v-col
      >
    </v-row>
  </div>
</template>

<script>
// import TimePickerCommon from "../../../components/Snippets/TimePickerCommon.vue";
export default {
  // components: {
  //   TimePickerCommon,
  // },
  data() {
    return {
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
    // mergeTimeSlots(schedule) {
    //   console.log(schedule.length);
    //   if (schedule.length <= 1) {
    //     return schedule; // Nothing to merge if there's only one or zero items
    //   }

    //   const mergedSchedule = [];
    //   let currentSlot = schedule[0];

    //   for (let i = 1; i < schedule.length; i++) {
    //     const nextSlot = schedule[i];

    //     const currentClose = new Date(`2023-10-02T${currentSlot.time_close}`);
    //     const nextOpen = new Date(`2023-10-02T${nextSlot.time_open}`);
    //     console.log(currentClose, nextOpen);
    //     if (currentClose == nextOpen) {
    //       // Merge the current slot with the next slot
    //       currentSlot.time_close = nextSlot.time_close;
    //     } else {
    //       // They are not overlapping, so add the current slot to the merged schedule
    //       mergedSchedule.push(currentSlot);
    //       currentSlot = nextSlot;
    //     }

    //     currentSlot = nextSlot;
    //   }

    //   // Add the last slot to the merged schedule
    //   mergedSchedule.push(currentSlot);

    //   return mergedSchedule;
    // },
    updateSettings() {
      // Convert the Set to an array
      const myArray = Array.from(this.selectedCells);

      // Convert the array to a JSON string
      const jsonString = JSON.stringify(myArray);

      let device_id = this.$route.params.id;
      let options = {
        params: {
          per_page: 1000,
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
        .post(`/devices_active_settings/${device_id}`, options.params)
        .then(({ data }) => {});
    },
    updateSettings1() {
      let timeTable = [];

      let mondaySchedule = [
        {
          time_open: "10:00",
          time_close: "10:30",
        },
        {
          time_open: "10:30",
          time_close: "11:30",
        },
        {
          time_open: "11:20",
          time_close: "12:30",
        },
      ];

      timeTable.push({ day: "Monday", schedule: mondaySchedule });

      // You can continue this pattern for the remaining days...

      // Display the class timetable
      console.log(timeTable);

      //let mergeTimeSlotsFinal = this.mergeTimeSlots(timeTable[0].schedule);
      //console.log(mergeTimeSlotsFinal);

      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
        },
      };
      // this.$axios
      //   .get(`/scheduled_employees_with_type`, options)
      //   .then(({ data }) => {
      //     this.scheduled_employees = data;
      //     this.scheduled_employees.unshift({
      //       system_user_id: "",
      //       name_with_user_id: "All Employees",
      //     });
      //   });
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
      let device_id = this.$route.params.id;
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
        .get(`/get_device_active_settings/${device_id}`, options)
        .then(({ data }) => {
          data.forEach((element) => {
            console.log(element);
            const myArray = element.split("-");
            this.toggleCellBackground(myArray[0], myArray[1]);

            // this.selectedCells.add(element);
          });

          console.log(this.selectedCells);
        });
    },
    updateTimeRange() {
      let timeArray = this.generateTimeSlotsRange(
        this.dialog_time_start,
        this.dialog_time_end + 1
      );
      timeArray.forEach((element) => {
        let columnIndex = this.timeSlots.findIndex((item) => item == element);

        this.toggleCellBackground(this.day_index, columnIndex, true);
      });
      console.log(timeArray);
    },
    toggleCellBackground(rowIndex, columnIndex, isPopup = false) {
      const refName = `cell_${rowIndex}_${columnIndex}`;
      const printableContent = document.getElementById(refName);

      const key = `${rowIndex}-${columnIndex}`;

      if (this.selectedCells.has(key)) {
        if (!isPopup) {
          this.selectedCells.delete(key);
          if (printableContent) printableContent.style.backgroundColor = "red";
        }
      } else {
        this.selectedCells.add(key);
        const randomColor =
          "#" + Math.floor(Math.random() * 16777215).toString(16);

        if (printableContent) printableContent.style.backgroundColor = "green";
      }

      console.log(this.selectedCells);
    },
    isSelected(rowIndex, columnIndex) {
      return this.selectedCells.has(`${rowIndex}-${columnIndex}`);
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
  background-color: red;
  border-right: 1px solid #ec6060;
}
.selected {
  background-color: green;
  border-right: 1px solid green;
}
.selected-cell {
  background-color: green; /* Change this color to the desired highlight color */
}
</style>
