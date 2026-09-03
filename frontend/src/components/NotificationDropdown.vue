<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useNotificationsStore } from '@/stores/notifications'
import { useRouter } from 'vue-router'
import {
  BellIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  ExclamationCircleIcon,
  InformationCircleIcon,
  CheckIcon,
  ClockIcon,
} from '@heroicons/vue/24/outline'

const store = useNotificationsStore()
const router = useRouter()
const open = ref(false)
const activeTab = ref<'all' | 'unread'>('all')
const containerRef = ref<HTMLElement | null>(null)

function toggle() {
  open.value = !open.value
  if (open.value) {
    store.fetchNotifications()
  }
}

async function handleClickItem(item: any) {
  if (!item.read) {
    await store.markAsRead(item.id)
  }
  open.value = false
  if (item.link) {
    router.push(item.link)
  }
}

async function handleMarkAllAsRead() {
  await store.markAllAsRead()
}

const filteredNotifications = computed(() => {
  if (activeTab.value === 'unread') {
    return store.notifications.filter((n) => !n.read)
  }
  return store.notifications
})

function onOutsideClick(e: MouseEvent) {
  if (containerRef.value && !containerRef.value.contains(e.target as Node)) {
    open.value = false
  }
}

onMounted(() => {
  store.fetchNotifications()
  document.addEventListener('mousedown', onOutsideClick)
})

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', onOutsideClick)
})
</script>

<template>
  <div ref="containerRef" class="relative">
    <!-- Bell Trigger Button -->
    <button
      type="button"
      @click="toggle"
      class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none"
      title="Notifikasi"
    >
      <BellIcon class="w-5 h-5" />
      <!-- Unread Badge Counter (Disappears when unreadCount is 0) -->
      <span
        v-if="store.unreadCount > 0"
        class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center animate-pulse shadow-sm"
      >
        {{ store.unreadCount > 9 ? '9+' : store.unreadCount }}
      </span>
    </button>

    <!-- Dropdown Drawer -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="open"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl z-[120] overflow-hidden"
      >
        <!-- Header -->
        <div class="px-4 pt-3.5 pb-2.5 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/80">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Notifikasi</h3>
              <span
                v-if="store.unreadCount > 0"
                class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/70 text-rose-600 dark:text-rose-400"
              >
                {{ store.unreadCount }} baru
              </span>
            </div>
            <button
              v-if="store.unreadCount > 0"
              @click="handleMarkAllAsRead"
              class="text-[11px] font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 hover:underline flex items-center gap-1"
            >
              <CheckIcon class="w-3.5 h-3.5 stroke-2" />
              Tandai semua dibaca
            </button>
          </div>

          <!-- Segmented Filter Tabs (Semua vs Belum Dibaca) -->
          <div class="flex gap-1 mt-2.5 p-0.5 bg-slate-200/70 dark:bg-slate-900/80 rounded-xl">
            <button
              type="button"
              @click="activeTab = 'all'"
              :class="[
                'flex-1 py-1 text-xs font-semibold rounded-lg transition-all',
                activeTab === 'all'
                  ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs'
                  : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white',
              ]"
            >
              Semua ({{ store.notifications.length }})
            </button>
            <button
              type="button"
              @click="activeTab = 'unread'"
              :class="[
                'flex-1 py-1 text-xs font-semibold rounded-lg transition-all',
                activeTab === 'unread'
                  ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs'
                  : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white',
              ]"
            >
              Belum Dibaca ({{ store.unreadCount }})
            </button>
          </div>
        </div>

        <!-- Notification List (Persistent items) -->
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
          <div
            v-for="item in filteredNotifications"
            :key="item.id"
            @click="handleClickItem(item)"
            :class="[
              'p-3.5 flex items-start gap-3 cursor-pointer transition-colors relative border-l-3',
              !item.read
                ? 'bg-primary-50/20 dark:bg-primary-950/20 border-l-primary-500 hover:bg-primary-50/40 dark:hover:bg-primary-950/30'
                : 'bg-white dark:bg-slate-900 border-l-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50 opacity-85',
            ]"
          >
            <!-- Icon -->
            <div class="mt-0.5 shrink-0">
              <div
                v-if="item.type === 'danger'"
                class="w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center"
              >
                <ExclamationCircleIcon class="w-4 h-4 stroke-2" />
              </div>
              <div
                v-else-if="item.type === 'warning'"
                class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center"
              >
                <ExclamationTriangleIcon class="w-4 h-4 stroke-2" />
              </div>
              <div
                v-else
                class="w-7 h-7 rounded-lg bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 flex items-center justify-center"
              >
                <InformationCircleIcon class="w-4 h-4 stroke-2" />
              </div>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-1.5">
                <p
                  :class="[
                    'text-xs truncate',
                    !item.read
                      ? 'font-bold text-slate-900 dark:text-white'
                      : 'font-medium text-slate-700 dark:text-slate-300',
                  ]"
                >
                  {{ item.title }}
                </p>
                <!-- Glowing unread dot -->
                <span
                  v-if="!item.read"
                  class="w-2 h-2 rounded-full bg-primary-500 shrink-0 shadow-xs"
                  title="Belum dibaca"
                ></span>
              </div>

              <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5 leading-snug line-clamp-2">
                {{ item.message }}
              </p>

              <!-- Timestamp & Read State -->
              <div class="flex items-center justify-between mt-1 text-[10px] text-slate-400">
                <span>{{ item.created_at ? item.created_at.slice(0, 10) : 'Baru saja' }}</span>
                <span v-if="item.read" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 font-medium">
                  <CheckIcon class="w-3 h-3 stroke-2" />
                  Dibaca
                </span>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="!filteredNotifications.length" class="p-8 text-center text-slate-400 dark:text-slate-500 space-y-1">
            <CheckCircleIcon class="w-8 h-8 mx-auto opacity-40 text-emerald-500" />
            <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">
              {{ activeTab === 'unread' ? 'Tidak ada notifikasi belum dibaca' : 'Tidak ada notifikasi' }}
            </p>
            <p class="text-[10px] text-slate-400">Semua tagihan dan anggaranmu terkendali!</p>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>
