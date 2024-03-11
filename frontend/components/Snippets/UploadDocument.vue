<template>
  <div>
    <!-- <v-img
      style="width: 150px; height: 150px; border-radius: 50%; margin: 0 auto"
      :src="imagePreview"
    ></v-img> -->
    <v-btn outlined block  color="primary" @click="onpick_attachment"
      >{{ !upload.name ? "Upload " + label : "Change " + label }}
      <v-icon right dark>mdi-cloud-upload</v-icon>
    </v-btn>

    <input
      required
      type="file"
      @change="attachment"
      style="display: none"
      accept="image/*"
      ref="attachment_input"
    />
  </div>
</template>

<script>
export default {
  props: {
    defaultImage: {
      default: "no-profile-image.jpg",
    },
    label: {
      default: "Document",
    },
  },
  data: () => ({
    imagePreview: "/no-profile-image.jpg",
    upload: {
      name: "",
    },
  }),
  created() {
    // this.imagePreview = this.defaultImage || "/no-profile-image.jpg";
  },
  methods: {
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },
    attachment(e) {
      this.upload.name = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;
      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          //   this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file[0]);
        this.$emit("uploaded", file[0]);
      }
    },
  },
};
</script>
