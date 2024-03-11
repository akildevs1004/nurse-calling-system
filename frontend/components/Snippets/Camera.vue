<template>
  <div>
    <v-avatar style="border: 1px solid purple" size="300">
      <img v-show="!isClicked" src="/no-profile-image.jpg" ref="img" alt="" />

      <video
        v-show="isClicked"
        width="100%"
        ref="video"
        autoplay
        playsinline
      ></video>
      <canvas ref="canvas" style="display: none"></canvas>
    </v-avatar>
    
    <div class="mb-1">
      <v-btn class="primary mt-1" @click="takeSnapshot">{{
        isCamera ? "Take Picture" : "Open Camera"
      }}</v-btn>
    </div>
  </div>
</template>

<script>
export default {
  auth: false,
  layout: "login",

  data: () => ({
    isClicked: false,
    isCamera: false,
    videoStream: null,
  }),

  mounted() {
    this.setupCamera();
  },
  methods: {
    async setupCamera() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({
          video: true,
        });
        const video = this.$refs.video;
        video.srcObject = stream;
        this.videoStream = stream;
        video.play();
      } catch (error) {
        console.error("Error accessing the camera: ", error);
      }
    },
    takeSnapshot() {
      this.isClicked = true;
      this.isCamera = !this.isCamera;
      const video = this.$refs.video;
      const canvas = this.$refs.canvas;
      const context = canvas.getContext("2d");
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      context.drawImage(video, 0, 0, canvas.width, canvas.height);
      this.$emit("imageSrc", canvas.toDataURL("image/png"));

      if (this.isCamera) {
        video.play();
      } else if (!this.isCamera) {
        video.pause();
      }
    },
    beforeDestroy() {
      if (this.videoStream) {
        this.videoStream.getTracks().forEach((track) => track.stop());
      }
    },
  },
};
</script>
