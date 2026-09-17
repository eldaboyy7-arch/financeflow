<script setup lang="ts">
import { RouterView } from 'vue-router'
import TheNavbar from '@/components/TheNavbar.vue'
import TheFooter from '@/components/TheFooter.vue'
import FloatingWhatsappBar from '@/components/FloatingWhatsappBar.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
    <!-- Navbar (Sticky persistent WhatsApp access) -->
    <TheNavbar />

    <!-- Routed Page Content with smooth transition -->
    <main class="flex-1">
      <RouterView v-slot="{ Component }">
        <transition name="page-fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </RouterView>
    </main>

    <!-- Footer -->
    <TheFooter />

    <!-- Floating WhatsApp Quick Action Button -->
    <FloatingWhatsappBar />
  </div>
</template>
