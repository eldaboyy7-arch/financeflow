<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import BrandLogo from '@/components/BrandLogo.vue'
import NotificationDropdown from '@/components/NotificationDropdown.vue'
import AiAdvisorDrawer from '@/components/AiAdvisorDrawer.vue'
import ReceiptScannerModal from '@/components/ReceiptScannerModal.vue'
import LogoutConfirmModal from '@/components/LogoutConfirmModal.vue'
import {
  Squares2X2Icon,
  ArrowsRightLeftIcon,
  BuildingLibraryIcon,
  TagIcon,
  ChartBarIcon,
  ChartPieIcon,
  SparklesIcon,
  ArrowPathIcon,
  Cog6ToothIcon,
  Bars3Icon,
  SunIcon,
  MoonIcon,
  ArrowRightStartOnRectangleIcon,
  QuestionMarkCircleIcon,
  XMarkIcon,
  CameraIcon,
  CreditCardIcon,
  DocumentTextIcon,
  LightBulbIcon,
  ArrowRightIcon,
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const uiStore = useUiStore()
const route = useRoute()
const router = useRouter()

const showLogoutModal = ref(false)
const aiAdvisorRef = ref<InstanceType<typeof AiAdvisorDrawer> | null>(null)

function handleLogout() {
  showLogoutModal.value = true
}

const showGuide = ref(false)
const guideStep = ref(0)
const mobileMenuOpen = ref(false)
const showScanner = ref(false)

function handleScanSaved() {
  showScanner.value = false
  if (route.path === '/transaksi' || route.path === '/') {
    window.location.reload()
  } else {
    router.push('/transaksi')
  }
}

// Otomatis tampilkan panduan untuk user baru / yang belum pernah lihat
onMounted(() => {
  const userId = authStore.user?.id
  if (userId) {
    const hasSeen = localStorage.getItem(`financeflow_seen_guide_${userId}`)
    if (!hasSeen) {
      setTimeout(() => {
        openGuide()
      }, 500)
    }
  }
})

function markGuideSeen() {
  const userId = authStore.user?.id
  if (userId) {
    localStorage.setItem(`financeflow_seen_guide_${userId}`, 'true')
  }
}

const navItems = [
  { name: 'dashboard',   route: '/',          icon: Squares2X2Icon,        label: 'Dasbor' },
  { name: 'transaksi',  route: '/transaksi',  icon: ArrowsRightLeftIcon,   label: 'Transaksi' },
  { name: 'anggaran',   route: '/anggaran',   icon: ChartPieIcon,          label: 'Anggaran' },
  { name: 'impian',     route: '/impian',     icon: SparklesIcon,          label: 'Impian & Goals' },
  { name: 'langganan',  route: '/langganan',  icon: ArrowPathIcon,         label: 'Tagihan Rutin' },
  { name: 'rekening',   route: '/rekening',   icon: BuildingLibraryIcon,   label: 'Rekening' },
  { name: 'kategori',   route: '/kategori',   icon: TagIcon,               label: 'Kategori' },
  { name: 'laporan',    route: '/laporan',    icon: ChartBarIcon,          label: 'Laporan' },
  { name: 'pengaturan', route: '/pengaturan', icon: Cog6ToothIcon,         label: 'Pengaturan' },
]

// 5 item utama untuk bottom navigation di mobile ala Instagram
const mobileNavItems = [
  { name: 'dashboard',   route: '/',          icon: Squares2X2Icon,        label: 'Dasbor' },
  { name: 'transaksi',  route: '/transaksi',  icon: ArrowsRightLeftIcon,   label: 'Transaksi' },
  { name: 'anggaran',   route: '/anggaran',   icon: ChartPieIcon,          label: 'Anggaran' },
  { name: 'laporan',    route: '/laporan',    icon: ChartBarIcon,          label: 'Laporan' },
  { name: 'rekening',   route: '/rekening',   icon: BuildingLibraryIcon,   label: 'Rekening' },
]

const guideSteps = [
  {
    step: 'Pengenalan',
    title: 'Selamat Datang di FinanceFlow',
    content: 'Kelola keuangan harian, tabungan impian, dan anggaran bulananmu dalam satu platform yang praktis dan terstruktur.',
    iconComponent: SparklesIcon,
    badge: 'Panduan Awal',
    tip: 'Ikuti 5 langkah singkat ini agar kamu bisa langsung memanfaatkan semua fitur secara optimal.',
    gradient: 'from-blue-500/20 via-indigo-500/10 to-transparent',
    color: 'text-[#0066FF] dark:text-blue-400',
    bgIcon: 'bg-blue-50 dark:bg-blue-950/70 border border-blue-100 dark:border-blue-900/60',
  },
  {
    step: 'Langkah 1',
    title: 'Atur Rekening & Dompet',
    content: 'Daftarkan semua tempat penyimpanan danamu: dompet tunai, rekening bank, maupun e-wallet. Saldo awal dapat disesuaikan kapan saja.',
    iconComponent: CreditCardIcon,
    badge: 'Rekening',
    tip: 'Kamu dapat memisahkan rekening operasional harian dengan pos tabungan khusus.',
    gradient: 'from-emerald-500/20 via-teal-500/10 to-transparent',
    color: 'text-emerald-600 dark:text-emerald-400',
    bgIcon: 'bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-100 dark:border-emerald-900/60',
  },
  {
    step: 'Langkah 2',
    title: 'Catat Uang Masuk & Keluar',
    content: 'Catat pemasukan atau pengeluaran secara teratur. Masukkan nominal, pilih kategori, dan tentukan rekening yang digunakan.',
    iconComponent: DocumentTextIcon,
    badge: 'Transaksi',
    tip: 'Saldo rekening akan otomatis diperbarui seketika setelah transaksi disimpan.',
    gradient: 'from-indigo-500/20 via-purple-500/10 to-transparent',
    color: 'text-indigo-600 dark:text-indigo-400',
    bgIcon: 'bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-100 dark:border-indigo-900/60',
  },
  {
    step: 'Langkah 3',
    title: 'Pindahkan Saldo Antar Rekening',
    content: 'Tarik tunai dari ATM atau isi saldo e-wallet dari bank? Gunakan menu Transfer agar mutasi antar rekening tercatat rapi.',
    iconComponent: ArrowsRightLeftIcon,
    badge: 'Transfer',
    tip: 'Kamu juga bisa mencantumkan biaya admin transaksi jika ada pemotongan.',
    gradient: 'from-amber-500/20 via-orange-500/10 to-transparent',
    color: 'text-amber-600 dark:text-amber-400',
    bgIcon: 'bg-amber-50 dark:bg-amber-950/70 border border-amber-100 dark:border-amber-900/60',
  },
  {
    step: 'Langkah 4',
    title: 'Kendalikan Anggaran & Target',
    content: 'Tentukan batas belanja bulanan per kategori dan buat target dana impian agar rencana keuanganmu tetap terkontrol.',
    iconComponent: ChartPieIcon,
    badge: 'Anggaran & Target',
    tip: 'Sistem memberi peringatan otomatis saat pengeluaran mendekati pagu anggaran.',
    gradient: 'from-purple-500/20 via-pink-500/10 to-transparent',
    color: 'text-purple-600 dark:text-purple-400',
    bgIcon: 'bg-purple-50 dark:bg-purple-950/70 border border-purple-100 dark:border-purple-900/60',
  },
  {
    step: 'Langkah 5',
    title: 'Evaluasi Melalui Laporan',
    content: 'Pantau arus kas, rasio tabungan, dan pola pengeluaran bulanan melalui grafik analitik yang informatif dan mudah dipahami.',
    iconComponent: ChartBarIcon,
    badge: 'Laporan & Analitik',
    tip: 'Data laporan membantu kamu mengambil keputusan finansial secara lebih cerdas.',
    gradient: 'from-rose-500/20 via-blue-500/10 to-transparent',
    color: 'text-rose-600 dark:text-rose-400',
    bgIcon: 'bg-rose-50 dark:bg-rose-950/70 border border-rose-100 dark:border-rose-900/60',
  },
]

function openGuide() {
  guideStep.value = 0
  showGuide.value = true
}

function closeGuide() {
  showGuide.value = false
  markGuideSeen()
}

function nextStep() {
  if (guideStep.value < guideSteps.length - 1) {
    guideStep.value++
  } else {
    closeGuide()
  }
}

function prevStep() {
  if (guideStep.value > 0) guideStep.value--
}

function isActive(item: { route: string }) {
  if (item.route === '/') return route.path === '/'
  return route.path.startsWith(item.route)
}

const userInitial = computed(() => authStore.user?.name?.charAt(0).toUpperCase() ?? '?')
</script>

<template>
  <div class="flex h-screen overflow-hidden bg-slate-50 dark:bg-slate-900">
    <!-- Desktop Sidebar (Hidden on Mobile) -->
    <aside
      :class="[
        'hidden md:flex flex-col bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 transition-all duration-300 z-20 shrink-0',
        uiStore.sidebarOpen ? 'w-60' : 'w-[60px]',
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center px-3.5 py-4 border-b border-slate-100 dark:border-slate-700 h-[61px]">
        <BrandLogo :variant="uiStore.sidebarOpen ? 'compact' : 'icon'" size="sm" />
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
        <RouterLink
          v-for="item in navItems"
          :key="item.name"
          :to="item.route"
          :title="!uiStore.sidebarOpen ? item.label : undefined"
          :class="[
            'flex items-center gap-3 px-2.5 py-2 rounded-lg transition-all duration-150 group',
            isActive(item)
              ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400'
              : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:text-slate-900 dark:hover:text-white',
          ]"
        >
          <component
            :is="item.icon"
            :class="[
              'w-5 h-5 shrink-0',
              isActive(item) ? 'stroke-[2]' : 'stroke-[1.5]',
            ]"
          />
          <span v-if="uiStore.sidebarOpen" class="text-sm font-medium truncate">{{ item.label }}</span>
        </RouterLink>
      </nav>

      <!-- User -->
      <div class="border-t border-slate-100 dark:border-slate-700 p-2">
        <div class="flex items-center gap-2.5 px-1 py-1">
          <div class="w-7 h-7 bg-primary-100 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0">
            <span class="text-xs font-semibold text-primary-700 dark:text-primary-300">{{ userInitial }}</span>
          </div>
          <div v-if="uiStore.sidebarOpen" class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ authStore.user?.name }}</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate leading-tight">{{ authStore.user?.email }}</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Topbar -->
      <header class="flex items-center justify-between px-4 sm:px-5 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 h-[61px] shrink-0">
        <!-- Left: Desktop Toggle / Mobile Hamburger & Brand -->
        <div class="flex items-center gap-2">
          <!-- Sidebar toggle on Desktop -->
          <button
            @click="uiStore.toggleSidebar"
            class="hidden md:flex p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
          >
            <Bars3Icon class="w-5 h-5" />
          </button>

          <!-- Mobile Hamburger Toggle -->
          <button
            @click="mobileMenuOpen = true"
            class="flex md:hidden p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
            title="Buka Menu Navigasi"
            aria-label="Buka Menu Navigasi"
          >
            <Bars3Icon class="w-6 h-6" />
          </button>

          <!-- Brand on Mobile -->
          <div class="flex md:hidden items-center">
            <BrandLogo variant="compact" size="sm" />
          </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-1.5">
          <!-- Tanya AI Assistant Button in Top Navbar -->
          <button
            @click="aiAdvisorRef?.toggleDrawer()"
            class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-[#0066FF] dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/50 font-bold text-xs transition-all shadow-xs active:scale-95 mr-0.5"
            title="Tanya FinanceFlow AI"
          >
            <SparklesIcon class="w-4 h-4 text-amber-500 shrink-0" />
            <span class="hidden sm:inline">Tanya AI</span>
          </button>

          <!-- Mobile Pengaturan shortcut -->
          <RouterLink
            to="/pengaturan"
            class="flex md:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
            title="Pengaturan"
          >
            <Cog6ToothIcon class="w-5 h-5" />
          </RouterLink>

          <!-- Notifications -->
          <NotificationDropdown />

          <!-- Help / Guide -->
          <button
            @click="openGuide"
            class="p-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 text-slate-400 dark:text-slate-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
            title="Panduan Pemakaian"
          >
            <QuestionMarkCircleIcon class="w-5 h-5" />
          </button>

          <!-- Dark mode toggle -->
          <button
            @click="uiStore.toggleTheme"
            class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
            :title="uiStore.theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"
          >
            <SunIcon v-if="uiStore.theme === 'dark'" class="w-5 h-5" />
            <MoonIcon v-else class="w-5 h-5" />
          </button>

          <!-- Logout -->
          <button
            @click="handleLogout"
            class="flex items-center gap-1.5 px-2.5 py-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors ml-1"
            title="Keluar"
          >
            <ArrowRightStartOnRectangleIcon class="w-4 h-4" />
            <span class="hidden sm:inline text-xs font-medium">Keluar</span>
          </button>
        </div>
      </header>

      <!-- Page content with smooth transitions -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 pb-24 md:pb-6">
        <RouterView v-slot="{ Component, route }">
          <Transition name="app-slide" mode="out-in">
            <KeepAlive :max="10">
              <component :is="Component" :key="route.name" />
            </KeepAlive>
          </Transition>
        </RouterView>
      </main>
    </div>

    <!-- Banking-style Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-t border-slate-200/90 dark:border-slate-800/90 shadow-[0_-4px_25px_rgba(0,0,0,0.06)] pb-[env(safe-area-inset-bottom)]">
      <div class="flex items-center justify-around h-[66px] px-2 relative max-w-lg mx-auto">
        <!-- 1. Dasbor -->
        <RouterLink
          to="/"
          :class="[
            'flex flex-col items-center justify-center flex-1 h-full py-1.5 transition-all duration-200 select-none relative',
            isActive({ route: '/' })
              ? 'text-[#0066FF] dark:text-[#389eff] font-bold'
              : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 font-medium',
          ]"
        >
          <div
            v-if="isActive({ route: '/' })"
            class="absolute top-0 w-8 h-[3px] bg-[#0066FF] dark:bg-[#389eff] rounded-b-full shadow-[0_2px_8px_rgba(0,102,255,0.6)]"
          ></div>
          <Squares2X2Icon :class="['w-6 h-6 transition-transform duration-200', isActive({ route: '/' }) ? 'stroke-[2.2] scale-105 text-[#0066FF] dark:text-[#389eff]' : 'stroke-[1.6]']" />
          <span class="text-[11px] tracking-tight mt-1 leading-tight">Dasbor</span>
        </RouterLink>

        <!-- 2. Transaksi -->
        <RouterLink
          to="/transaksi"
          :class="[
            'flex flex-col items-center justify-center flex-1 h-full py-1.5 transition-all duration-200 select-none relative',
            isActive({ route: '/transaksi' })
              ? 'text-[#0066FF] dark:text-[#389eff] font-bold'
              : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 font-medium',
          ]"
        >
          <div
            v-if="isActive({ route: '/transaksi' })"
            class="absolute top-0 w-8 h-[3px] bg-[#0066FF] dark:bg-[#389eff] rounded-b-full shadow-[0_2px_8px_rgba(0,102,255,0.6)]"
          ></div>
          <ArrowsRightLeftIcon :class="['w-6 h-6 transition-transform duration-200', isActive({ route: '/transaksi' }) ? 'stroke-[2.2] scale-105 text-[#0066FF] dark:text-[#389eff]' : 'stroke-[1.6]']" />
          <span class="text-[11px] tracking-tight mt-1 leading-tight">Transaksi</span>
        </RouterLink>

        <!-- 3. CENTER ELEVATED ACTION BUTTON: Scan Struk (Ala Bank / QRIS) -->
        <div class="flex-1 flex flex-col items-center justify-center h-full relative -top-3">
          <button
            @click="showScanner = true"
            class="w-[50px] h-[50px] rounded-full bg-[#0066FF] hover:bg-[#0052CC] text-white shadow-[0_6px_20px_rgba(0,102,255,0.45)] hover:shadow-[0_8px_25px_rgba(0,102,255,0.6)] active:scale-90 hover:scale-105 transition-all duration-200 ring-4 ring-white dark:ring-slate-900 flex items-center justify-center group"
            title="Scan Struk / Bukti Transaksi"
            aria-label="Scan Struk"
          >
            <CameraIcon class="w-6 h-6 transition-transform duration-200 group-hover:scale-110 stroke-[2.2]" />
          </button>
          <span class="text-[11px] font-bold text-[#0066FF] dark:text-[#389eff] tracking-tight mt-1 leading-tight">Scan</span>
        </div>

        <!-- 4. Anggaran -->
        <RouterLink
          to="/anggaran"
          :class="[
            'flex flex-col items-center justify-center flex-1 h-full py-1.5 transition-all duration-200 select-none relative',
            isActive({ route: '/anggaran' })
              ? 'text-[#0066FF] dark:text-[#389eff] font-bold'
              : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 font-medium',
          ]"
        >
          <div
            v-if="isActive({ route: '/anggaran' })"
            class="absolute top-0 w-8 h-[3px] bg-[#0066FF] dark:bg-[#389eff] rounded-b-full shadow-[0_2px_8px_rgba(0,102,255,0.6)]"
          ></div>
          <ChartPieIcon :class="['w-6 h-6 transition-transform duration-200', isActive({ route: '/anggaran' }) ? 'stroke-[2.2] scale-105 text-[#0066FF] dark:text-[#389eff]' : 'stroke-[1.6]']" />
          <span class="text-[11px] tracking-tight mt-1 leading-tight">Anggaran</span>
        </RouterLink>

        <!-- 5. Laporan -->
        <RouterLink
          to="/laporan"
          :class="[
            'flex flex-col items-center justify-center flex-1 h-full py-1.5 transition-all duration-200 select-none relative',
            isActive({ route: '/laporan' })
              ? 'text-[#0066FF] dark:text-[#389eff] font-bold'
              : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 font-medium',
          ]"
        >
          <div
            v-if="isActive({ route: '/laporan' })"
            class="absolute top-0 w-8 h-[3px] bg-[#0066FF] dark:bg-[#389eff] rounded-b-full shadow-[0_2px_8px_rgba(0,102,255,0.6)]"
          ></div>
          <ChartBarIcon :class="['w-6 h-6 transition-transform duration-200', isActive({ route: '/laporan' }) ? 'stroke-[2.2] scale-105 text-[#0066FF] dark:text-[#389eff]' : 'stroke-[1.6]']" />
          <span class="text-[11px] tracking-tight mt-1 leading-tight">Laporan</span>
        </RouterLink>
      </div>
    </nav>

    <!-- Global Mobile Smart Receipt Scanner Modal -->
    <ReceiptScannerModal v-model="showScanner" @saved="handleScanSaved" />

    <!-- Mobile Off-Canvas Navigation Drawer -->
    <Teleport to="body">
      <!-- Backdrop -->
      <Transition
        enter-active-class="transition-opacity duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="mobileMenuOpen"
          class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm md:hidden"
          @click="mobileMenuOpen = false"
        />
      </Transition>

      <!-- Slide-over Drawer Menu -->
      <Transition
        enter-active-class="transition-transform duration-300 ease-out"
        enter-from-class="-translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition-transform duration-200 ease-in"
        leave-from-class="translate-x-0"
        leave-to-class="-translate-x-full"
      >
        <div
          v-if="mobileMenuOpen"
          class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-white dark:bg-slate-800 shadow-2xl flex flex-col md:hidden border-r border-slate-200 dark:border-slate-700"
        >
          <!-- Header Drawer -->
          <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-100 dark:border-slate-700 h-[61px]">
            <BrandLogo variant="compact" size="sm" />
            <button
              @click="mobileMenuOpen = false"
              class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
              title="Tutup Menu"
            >
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <!-- Navigation Links (All Menu Items) -->
          <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-1">
            <RouterLink
              v-for="item in navItems"
              :key="item.name"
              :to="item.route"
              @click="mobileMenuOpen = false"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors',
                isActive(item)
                  ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-semibold'
                  : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60',
              ]"
            >
              <component
                :is="item.icon"
                :class="[
                  'w-5 h-5 shrink-0',
                  isActive(item) ? 'stroke-[2.2]' : 'stroke-[1.6]',
                ]"
              />
              <span>{{ item.label }}</span>
            </RouterLink>
          </nav>

          <!-- User Info & Logout in Drawer -->
          <div class="border-t border-slate-100 dark:border-slate-700 p-3 space-y-2">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-50 dark:bg-slate-700/50">
              <div class="w-8 h-8 bg-primary-100 dark:bg-primary-950/60 rounded-full flex items-center justify-center shrink-0">
                <span class="text-xs font-semibold text-primary-700 dark:text-primary-300">{{ userInitial }}</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ authStore.user?.name }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate">{{ authStore.user?.email }}</p>
              </div>
            </div>
            <button
              @click="mobileMenuOpen = false; handleLogout()"
              class="w-full flex items-center justify-center gap-2 py-2 px-3 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-xl transition-colors"
            >
              <ArrowRightStartOnRectangleIcon class="w-4 h-4" />
              <span>Keluar dari Akun</span>
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Guide Modal -->
    <!-- Guide / Onboarding Modal -->
    <Teleport to="body">
      <div v-if="showGuide" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6 select-none">
        <!-- Backdrop with blur -->
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity" @click="closeGuide"></div>

        <!-- Dialog Card -->
        <div class="relative bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-[28px] sm:rounded-[32px] w-full max-w-[480px] shadow-2xl shadow-blue-500/10 border border-slate-200/80 dark:border-slate-800 overflow-hidden">
          
          <!-- Subtle Top Ambient Glow -->
          <div 
            :class="[
              'absolute -top-20 -right-20 w-56 h-56 rounded-full blur-3xl opacity-70 pointer-events-none transition-all duration-500 bg-gradient-to-br',
              guideSteps[guideStep].gradient
            ]"
          ></div>

          <!-- Top Progress Bar -->
          <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800">
            <div
              class="h-full bg-gradient-to-r from-[#0066FF] to-blue-400 transition-all duration-300 ease-out"
              :style="{ width: `${((guideStep + 1) / guideSteps.length) * 100}%` }"
            ></div>
          </div>

          <!-- Header / Close button & Step indicator -->
          <div class="flex items-center justify-between px-6 pt-5 pb-2">
            <!-- Stepper indicators -->
            <div class="flex items-center gap-1.5">
              <span
                v-for="(_, i) in guideSteps"
                :key="i"
                :class="[
                  'h-1.5 rounded-full transition-all duration-300',
                  i === guideStep
                    ? 'bg-[#0066FF] w-6'
                    : i < guideStep
                      ? 'bg-[#0066FF]/30 dark:bg-blue-600/40 w-2.5'
                      : 'bg-slate-200 dark:bg-slate-700 w-2.5',
                ]"
              ></span>
            </div>

            <!-- Close icon button -->
            <button
              @click="closeGuide"
              class="p-1.5 rounded-full text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              title="Tutup panduan"
            >
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <!-- Step Content with Animation -->
          <Transition name="step-slide" mode="out-in">
            <div :key="guideStep" class="px-6 sm:px-8 py-4 space-y-4">
              <!-- Top Row: Icon + Badge -->
              <div class="flex items-center justify-between">
                <div 
                  :class="[
                    'w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center shadow-sm',
                    guideSteps[guideStep].bgIcon
                  ]"
                >
                  <component
                    :is="guideSteps[guideStep].iconComponent"
                    class="w-6 h-6 sm:w-7 sm:h-7"
                    :class="guideSteps[guideStep].color"
                  />
                </div>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60">
                  {{ guideSteps[guideStep].badge }}
                </span>
              </div>

              <!-- Title & Body -->
              <div class="space-y-1.5 pt-1">
                <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-snug">
                  {{ guideSteps[guideStep].title }}
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-normal">
                  {{ guideSteps[guideStep].content }}
                </p>
              </div>

              <!-- Contextual Highlight / Tip Box -->
              <div 
                v-if="guideSteps[guideStep].tip"
                class="p-3 sm:p-3.5 rounded-2xl bg-blue-50/70 dark:bg-slate-800/80 border border-blue-100/80 dark:border-slate-700/60 flex items-start gap-2.5"
              >
                <LightBulbIcon class="w-4 h-4 text-[#0066FF] dark:text-blue-400 shrink-0 mt-0.5" />
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                  {{ guideSteps[guideStep].tip }}
                </p>
              </div>
            </div>
          </Transition>

          <!-- Footer Actions -->
          <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-t border-slate-100 dark:border-slate-800/80 mt-2 bg-slate-50/50 dark:bg-slate-900/50">
            <button
              v-if="guideStep > 0"
              @click="prevStep"
              class="px-4 py-2 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-xl transition-all"
            >
              Kembali
            </button>
            <button
              v-else
              @click="closeGuide"
              class="px-4 py-2 text-xs sm:text-sm font-medium text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded-xl transition-colors"
            >
              Lewati dulu
            </button>

            <button
              @click="nextStep"
              class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs sm:text-sm font-bold bg-[#0066FF] hover:bg-[#0052CC] text-white rounded-xl shadow-md shadow-[#0066FF]/25 transition-all active:scale-95"
            >
              <span>{{ guideStep < guideSteps.length - 1 ? 'Lanjut' : 'Mulai Sekarang' }}</span>
              <ArrowRightIcon class="w-4 h-4 shrink-0" />
            </button>
          </div>

        </div>
      </div>
    </Teleport>

    <!-- AI Advisor Floating Drawer -->
    <AiAdvisorDrawer ref="aiAdvisorRef" />

    <!-- Logout Confirmation Modal -->
    <LogoutConfirmModal v-model="showLogoutModal" />
  </div>
</template>

<style scoped>
/* Smooth step transition */
.step-slide-enter-active,
.step-slide-leave-active {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.step-slide-enter-from {
  opacity: 0;
  transform: translateX(16px);
}
.step-slide-leave-to {
  opacity: 0;
  transform: translateX(-16px);
}
</style>
