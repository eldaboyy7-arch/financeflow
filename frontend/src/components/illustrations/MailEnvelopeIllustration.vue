<script setup lang="ts">
// MailEnvelopeIllustration.vue - Using authentic asset from user sprite sheet
</script>

<template>
  <div class="relative flex items-center justify-center select-none w-full max-w-[260px] sm:max-w-[300px] mx-auto py-2">
    <!-- Ambient Glow -->
    <div class="absolute inset-0 m-auto w-48 h-48 bg-blue-100/60 dark:bg-blue-950/30 rounded-full blur-xl pointer-events-none"></div>

    <!-- Paper Plane Floating in Top Right -->
    <div class="absolute -top-2 right-4 z-20 w-14 h-14 animate-float-plane">
      <img src="/assets/auth/paper_plane_loop.png" alt="Paper plane" class="w-full h-full object-contain" />
    </div>

    <!-- Main Envelope with Checkmark -->
    <img
      src="/assets/auth/envelope_check.png"
      alt="Cek Email"
      class="relative z-10 w-full h-auto object-contain max-h-[190px] drop-shadow-md animate-float-envelope"
    />
  </div>
</template>

<style scoped>
@keyframes floatEnvelope {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

@keyframes floatPlane {
  0%, 100% {
    transform: translate(0, 0) rotate(0deg);
  }
  50% {
    transform: translate(4px, -6px) rotate(3deg);
  }
}

.animate-float-envelope {
  animation: floatEnvelope 3.2s ease-in-out infinite;
}

.animate-float-plane {
  animation: floatPlane 2.8s ease-in-out infinite 0.2s;
}
</style>
