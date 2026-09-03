<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { insightsApi } from '@/api/insights'
import type { SmartInsightItem } from '@/types/insight'
import {
  SparklesIcon,
  LightBulbIcon,
  ArrowTrendingUpIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps<{
  month?: number
  year?: number
}>()

const insights = ref<SmartInsightItem[]>([])
const loading = ref(false)

async function fetchInsights(force = false) {
  if (insights.value.length === 0 || force) {
    loading.value = true
  }
  try {
    const { data } = await insightsApi.get({
      month: props.month,
      year: props.year,
    })
    insights.value = data.data
  } catch (e) {
    console.error('Failed to load smart insights:', e)
  } finally {
    loading.value = false
  }
}

watch([() => props.month, () => props.year], () => {
  fetchInsights()
})

onMounted(() => {
  fetchInsights()
})

defineExpose({ fetchInsights })
</script>

<template>
  <div v-if="insights.length || loading" class="card p-4 sm:p-5 space-y-3.5">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <!-- Compact 3D Insight Illustration -->
        <img
          src="/assets/3d/analytics_insight_boy.png"
          alt="Insight"
          class="w-10 h-10 object-cover rounded-full drop-shadow-sm shrink-0 bg-blue-50 dark:bg-slate-700"
        />
        <div>
          <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-none">
            Rangkuman Bulan Ini
          </h2>
          <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
            Catatan dan tren pengeluaran keuanganmu
          </p>
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <div v-for="i in 2" :key="i" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 animate-pulse space-y-2 border border-slate-100 dark:border-slate-800">
        <div class="w-24 h-3.5 bg-slate-200 dark:bg-slate-700 rounded-md"></div>
        <div class="w-full h-3 bg-slate-200 dark:bg-slate-700 rounded-md"></div>
      </div>
    </div>

    <!-- Insights Grid (Max 3-4 cards) -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
      <div
        v-for="item in insights"
        :key="item.id"
        class="p-3.5 rounded-2xl border transition-all hover:shadow-sm flex items-start gap-3"
        :class="[
          item.type === 'danger'
            ? 'bg-rose-50/60 dark:bg-rose-950/20 border-rose-200/70 dark:border-rose-900/40 text-rose-950 dark:text-rose-100'
            : item.type === 'warning'
            ? 'bg-amber-50/60 dark:bg-amber-950/20 border-amber-200/70 dark:border-amber-900/40 text-amber-950 dark:text-amber-100'
            : item.type === 'success'
            ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200/70 dark:border-emerald-900/40 text-emerald-950 dark:text-emerald-100'
            : 'bg-primary-50/50 dark:bg-primary-950/20 border-primary-200/60 dark:border-primary-900/40 text-primary-950 dark:text-primary-100',
        ]"
      >
        <!-- Clean SVG Icon according to type -->
        <div class="shrink-0 mt-0.5">
          <ExclamationTriangleIcon
            v-if="item.type === 'danger'"
            class="w-5 h-5 text-rose-600 dark:text-rose-400"
          />
          <ExclamationTriangleIcon
            v-else-if="item.type === 'warning'"
            class="w-5 h-5 text-amber-600 dark:text-amber-400"
          />
          <CheckCircleIcon
            v-else-if="item.type === 'success'"
            class="w-5 h-5 text-emerald-600 dark:text-emerald-400"
          />
          <SparklesIcon
            v-else
            class="w-5 h-5 text-[#0066FF] dark:text-blue-400"
          />
        </div>

        <div class="flex-1 min-w-0">
          <h3 class="text-xs font-bold leading-tight truncate">
            {{ item.title }}
          </h3>
          <p class="text-[11px] opacity-85 mt-1 leading-relaxed">
            {{ item.message }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
