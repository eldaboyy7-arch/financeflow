<script setup lang="ts">
import { ref, watch } from 'vue'
import { RouterView, useRouter } from 'vue-router'
import ToastContainer from '@/components/ToastContainer.vue'

const router = useRouter()
const transitionName = ref('page-fade')

// Detect navigation direction based on route depth
router.beforeEach((to, from) => {
  const toDepth = (to.meta.depth as number) ?? -1
  const fromDepth = (from.meta.depth as number) ?? -1

  // Both have depth → directional slide (auth pages)
  if (toDepth >= 0 && fromDepth >= 0) {
    transitionName.value = toDepth > fromDepth ? 'slide-left' : 'slide-right'
  }
  // Entering or leaving app → simple fade
  else {
    transitionName.value = 'page-fade'
  }
})
</script>

<template>
  <RouterView v-slot="{ Component, route }">
    <Transition :name="transitionName" mode="out-in">
      <component :is="Component" :key="route.path" />
    </Transition>
  </RouterView>
  <ToastContainer />
</template>

<style>
/* ====================================
   GLOBAL PAGE TRANSITIONS
   ==================================== */

/* --- Fade (layout switch / default) --- */
.page-fade-enter-active,
.page-fade-leave-active {
  transition: opacity 0.25s ease;
}
.page-fade-enter-from,
.page-fade-leave-to {
  opacity: 0;
}

/* --- Slide Left (navigating FORWARD → deeper page) ---
     New page slides in from right, old page slides out to left */
.slide-left-enter-active,
.slide-left-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-left-enter-from {
  transform: translateX(60px);
  opacity: 0;
}
.slide-left-leave-to {
  transform: translateX(-60px);
  opacity: 0;
}

/* --- Slide Right (navigating BACK → shallower page) ---
     New page slides in from left, old page slides out to right */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-right-enter-from {
  transform: translateX(-60px);
  opacity: 0;
}
.slide-right-leave-to {
  transform: translateX(60px);
  opacity: 0;
}

/* --- App inner page slide (subtle, fast) --- */
.app-slide-enter-active,
.app-slide-leave-active {
  transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1),
              opacity 0.22s cubic-bezier(0.4, 0, 0.2, 1);
}
.app-slide-enter-from {
  transform: translateY(8px);
  opacity: 0;
}
.app-slide-leave-to {
  transform: translateY(-4px);
  opacity: 0;
}
</style>
