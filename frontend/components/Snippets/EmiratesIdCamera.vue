<template>
  <div>
    <div class="mb-1">
      <v-btn class="primary mt-1" @click="takeSnapshot">{{
        isCamera ? "Take Picture" : "Scan Emirates Id"
      }}</v-btn>
    </div>
    <video
      style="border: 1px solid purple; aspect-ratio: 1;"
      width="900"
      v-show="isCamera"
      ref="video"
      autoplay
      playsinline
    ></video>
    <img v-show="!isCamera" src="" ref="img" alt="" />

    <canvas ref="canvas" style="display: none"></canvas>
  </div>
</template>

<script>
export default {
  auth: false,
  layout: "login",

  data: () => ({
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
        this.videoStream = mediaStream;
        video.play();
      } catch (error) {
        console.error("Error accessing the camera: ", error);
      }
    },
    takeSnapshot() {
      this.isCamera = !this.isCamera;
      const video = this.$refs.video;
      const canvas = this.$refs.canvas;
      const context = canvas.getContext("2d");

      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      context.drawImage(video, 0, 0, canvas.width, canvas.height);

      const imageData = canvas.toDataURL("image/png");

      const img = this.$refs.img;
      img.height = 250;
      img.src = imageData;

      this.$emit("imageSrc", imageData);
    },
    beforeDestroy() {
      if (this.videoStream) {
        this.videoStream.getTracks().forEach((track) => track.stop());
      }
    },
  },
};
</script>
