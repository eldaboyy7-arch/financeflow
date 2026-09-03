<script setup lang="ts">
// BoySuccessIllustration.vue - Using authentic asset from user sprite sheet
</script>

<template>
  <div class="relative flex items-center justify-center select-none w-full max-w-[260px] sm:max-w-[300px] mx-auto py-2">
    <!-- Ambient Glow -->
    <div class="absolute inset-0 m-auto w-48 h-48 bg-emerald-100/60 dark:bg-emerald-950/30 rounded-full blur-xl pointer-events-none"></div>

    <img
      src="/assets/auth/boy_thumbs_up.png"
      alt="Password Berhasil"
      class="relative z-10 w-full h-auto object-contain max-h-[200px] drop-shadow-md animate-bounce-gentle"
    />
  </div>
</template>

<style scoped>
@keyframes bounceGentle {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-6px);
  }
}

.animate-bounce-gentle {
  animation: bounceGentle 2.8s ease-in-out infinite;
}
</style>
