<template>
    <v-avatar size="200">
      <v-img v-show="isImageBox" :src="imageSrc" />
      <video v-show="!isImageBox" ref="video" autoplay playsinline></video>
    </v-avatar>
</template>

<script>
export default {
  props: ["isImageBox"],
  data() {
    return {
      videoStream: null,
      imageSrc: "/no-profile-image.jpg",
    };
  },
  methods: {
    async openCamera() {
      const video = this.$refs.video;
      const mediaStream = await navigator.mediaDevices.getUserMedia({
        video: true,
      });
      video.srcObject = mediaStream;
      this.videoStream = mediaStream;
    },
    async takePicture() {
      const canvas = document.createElement("canvas");
      const video = this.$refs.video;
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      canvas
        .getContext("2d")
        .drawImage(video, 0, 0, canvas.width, canvas.height);
      this.imageSrc = canvas.toDataURL("image/jpeg");
      this.$emit("imageSrc", this.imageSrc);
    },
  },

  beforeDestroy() {
    if (this.videoStream) {
      this.videoStream.getTracks().forEach((track) => track.stop());
    }
  },
};
</script>
