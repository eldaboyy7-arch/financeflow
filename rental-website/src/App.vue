<script setup lang="ts">
import { RouterView } from 'vue-router'
import TheNavbar from '@/components/TheNavbar.vue'
import TheFooter from '@/components/TheFooter.vue'
import FloatingWhatsappBar from '@/components/FloatingWhatsappBar.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
    <!-- Navbar -->
    <TheNavbar />

    <!-- Routed Page Content -->
    <main class="flex-1">
      <RouterView />
    </main>

    <!-- Footer -->
    <TheFooter />

    <!-- Sticky Floating WhatsApp Bar (Mobile only) -->
    <FloatingWhatsappBar />
  </div>
</template>
