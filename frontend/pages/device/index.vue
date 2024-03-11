<template>
  <div v-if="can(`automation_contnet_access`)">
    <v-dialog
      v-model="dialogAccessSettings"
      width="90%"
      style="background-color: #fff !important"
    >
      <v-card>
        <v-card-title dense class="popup_background">
          <span>Time Selection</span>
          <v-spacer></v-spacer>
          <v-icon @click="dialogAccessSettings = false" outlined>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <DeviceAccessSettings
              :key="popup_device_id"
              :device_id="popup_device_id"
              @closepopup="closepopup"
            />
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="uploadedUserInfoDialog" max-width="500px">
      <v-card :loading="loadingDeviceData">
        <v-card-title class="popup_background">
          <span>Find User ID on Device </span>
          <v-spacer></v-spacer>

          <v-icon outlined @click="uploadedUserInfoDialog = false"
            >mdi-close-circle</v-icon
          >
        </v-card-title>

        <v-card-text class="mt-2">
          <v-row>
            <v-col cols="6">
              <v-text-field
                append-icon="mdi-magnify"
                label="Employee Device ID"
                type="number"
                dense
                outlined
                v-model="inputFindDeviceUserId"
              >
              </v-text-field
            ></v-col>
            <v-col cols="6">
              <v-btn
                dense
                small
                class="primary mt-2"
                @click="getUserInfoFromDevice()"
              >
                Get Details</v-btn
              >
            </v-col>
          </v-row>

          <v-card
            v-for="(visitor, index) in visitorUploadedDevicesInfo"
            :key="'vs' + index"
          >
            <v-card-title style="font-size: 13px"
              >{{ ++index }}: Device: {{ visitor.deviceName }}
            </v-card-title>
            <v-card-text class="mt-2">
              <v-row
                class="100%"
                style="margin: auto; line-height: 36px"
                v-if="visitor.SDKresponseData.data"
              >
                <v-col cols="4" style="padding: 0px">
                  <v-img
                    style="
                      border-radius: 10%;
                      width: 100px;
                      max-width: 95%;

                      height: auto;
                      border: 1px solid #ddd;
                    "
                    :src="
                      visitor.SDKresponseData.data.faceImage
                        ? 'data:image/jpeg;base64, ' +
                          visitor.SDKresponseData.data.faceImage
                        : '/no-profile-image.jpg'
                    "
                  >
                  </v-img>
                </v-col>

                <v-col cols="8" style="padding: 0px">
                  <v-simple-table>
                    <tr>
                      <td>Name</td>
                      <td>: {{ visitor.SDKresponseData.data.name }}</td>
                    </tr>

                    <tr>
                      <td>System User Id</td>
                      <td>: {{ visitor.SDKresponseData.data.userCode }}</td>
                    </tr>

                    <tr>
                      <td>Expiry Date</td>
                      <td>: {{ visitor.SDKresponseData.data.expiry }}</td>
                    </tr>

                    <tr>
                      <td>Timezone Group Id</td>
                      <td>: {{ visitor.SDKresponseData.data.timeGroup }}</td>
                    </tr>
                  </v-simple-table>
                </v-col>
              </v-row>

              <div v-else>{{ visitor.SDKresponseData.message }}</div>

              <!-- <v-row>
                <v-col cols="12">
                  <v-btn
                    class="align-right"
                    style="float: right; color: #fff"
                    dense
                    small
                    color="red"
                    @click="deleteFromDevice(visitor)"
                    >Delete</v-btn
                  >
                </v-col>
              </v-row> -->
            </v-card-text>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="DialogDeviceSettings" max-width="800px">
      <v-card>
        <v-card-title class="popup_background">
          <span> Device Settings </span>
          <v-spacer></v-spacer>

          <v-icon outlined @click="DialogDeviceSettings = false"
            >mdi-close-circle</v-icon
          >
        </v-card-title>

        <v-card-text class="mt-2">
          <v-card :loading="loadingDeviceData">
            <v-card-text class="mt-2">
              <v-row class="100%" style="margin: auto; line-height: 36px">
                <v-col cols="12" style="padding: 0px">
                  <v-simple-table>
                    <tr>
                      <td colspan="2">
                        <v-btn
                          style="float: right"
                          dense
                          small
                          class="primary mt-2 align-right"
                          @click="
                            getDeviceSettginsFromSDK(deviceSettings.device_id)
                          "
                        >
                          Reload
                        </v-btn>
                        <br />
                        <div v-if="loadingDeviceData" style="color: red">
                          Connecting to Device. Please wait...
                        </div>
                        <div style="color: green">
                          {{ sdk_message }}
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>Device ID</td>
                      <td>
                        <v-text-field
                          :disabled="true"
                          class="pb-0"
                          v-model="deviceSettings.device_id"
                          placeholder="Device ID"
                          outlined
                          dense
                          label="Device ID  "
                        ></v-text-field>
                      </td>
                    </tr>
                    <!-- <tr>
                      <td>Device Model Name</td>
                      <td>
                        <v-text-field
                          :disabled="true"
                          :rules="device_model"
                          class="pb-0"
                          v-model="deviceSettings.name"
                          placeholder="Device Model"
                          outlined
                          dense
                          label="Device Model  "
                        ></v-text-field>
                      </td>
                    </tr> -->

                    <tr>
                      <td>Door</td>
                      <td>
                        <v-select
                          class="pb-0"
                          v-model="deviceSettings.door"
                          placeholder="Entry or exit"
                          :items="[
                            { name: 'Entry', value: 0 },
                            { name: 'Exit', value: 1 },
                          ]"
                          item-value="value"
                          item-text="name"
                          outlined
                          dense
                          label="Entry or exit"
                        ></v-select>
                      </td>
                    </tr>

                    <tr>
                      <td>Language</td>
                      <td>
                        <v-autocomplete
                          class="pb-0"
                          v-model="deviceSettings.language"
                          placeholder="language"
                          :items="[
                            { name: 'English', value: 2 },
                            { name: 'Arabic', value: 12 },
                            { name: 'Russian', value: 5 },
                            { name: 'Italian', value: 8 },
                            { name: 'Thai', value: 11 },

                            { name: 'Korean', value: 10 },
                            { name: 'Japanese', value: 9 },

                            { name: 'Spanish', value: 7 },
                            { name: 'Portuguese', value: 6 },

                            { name: 'French', value: 4 },
                            { name: 'Traditional Chinese', value: 3 },
                          ]"
                          item-value="value"
                          item-text="name"
                          outlined
                          dense
                          label="language"
                        ></v-autocomplete>
                      </td>
                    </tr>
                    <tr>
                      <td>Volume</td>
                      <td>
                        <v-select
                          class="pb-0"
                          v-model="deviceSettings.volume"
                          placeholder="volume"
                          :items="[0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]"
                          outlined
                          dense
                          label="volume"
                        ></v-select>
                      </td>
                    </tr>
                    <tr>
                      <td>Device Menu password</td>
                      <td>
                        <v-text-field
                          type="number"
                          :rules="menu_password"
                          class="pb-0"
                          v-model="deviceSettings.menuPassword"
                          placeholder="min 4 to  max 8 numbers"
                          outlined
                          dense
                          label="Menu Password"
                        ></v-text-field>
                      </td>
                    </tr>

                    <tr>
                      <td>Alarm Events Push(msgPush)</td>
                      <td>
                        <v-select
                          class="pb-0"
                          v-model="deviceSettings.msgPush"
                          placeholder="Push to Live"
                          :items="[
                            { name: 'Enabled', value: 1 },
                            { name: 'Disabled', value: 0 },
                          ]"
                          item-value="value"
                          item-text="name"
                          outlined
                          dense
                          label="Push to Live"
                        ></v-select>
                      </td>
                    </tr>
                    <tr>
                      <td>Time</td>
                      <td>
                        <v-text-field
                          :disabled="true"
                          class="pb-0"
                          v-model="deviceSettings.time"
                          placeholder="Date Time"
                          outlined
                          dense
                          label="Date Time"
                        ></v-text-field>
                      </td>
                    </tr>
                    <tr>
                      <td>Maker - Manufacturer</td>
                      <td>
                        <v-text-field
                          :disabled="true"
                          class="pb-0"
                          v-model="deviceSettings.maker_manufacturer"
                          placeholder="Manufacturer Name"
                          outlined
                          dense
                          label="Manufacturer Name"
                        ></v-text-field>
                      </td>
                    </tr>
                    <tr>
                      <td>Maker - Website Link</td>
                      <td>
                        <v-text-field
                          :disabled="true"
                          class="pb-0"
                          v-model="deviceSettings.maker_webAddr"
                          placeholder="website link"
                          outlined
                          dense
                          label="website link"
                        ></v-text-field>
                      </td>
                    </tr>
                    <tr>
                      <td>Maker - Delivery/Release Date</td>
                      <td>
                        <v-menu
                          ref="to_menu_filter"
                          v-model="to_menu_filter"
                          :close-on-content-click="false"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              :disabled="true"
                              outlined
                              dense
                              v-model="deviceSettings.maker_deliveryDate"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                              placeholder="Schedule To Date"
                            ></v-text-field>
                          </template>

                          <v-date-picker
                            style="height: 350px"
                            v-model="deviceSettings.maker_deliveryDate"
                            no-title
                            scrollable
                            @input="to_menu_filter = false"
                          >
                          </v-date-picker>
                        </v-menu>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td>
                        <v-btn
                          dense
                          small
                          class="primary mt-2"
                          @click="updateDeviceSettings()"
                        >
                          Update settings</v-btn
                        >
                      </td>
                    </tr>
                  </v-simple-table>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="DialogDeviceMegviiSettings" max-width="800px">
      <v-card>
        <v-card-title class="popup_background">
          <span>Camera Device MEGVII Settings </span>
          <v-spacer></v-spacer>

          <v-icon outlined @click="DialogDeviceMegviiSettings = false"
            >mdi-close-circle</v-icon
          >
        </v-card-title>

        <v-card-text class="mt-2">
          <v-row class="100%" style="margin: auto; line-height: 36px">
            <v-col cols="12" style="padding: 0px">
              <v-simple-table>
                <tr>
                  <td colspan="2">
                    <v-btn
                      style="float: right"
                      dense
                      small
                      class="primary mt-2 align-right"
                      @click="
                        getDeviceCAMVIISettginsFromSDK(
                          deviceCAMVIISettings.device_id
                        )
                      "
                    >
                      Reload
                    </v-btn>
                    <br />
                    <div v-if="loadingDeviceData" style="color: red">
                      Connecting to Device. Please wait...
                    </div>
                    <div style="color: green">
                      {{ sdk_message }}
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Device Model</td>
                  <td>
                    <v-text-field
                      :disabled="true"
                      class="pb-0"
                      v-model="deviceCAMVIISettings.model_spec"
                      placeholder="Device Model"
                      outlined
                      dense
                    ></v-text-field>
                  </td>
                </tr>

                <tr>
                  <td>Device Time</td>
                  <td>
                    <v-text-field
                      :disabled="true"
                      class="pb-0"
                      v-model="deviceCAMVIISettings.local_time"
                      placeholder="Device Time"
                      outlined
                      dense
                    ></v-text-field>
                  </td>
                </tr>
                <tr>
                  <td>Device Always Open Status</td>
                  <td>
                    <v-switch
                      :disabled="true"
                      v-model="deviceCAMVIISettings.door_open_stat"
                      :label="
                        deviceCAMVIISettings.door_open_stat == 'none'
                          ? 'OFF'
                          : deviceCAMVIISettings.door_open_stat
                      "
                      color="green"
                      value="green"
                      hide-details
                    ></v-switch>
                    <!-- <v-text-field
                      :disabled="true"
                      class="pb-0"
                      v-model="deviceCAMVIISettings.door_open_stat"
                      placeholder="Device Always Open Status"
                      outlined
                      dense
                      label="Device Always Open Status  "
                    ></v-text-field> -->
                  </td>
                </tr>

                <tr>
                  <td>Device Wifi IP</td>
                  <td>
                    <v-text-field
                      :disabled="true"
                      class="pb-0"
                      v-model="deviceCAMVIISettings.wifi_ip"
                      placeholder="Device Wifi IP"
                      outlined
                      dense
                    ></v-text-field>
                  </td>
                </tr>
                <tr>
                  <td>Device LAN IP</td>
                  <td>
                    <v-text-field
                      :disabled="true"
                      class="pb-0"
                      v-model="deviceCAMVIISettings.lan_ip"
                      placeholder="Device LAN IP "
                      outlined
                      dense
                    ></v-text-field>
                  </td>
                </tr>
                <tr>
                  <td>Device Server IP address</td>
                  <td>
                    <v-text-field
                      :disabled="true"
                      class="pb-0"
                      v-model="deviceCAMVIISettings.ipaddr"
                      placeholder="Device Server IP address"
                      outlined
                      dense
                    ></v-text-field>
                  </td>
                </tr>
                <tr>
                  <td>Device Volume</td>
                  <td>
                    <v-progress-linear
                      v-model="deviceCAMVIISettings.voice_volume"
                      height="25"
                    >
                      <strong
                        >{{
                          Math.ceil(deviceCAMVIISettings.voice_volume)
                        }}%</strong
                      >
                    </v-progress-linear>
                    <!-- <v-text-field
                      :disabled="true"
                      class="pb-0"
                      v-model="deviceCAMVIISettings.voice_volume"
                      placeholder="Device Volume"
                      outlined
                      dense
                      label="Device Volume  "
                    ></v-text-field> -->
                  </td>
                </tr>

                <tr>
                  <td></td>
                  <td>
                    <v-btn
                      dense
                      small
                      class="primary mt-2"
                      @click="updateDeviceCAMMIISettings()"
                    >
                      Update settings</v-btn
                    >
                  </td>
                </tr>
              </v-simple-table>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <div class="text-center ma-5">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-row>
      <!-- <v-col xs="12" sm="12" md="3" cols="12">
        <v-select class="form-control" @change="getDataFromApi(`device`)" v-model="pagination.per_page"
          :items="[10, 25, 50, 100]" placeholder="Per Page Records" solo hide-details flat></v-select>
      </v-col> -->
      <!-- <v-col xs="12" sm="12" md="3" cols="12">
        <v-text-field class="form-control py-0 custom-text-box floating shadow-none" placeholder="Search..." solo flat
          @input="searchIt" v-model="search" hide-details></v-text-field>
      </v-col> -->
    </v-row>
    <!-- <Back color="primary" /> -->
    <v-navigation-drawer v-model="editDialog" bottom temporary right fixed>
      <v-toolbar class="popup_background" dense>
        {{ this.editedIndex == -1 ? "New " : "Edit " }} Device
        <v-spacer></v-spacer>

        <v-icon @click="editDialog = false" outlined dark>
          mdi mdi-close-circle
        </v-icon>
      </v-toolbar>
      <v-form ref="form" v-model="valid" lazy-validation>
        <v-row class="ma-1">
          <v-col md="12">
            <v-text-field
              :hide-details="!payload.name"
              v-model="payload.name"
              placeholder="Device Name"
              outlined
              dense
              label="Device Name *"
            ></v-text-field>
            <span v-if="errors && errors.name" class="error--text pa-0 ma-0"
              >{{ errors.name[0] }}
            </span>
          </v-col>
          <v-col md="12">
            <v-text-field
              class="pb-0"
              :hide-details="!payload.short_name"
              v-model="payload.short_name"
              placeholder="Short Name"
              outlined
              dense
              label="Short Name *"
            ></v-text-field>
            <span
              v-if="errors && errors.short_name"
              class="error--text pa-0 ma-0"
              >{{ errors.short_name[0] }}
            </span>
          </v-col>
          <v-col md="12">
            <v-autocomplete
              class="pb-0"
              :hide-details="!payload.branch_id"
              v-model="payload.branch_id"
              placeholder="Branch Name"
              outlined
              dense
              label="Branch Name *"
              :items="branches"
              item-value="id"
              item-text="branch_name"
            ></v-autocomplete>
            <span
              v-if="errors && errors.branch_id"
              class="error--text pa-0 ma-0"
              >{{ errors.branch_id[0] }}
            </span>
          </v-col>
          <v-col md="12">
            <v-text-field
              class="pb-0"
              :hide-details="!payload.location"
              v-model="payload.location"
              placeholder="Device location"
              outlined
              dense
              label="Device location *"
            ></v-text-field>
            <span v-if="errors && errors.location" class="error--text"
              >{{ errors.location[0] }}
            </span>
          </v-col>
          <v-col md="12">
            <v-autocomplete
              class="pb-0"
              :hide-details="!payload.utc_time_zone"
              v-model="payload.utc_time_zone"
              placeholder="Time Zone"
              outlined
              dense
              label="Time Zone(Ex:UTC+) *"
              :items="getTimezones()"
              item-value="key"
              item-text="text"
            ></v-autocomplete>
            <span v-if="errors && errors.utc_time_zone" class="error--text"
              >{{ errors.utc_time_zone[0] }}
            </span>
          </v-col>
          <v-col md="12">
            <v-text-field
              class="pb-0"
              :hide-details="!payload.model_number"
              v-model="payload.model_number"
              placeholder="Model Number"
              outlined
              dense
              label="Model Number *"
            ></v-text-field>
            <span v-if="errors && errors.model_number" class="error--text"
              >{{ errors.model_number[0] }}
            </span>
          </v-col>
          <v-col md="12">
            <v-text-field
              class="pb-0"
              :hide-details="!payload.device_id"
              v-model="payload.device_id"
              placeholder="Serial Number"
              outlined
              dense
              label="Serial Number *"
            ></v-text-field>
            <span v-if="errors && errors.device_id" class="error--text"
              >{{ errors.device_id[0] }}
            </span>
          </v-col>
          <v-col md="12">
            <v-autocomplete
              class="pb-0"
              :hide-details="!payload.function"
              v-model="payload.function"
              placeholder="Function"
              outlined
              dense
              label="Function *"
              :items="[
                { id: 'auto', name: 'Auto' },
                { id: 'In', name: 'In' },
                { id: 'Out', name: 'Out' },
              ]"
              item-value="id"
              item-text="name"
            ></v-autocomplete>
            <span v-if="errors && errors.function" class="error--text"
              >{{ errors.function[0] }}
            </span>
          </v-col>
          <v-col md="12">
            <v-autocomplete
              class="pb-0"
              :hide-details="!payload.device_type"
              v-model="payload.device_type"
              placeholder="Device Type"
              outlined
              dense
              label="Device Type *"
              :items="[
                { id: 'all', name: 'All(Attendance and Access)' },
                { id: 'Attendance', name: 'Attendance' },
                { id: 'Access Control', name: 'Access Control' },
              ]"
              item-value="id"
              item-text="name"
            ></v-autocomplete>
            <span v-if="errors && errors.device_type" class="error--text"
              >{{ errors.device_type[0] }}
            </span>
          </v-col>
          <v-col md="12">
            <v-autocomplete
              class="pb-0"
              :hide-details="!payload.status_id"
              v-model="payload.status_id"
              placeholder="Time Zone"
              outlined
              dense
              label="Device Status *"
              :items="device_statusses"
              item-value="id"
              item-text="name"
            ></v-autocomplete>
            <span v-if="errors && errors.status_id" class="error--text"
              >{{ errors.status_id[0] }}
            </span>
          </v-col>
          <!-- <v-col md="12">
            <v-autocomplete
              class="pb-0"
              :hide-details="!payload.camera_save_images"
              v-model="payload.camera_save_images"
              placeholder="Camera Unregistred Images"
              outlined
              dense
              label="Camera Unregistred Images"
              :items="[
                { id: false, name: 'No' },
                { id: true, name: 'Yes' },
              ]"
              item-value="id"
              item-text="name"
            ></v-autocomplete>
            <span v-if="errors && errors.camera_save_images" class="error--text"
              >{{ errors.camera_save_images[0] }}
            </span>
          </v-col> -->
        </v-row>
      </v-form>
      <v-row v-if="deviceResponse">
        <v-col>
          <div style="color: red; font-size: 14px" class="pl-1">
            {{ deviceResponse }}
          </div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <div class="text-right">
            <v-btn
              small
              :loading="loading"
              color="primary"
              @click="store_device"
            >
              Submit
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </v-navigation-drawer>

    <v-card class="mb-5 mt-2" elevation="0">
      <v-toolbar class="rounded-md" dense flat>
        <v-toolbar-title><span> Devices List</span></v-toolbar-title>

        <span>
          <v-btn
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            title="Reload"
          >
            <v-icon class="ml-2" @click="getDataFromApi()" dark
              >mdi mdi-reload</v-icon
            >
          </v-btn>
        </span>

        <span v-if="isCompany" style="width: 250px">
          <v-select
            @change="getDataFromApi()"
            class="pt-10 px-2"
            v-model="filters[`branch_id`]"
            :items="[{ id: ``, branch_name: `Select All` }, ...branchesList]"
            dense
            placeholder="Select Branch"
            outlined
            item-value="id"
            item-text="branch_name"
          >
          </v-select>
        </span>
        <!-- </template>
          <span>Reload</span>
        </v-tooltip> -->

        <!-- <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }"> -->
        <!-- <v-btn
          x-small
          :ripple="false"
          text
          title="Filter"
          @click="toggleFilter()"
        >
          <v-icon dark>mdi-filter</v-icon>
        </v-btn> -->
        <!-- </template>
          <span>Filter</span>
        </v-tooltip> -->

        <v-spacer></v-spacer>

        <span>
          <v-btn
            x-small
            :ripple="false"
            text
            title="Sync Devices"
            @click="updateDevicesHealth"
          >
            <v-icon dark white>mdi-cached</v-icon>
          </v-btn>
        </span>
        <span>
          <v-btn
            v-if="can(`device_create`)"
            x-small
            :ripple="false"
            text
            title="Add Device"
            @click="addItem()"
          >
            <v-icon dark white>mdi-plus-circle</v-icon>
          </v-btn>
        </span>
      </v-toolbar>

      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
      <v-data-table
        dense
        :headers="headers"
        :items="data"
        model-value="data.id"
        :loading="loading"
        :footer-props="{
          itemsPerPageOptions: [50, 100, 500, 1000],
        }"
        class="elevation-1 pt-5"
        :options.sync="options"
        :server-items-length="totalRowsCount"
      >
        <!-- <template v-slot:header="{ props: { headers } }">
          <tr v-if="isFilter">
            <td
              style="width: 50px"
              v-for="header in headers"
              :key="header.text"
            >
              <v-container>
                <v-text-field
                  clearable
                  :hide-details="true"
                  v-if="header.filterable && !header.filterSpecial"
                  v-model="filters[header.value]"
                  :id="header.value"
                  @input="applyFilters(header.key, $event)"
                  outlined
                  dense
                  autocomplete="off"
                ></v-text-field>
                <v-select
                  :hide-details="true"
                  @change="applyFilters('status', $event)"
                  item-value="value"
                  item-text="title"
                  v-model="filters[header.text]"
                  outlined
                  dense
                  v-else-if="header.filterSpecial && header.text == 'Status'"
                  :items="[
                    { value: '', title: 'All' },
                    { value: '1', title: 'Online' },
                    {
                      value: '2',
                      title: 'Offline',
                    },
                  ]"
                ></v-select>
              </v-container>
            </td>
          </tr>
        </template> -->
        <template v-slot:item.sno="{ item, index }">
          {{ ++index }}
        </template>
        <template v-slot:item.name="{ item }">
          {{ caps(item.name) }}
          <div class="secondary-value">{{ item.short_name }}</div>
        </template>

        <template v-slot:item.location="{ item }">
          {{ caps(item.location) }}
        </template>
        <template v-slot:item.device_id="{ item }">
          {{ item.device_id }}
          <div class="secondary-value">{{ item.model_number }}</div>
        </template>
        <template v-slot:item.function="{ item }">
          <img
            title="Auto (In and Out)"
            v-if="item.function == 'auto'"
            src="/icons/function_in_out.png"
            style="width: 30px"
          />
          <img
            title="Only In"
            v-else-if="item.function == 'In'"
            src="/icons/function_in.png"
            style="width: 30px"
          />
          <img
            title="Only Out"
            v-else-if="item.function == 'Out'"
            src="/icons/function_out.png"
            style="width: 30px"
          />
        </template>

        <template v-slot:item.device_type="{ item }">
          <img
            title="All (Attendance and Access Control )"
            v-if="item.device_type == 'all'"
            src="/icons/device_type_all.png"
            style="width: 30px"
          />
          <img
            title="Only Access Control"
            v-else-if="item.device_type == 'Access Control'"
            src="/icons/device_type_access_control.png"
            style="width: 30px"
          />
          <img
            title="Only Attendance"
            v-else-if="item.device_type == 'Attendance'"
            src="/icons/device_type_attendance.png"
            style="width: 30px"
          />
        </template>
        <template v-slot:item.door_open="{ item }">
          <img
            style="cursor: pointer"
            title="Click to Open Door"
            @click="open_door(item.device_id)"
            src="/icons/door_open.png"
            class="iconsize30"
          />
        </template>
        <template v-slot:item.door_close="{ item }">
          <img
            style="cursor: pointer"
            title="Click to Close Door"
            @click="close_door(item.device_id)"
            src="/icons/door_close.png"
            class="iconsize30"
          />
        </template>

        <template v-slot:item.always_open="{ item }">
          <img
            style="cursor: pointer"
            title="Click to Always Open"
            @click="open_door_always(item.id)"
            src="/icons/always_open.png"
            class="iconsize30"
          />
        </template>
        <template v-slot:item.alarm="{ item }">
          <div v-if="item.alarm_status == 1">
            <v-icon
              class="alarm"
              @click="UpdateAlarmStatus(item, 0)"
              title="Click to Turn OFF Alarm "
              >mdi mdi-alarm-light</v-icon
            >
            <div class="secondary-value">{{ item.alarm_start_datetime }}</div>
          </div>

          <v-icon v-else-if="item.alarm_status == 0" title="Now Alaram is OFF"
            >mdi mdi-alarm-light-outline</v-icon
          >
        </template>

        <template v-slot:item.sync_date_time="{ item }">
          <img
            style="cursor: pointer"
            title="Click Sync UTC Time"
            @click="sync_date_time(item)"
            src="/icons/sync_date_time.png"
            class="iconsize30"
          />
        </template>

        <template v-slot:item.open_always="{ item }"> </template>
        <template v-slot:item.status_id="{ item }">
          <img
            title="Online"
            v-if="item.status.name == 'active'"
            src="/icons/device_status_open.png"
            style="width: 30px"
          />
          <img
            title="Offline"
            v-else
            src="/icons/device_status_close.png"
            style="width: 30px"
          />

          <!-- <img
            @click="sync_date_time(item)"
            :src="getDeviceStatusIcon(item)"
            class="iconsize30"
          /> -->
          <!-- <v-chip
            small
            class="p-2 mx-1"
            :color="item.status.name == 'active' ? 'primary' : 'error'"
          >
            {{ item.status.name == "active" ? "online" : "offline" }}
          </v-chip> -->
        </template>
        <template v-slot:item.status="{ item }">
          <!-- <v-chip
            small
            class="p-2"
            color="primary"
            @click="open_door(item.device_id)"
          >
            Open
          </v-chip>
          <v-chip
            small
            class="p-2 mx-1"
            color="primary"
            @click="open_door_always(item.device_id)"
          >
            Open Always
          </v-chip>

          <v-chip
            small
            class="p-2"
            color="error"
            @click="open_door_always(item.device_id)"
          >
            Close
          </v-chip> -->
        </template>
        <!-- <template v-slot:item.sync_date_time="{ item }">
          <v-chip
            small
            class="p-2 mx-1"
            @click="sync_date_time(item)"
            :color="'primary'"
          >
            {{
              item.sync_date_time == "---"
                ? "click to sync"
                : item.sync_date_time
            }}
          </v-chip>
        </template> -->
        <template v-slot:item.options="{ item }">
          <v-menu bottom left>
            <template v-slot:activator="{ on, attrs }">
              <div class="text-right">
                <v-btn dark-2 icon v-bind="attrs" v-on="on">
                  <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
              </div>
            </template>
            <v-list width="120" dense>
              <v-list-item @click="findUser(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small>mdi-magnify </v-icon>
                  Find User
                </v-list-item-title>
              </v-list-item>
              <v-list-item v-if="can(`device_edit`)" @click="editItem(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-pencil </v-icon>
                  Edit
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                v-if="can(`device_edit`) && item.model_number == 'CAMERA1'"
                @click="showDeviceCameraSettings(item)"
              >
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-cog </v-icon>
                  Settings
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                v-else-if="can(`device_edit`) && item.model_number == 'MEGVII'"
                @click="showDeviceMegviiSettings(item)"
              >
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-cog </v-icon>
                  Settings
                </v-list-item-title>
              </v-list-item>
              <v-list-item v-else @click="showDeviceSettings(item)">
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small> mdi-cog </v-icon>
                  Settings
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                v-if="can(`device_delete`)"
                @click="deleteItem(item)"
              >
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="error" small> mdi-delete </v-icon>
                  Delete
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>
<script>
// import Back from "../../components/Snippets/Back.vue";
import timeZones from "../../defaults/utc_time_zones.json";
import DeviceAccessSettings from "../../components/DeviceAccessSettings.vue";
export default {
  components: { DeviceAccessSettings },

  data: () => ({
    deviceCAMVIISettings: {},
    DialogDeviceMegviiSettings: false,
    valid: false,
    rules: [(value) => (value || "").length <= 10 || "Max 10 characters"],
    device_model: [
      (v) => !!v || "Device Name  is required",
      (value) => (value || "").length <= 20 || "Max 20 characters",
    ],

    menu_password: [
      (v) => !!v || "Password is required",
      (value) => (value || "").length <= 8 || "Max 8 characters",
    ],
    sdk_message: "",
    DialogDeviceSettings: false,
    deviceSettings: { maker: {} },
    to_menu_filter: false,
    to_menu_filter: "",
    to_date_filter: "",
    visitorUploadedDevicesInfo: [],
    loadingDeviceData: false,
    visitor_status_list: [],
    inputFindDeviceUserId: "",
    popupDeviceId: null,
    uploadedUserInfoDialog: false,
    dialogAccessSettings: false,
    popup_device_id: "",
    editDialog: false,
    showFilters: false,
    filters: {
      branch_id: "",
    },
    isFilter: false,
    totalRowsCount: 0,
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    timeZones: timeZones,
    payload: {
      name: "",
      device_type: "",
      device_id: "",
      model_number: "",
      status_id: "",
      company_id: "",
      location: "",
      short_name: "",
      ip: "",
      port: "",
      camera_save_images: false,
    },
    Model: "Device",
    pagination: {
      current: 1,
      total: 0,
      per_page: 100,
    },
    options: {},
    endpoint: "device",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    deviceResponse: "",
    headers: [
      {
        text: "Sno",
        align: "left",
        sortable: false,
        value: "sno",
        filterable: false,
      },
      {
        text: "Name",
        align: "left",
        sortable: false,
        value: "name",
        filterable: false,
      },
      // {
      //   text: "Short Name",
      //   align: "left",
      //   sortable: false,
      //   value: "short_name",
      //   filterable: false,
      // },
      // {
      //   text: "Branch",
      //   align: "left",
      //   sortable: false,
      //   value: "branch",
      //   filterable: false,
      // },

      {
        text: "Location",
        align: "left",
        sortable: false,
        value: "location",
        filterable: false,
      },
      {
        text: "Time zone",
        align: "left",
        sortable: false,
        value: "utc_time_zone",
        filterable: false,
      },
      // {
      //   text: "Model Number",
      //   align: "left",
      //   sortable: false,
      //   value: "model_number",
      //   filterable: false,
      // },

      {
        text: "Device Serial Number",
        align: "left",
        sortable: false,
        value: "device_id",
        filterable: false,
      },
      {
        text: "Function",
        align: "left",
        sortable: false,
        value: "function",
        filterable: false,
      },
      {
        text: "Type",
        align: "left",
        sortable: false,
        value: "device_type",
        filterable: false,
      },
      {
        text: "Door Open",
        align: "left",
        sortable: false,
        value: "door_open",
        filterable: false,
      },
      {
        text: "Door Close",
        align: "left",
        sortable: false,
        value: "door_close",
        filterable: false,
      },
      {
        text: "Always Open",
        align: "left",
        sortable: false,
        value: "always_open",
        filterable: false,
      },
      {
        text: "Alarm",
        align: "center",
        width: "100px",
        sortable: false,
        value: "alarm",
        filterable: false,
      },

      {
        text: "Time Sync",
        align: "left",
        sortable: false,
        value: "sync_date_time",
        filterable: false,
      },
      {
        text: "Status",
        align: "center",
        sortable: false,
        value: "status_id",
        filterable: false,
        filterSpecial: false,
      },
      {
        text: "Options",
        align: "center",
        sortable: false,
        value: "options",
        filterable: false,
        filterSpecial: false,
      },
    ],
    editedIndex: -1,
    response: "",
    errors: [],

    device_statusses: [],
    branches: [],
    branchesList: [],
    isCompany: true,
    timeZoneOptions: [],
    editedItem: null,
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New device" : "Edit device";
    },
  },

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
  },
  mounted() {
    setTimeout(() => {
      this.updateDevicesHealth();
    }, 1000 * 5);

    setInterval(() => {
      if (this.$route.name == "device") {
        this.getDataFromApi();
      }
    }, 1000 * 60);
  },
  async created() {
    this.loading = true;

    if (this.$auth.user.branch_id) {
      this.filters[branch_id] = this.$auth.user.branch_id;
      this.isCompany = false;
      return;
    }

    let branch_header = [
      {
        text: "Branch",
        align: "left",
        sortable: true,
        key: "branch_id", //sorting
        value: "company_branch.branch_name", //edit purpose

        filterable: true,
        filterSpecial: true,
      },
    ];
    this.headers.splice(1, 0, ...branch_header);

    try {
      const { data } = await this.$axios.get(`branches_list`, {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company_id,
        },
      });
      this.branchesList = data;
    } catch (error) {
      // Handle the error
      console.error("Error fetching branch list", error);
    }

    this.getDataFromApi();
    this.getBranches();
    this.getDeviceStatus();
  },

  methods: {
    UpdateAlarmStatus(item, status) {
      if (status == 0) {
        if (confirm("Are you sure you want to TURN OFF the Alarm")) {
          let options = {
            params: {
              company_id: this.$auth.user.company_id,
              serial_number: item.serial_number,
              status: status,
            },
          };
          this.loading = true;
          this.$axios
            .post(`/update-device-alarm-status`, options.params)
            .then(({ data }) => {
              this.getDataFromApi();
              if (!data.status) {
                if (data.message == "undefined") {
                  this.response = "Try again. No connection available";
                } else this.response = "Try again. " + data.message;
                this.snackbar = true;

                return;
              } else {
                setTimeout(() => {
                  this.loading = false;
                  this.response = data.message;
                  this.snackbar = true;
                }, 2000);

                return;
              }
            })
            .catch((e) => console.log(e));
        }
      }
    },
    showDeviceMegviiSettings(item) {
      this.errors = [];
      this.payload = {};
      this.editedIndex = item.id;

      this.editedItem = item;
      this.loadingDeviceData = true;

      this.getDeviceCAMVIISettginsFromSDK(item.device_id);
      this.DialogDeviceMegviiSettings = true;
    },
    showDeviceCameraSettings(device) {
      return false;
    },

    updateDeviceCAMMIISettings() {
      if (confirm("Are you want to Update Device settings  ?")) {
        {
          this.deviceCAMVIISettings.voice_volume = Math.ceil(
            this.deviceCAMVIISettings.voice_volume
          );
          let options = {
            params: {
              company_id: this.$auth.user.company_id,
              deviceSettings: this.deviceCAMVIISettings,
            },
          };
          this.loading = true;
          this.$axios
            .post(`/update-device-camvii-sdk-settings`, options.params)
            .then(({ data }) => {
              if (!data.status) {
                if (data.message == "undefined") {
                  this.response = "Try again. No connection available";
                } else this.response = "Try again. " + data.message;
                this.snackbar = true;

                return;
              } else {
                setTimeout(() => {
                  this.loading = false;
                  this.response = data.message;
                  this.snackbar = true;
                }, 2000);

                return;
              }
            })
            .catch((e) => console.log(e));
        }
      }
    },
    updateDeviceSettings() {
      if (confirm("Are you want to Update Device settings  ?")) {
        if (
          this.$refs.form.validate() &&
          this.deviceSettings.menuPassword != ""
          // &&          this.deviceSettings.name != ""
        ) {
          let options = {
            params: {
              company_id: this.$auth.user.company_id,
              deviceSettings: this.deviceSettings,
            },
          };
          this.loading = true;
          this.$axios
            .post(`/update-device-sdk-settings`, options.params)
            .then(({ data }) => {
              if (!data.status) {
                if (data.message == "undefined") {
                  this.response = "Try again. No connection available";
                } else this.response = "Try again. " + data.message;
                this.snackbar = true;

                return;
              } else {
                setTimeout(() => {
                  this.loading = false;
                  this.response = data.message;
                  this.snackbar = true;
                }, 2000);

                return;
              }
            })
            .catch((e) => console.log(e));
        }
      }
    },
    findUser(item) {
      this.popupDeviceId = item.device_id;
      this.uploadedUserInfoDialog = true;
    },

    getDeviceCAMVIISettginsFromSDK(device_id) {
      if (device_id != "") {
        this.sdk_message = "";
        this.deviceSettings = {};
        this.deviceSettings.device_id = device_id;
        this.loadingDeviceData = true;
        let counter = 1;

        let options = {
          params: {
            company_id: this.$auth.user.company_id,
            device_id: device_id,
          },
        };
        this.loadingDeviceData = true;
        this.$axios
          .get(`get-device-camvii-settings-from-sdk`, options)
          .then(({ data }) => {
            this.loadingDeviceData = false;

            this.sdk_message = data.SDKresponseData.message;
            if (!data.SDKresponseData.data) {
              this.response = "Try again. " + data.message;
              this.snackbar = true;

              return;
            } else {
              this.deviceCAMVIISettings = data.SDKresponseData.data;
              this.deviceCAMVIISettings.device_id = device_id;

              return;
            }
          });
      }
    },
    getDeviceSettginsFromSDK(device_id) {
      if (device_id != "") {
        this.sdk_message = "";
        this.deviceSettings = {};
        this.deviceSettings.device_id = device_id;
        this.loadingDeviceData = true;
        let counter = 1;

        let options = {
          params: {
            company_id: this.$auth.user.company_id,
            device_id: device_id,
          },
        };
        this.loadingDeviceData = true;
        this.$axios
          .get(`get-device-settings-from-sdk`, options)
          .then(({ data }) => {
            this.loadingDeviceData = false;

            this.sdk_message = data.SDKresponseData.message;
            if (!data.SDKresponseData.data) {
              this.response = "Try again. " + data.message;
              this.snackbar = true;

              return;
            } else {
              this.deviceSettings = data.SDKresponseData.data;
              this.deviceSettings.device_id = device_id;

              this.deviceSettings.time = this.deviceSettings.time.replace(
                "T",
                " "
              );

              return;
            }
          });
      }
    },
    getUserInfoFromDevice() {
      if (this.inputFindDeviceUserId != "") {
        this.visitorUploadedDevicesInfo = [];
        this.loadingDeviceData = true;
        let counter = 1;

        let options = {
          params: {
            company_id: this.$auth.user.company_id,

            system_user_id: this.inputFindDeviceUserId,

            device_id: this.popupDeviceId,
          },
        };
        this.loadingDeviceData = true;
        this.$axios
          .get(`get-device-person-details`, options)
          .then(({ data }) => {
            this.loadingDeviceData = false;
            counter++;
            if (!data.deviceName) {
              this.response = data.message;
              this.snackbar = true;

              return;
            } else {
              data.system_user_id = this.inputFindDeviceUserId;
              data.device_id = this.popupDeviceId;

              this.visitorUploadedDevicesInfo.push(data);

              return;
            }
          });
      }
    },
    getTimezones() {
      return Object.keys(this.timeZones).map((key) => ({
        offset: this.timeZones[key].offset,
        time_zone: this.timeZones[key].time_zone,
        key: key,
        text: key + " - " + this.timeZones[key].offset,
      }));
    },
    getBranches() {
      this.$axios
        .get(`branch`, { params: { company_id: this.$auth.user.company_id } })
        .then(({ data }) => {
          this.branches = data.data;
        });
    },
    getDeviceStatus() {
      this.$axios.get(`device_status`).then(({ data }) => {
        this.device_statusses = data.data;
      });
    },
    datatable_save() {},
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },
    async sync_date_time(item) {
      if (
        confirm(
          "Are you want to change the Device Time to " +
            item.utc_time_zone +
            "?"
        )
      ) {
        try {
          const dt = new Date();
          const year = dt.getFullYear();
          const month = String(dt.getMonth() + 1).padStart(2, "0");
          const day = String(dt.getDate()).padStart(2, "0");
          const hours = String(dt.getHours()).padStart(2, "0");
          const minutes = String(dt.getMinutes()).padStart(2, "0");
          const seconds = String(dt.getSeconds()).padStart(2, "0");

          const apiUrl = `sync_device_date_time/${item.device_id}/${this.$auth.user.company_id}`;
          const sync_able_date_time = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
          const { data } = await this.$axios.get(apiUrl, {
            params: { sync_able_date_time },
          });

          this.snackbar = true;
          this.response = data.message;
        } catch (error) {
          this.snackbar = true;
          this.response = error;
          console.error("Error syncing date and time:", error);
        }
      }
    },
    closepopup() {
      this.snackbar = true;
      this.response = "Device Time details are updated successfully";
      this.dialogAccessSettings = false;
    },
    getUTC_CurentTime(targetTimezone) {
      const localDate = new Date();
      const options = {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        timeZone: targetTimezone,
        hour12: false,
      };
      const formattedDateTime = localDate.toLocaleDateString("en-US", options);

      let dt = new Date(formattedDateTime);

      let year = dt.getFullYear();
      let month = dt.getMonth() + 1;
      let day = dt.getDate();

      let hours = dt.getHours();
      hours = hours < 10 ? "0" + hours : hours;

      let minutes = dt.getMinutes();
      minutes = minutes < 10 ? "0" + minutes : minutes;

      let seconds = dt.getSeconds();
      seconds = seconds < 10 ? "0" + seconds : seconds;

      let sync_able_date_time = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

      return sync_able_date_time;
    },
    open_door(device_id) {
      let options = {
        params: { device_id },
      };
      confirm("Are you sure want to open the Door?") &&
        this.$axios.get(`open_door`, options).then(({ data }) => {
          this.snackbar = true;
          this.response = data.message;
          // this.getDataFromApi();
        });
    },
    open_door_always(device_id) {
      this.popup_device_id = device_id;
      this.dialogAccessSettings = true;

      /////////// this.$router.push(`/device/time_settings/${device_id}`);
      // let options = {
      //   params: { device_id },
      // };
      // this.$axios.get(`open_door_always`, options).then(({ data }) => {
      //   this.snackbar = true;
      //   this.response = data.message;
      //   this.getDataFromApi();
      // });
    },
    close_door(device_id) {
      let options = {
        params: { device_id },
      };
      confirm("Are you sure want to close the Door?") &&
        this.$axios.get(`close_door`, options).then(({ data }) => {
          this.snackbar = true;
          this.response = data.message;
          this.getDataFromApi();
        });
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    onPageChange() {
      this.getDataFromApi();
    },
    applyFilters() {
      this.getDataFromApi();
    },
    toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    async getDataFromApi(
      url = this.endpoint,
      filter_column = "",
      filter_value = ""
    ) {
      this.loading = true;
      const data = await this.$store.dispatch("fetchData", {
        key: "devices",
        options: this.options,
        refresh: true,
        endpoint: this.endpoint,
        filters: this.filters,
      });
      this.data = data.data;
      this.totalRowsCount = data.total;
      this.loading = false;

      return;

      if (url == "") url = this.endpoint;
      this.loading = true;
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          branch_id: this.branch_id,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };
      if (filter_column != "") {
        if (filter_column == "serach_status_name") {
          options.params[filter_column] =
            filter_value.toLowerCase() == "online"
              ? "active"
              : filter_value.toLowerCase() == "offline"
              ? "inactive"
              : "";
        } else options.params[filter_column] = filter_value;
      }
      await this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }
        this.totalRowsCount = data.total;
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },
    async updateDevicesHealth() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      await this.$axios
        .get("/check_device_health", options)
        .then(({ data }) => {
          this.snackbar = true;
          this.response = data;
          this.getDataFromApi();
        });
    },

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    editItem(item) {
      this.errors = [];
      this.payload = {};
      this.editedIndex = item.id;

      this.payload = Object.assign({}, item);

      this.popupDeviceId = item.device_id;

      this.editDialog = true;

      this.deviceResponse = "";
    },
    showDeviceSettings(item) {
      this.errors = [];
      this.payload = {};
      this.editedIndex = item.id;

      this.editedItem = item;
      this.loadingDeviceData = true;

      this.getDeviceSettginsFromSDK(item.device_id);
      this.DialogDeviceSettings = true;
    },
    addItem() {
      this.payload = {};
      this.errors = [];
      if (!this.isCompany) {
        this.payload.branch_id = this.branch_id;
      }

      this.editedIndex = -1;
      this.editDialog = true;

      this.deviceResponse = "";
    },
    store_device() {
      let id = this.editedIndex;
      //let company_id = console.log(this.payload);
      let payload = this.payload;

      this.payload.company_id = this.$auth.user.company_id;
      if (this.editedIndex == -1) this.payload.status_id = 2;
      this.payload.ip = "0.0.0.0";
      this.payload.serial_number = this.payload.device_id;
      this.payload.port = "0000";

      delete this.payload.status;
      delete this.payload.company;
      delete this.payload.company_branch;

      this.loading = true;
      if (this.editedIndex == -1) {
        this.$axios
          .post(`/device`, payload)
          .then(({ data }) => {
            this.loading = false;
            if (!data.status) {
              this.errors = [];
              if (data.errors) this.errors = data.errors;

              this.snackbar = true;
              this.deviceResponse = data.message;
              this.response = data.message;
            } else if (data.status == "device_api_error") {
              this.errors = [];
              this.snackbar = true;
              this.deviceResponse =
                "Check the Device information. There are errors.";

              this.response = "Check the Device information. There are errors.";
            } else {
              this.snackbar = true;
              this.deviceResponse = "Device details are  Created successfully";
              this.response = "Device details are  Created successfully";
              this.getDataFromApi();
              this.editDialog = false;
            }
          })
          .catch((e) => console.log(e));
      } else {
        this.$axios
          .put(`/device/${id}`, payload)
          .then(({ data }) => {
            this.loading = false;
            if (!data.status) {
              this.errors = data.errors;
            } else if (data.status == "device_api_error") {
              this.errors = [];
              this.snackbar = true;
              this.deviceResponse =
                "Check the Device information. There are errors.";
              this.response = "Check the Device information. There are errors.";
            } else {
              this.snackbar = true;
              this.deviceResponse = "Device details are  updated successfully";
              this.response = "Device details are updated successfully";
              this.getDataFromApi();
              this.editDialog = false;
            }
          })
          .catch((e) => console.log(e));
      }
    },

    getFunctionIcon(item) {
      if (item.function == "auto") {
        return "/icons/function_in_out.png?t=2";
      } else if (item.function == "In") {
        return "/icons/function_in.png";
      } else if (item.function == "Out") {
        return "/icons/function_out.png";
      }
    },
    getDeviceIcon(item) {
      if (item.device_type == "all") {
        return "/icons/device_type_all.png";
      } else if (item.device_type == "Access Control") {
        return "/icons/device_type_attendance.png";
      } else if (item.device_type == "Attendance") {
        return "/icons/device_type_access_control.png";
      }
    },
    getDeviceStatusIcon(item) {
      if (item.status.name == "active") {
        return "/icons/device_status_open.png";
      } else {
        return "/icons/device_status_close.png";
      }
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.snackbar = data.status;
              this.response = data.message;
              this.getDataFromApi();
            }
          })
          .catch((err) => console.log(err));
    },
  },
};
</script>
<style scoped>
.v-text-field.v-text-field--enclosed .v-text-field__details,
.v-text-field.v-text-field--enclosed .v-text-field__details,
.v-text-field__details,
.v-text-field.v-text-field--enclosed .v-text-field__details {
  margin-bottom: 0px !important;
  padding: 0px !important;
}
.v-messages {
  min-height: 0px !important;
}
</style>

<!-- <style>
.v-dialog {
  background-color: #fff;
}
</style>
<style scoped>
@import "https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.css";
</style> -->
