<script setup lang="ts">
import { useAppModeStore } from '@/stores/appMode'
import { useRouter } from 'vue-router'
import { TruckIcon, BriefcaseIcon } from '@heroicons/vue/24/outline'

const modeStore = useAppModeStore()
const router = useRouter()

function switchMode(mode: 'general' | 'rental') {
  modeStore.setMode(mode)
  router.push(mode === 'rental' ? '/rental' : '/')
}
</script>

<template>
  <!-- Mode switcher hidden: permanently in rental & tour mode -->
</template>
