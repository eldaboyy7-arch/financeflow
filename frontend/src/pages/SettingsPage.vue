<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import { authApi } from '@/api/auth'
import LogoutConfirmModal from '@/components/LogoutConfirmModal.vue'
import SelectInput from '@/components/SelectInput.vue'
import type { SelectOption } from '@/components/SelectInput.vue'
import {
  UserCircleIcon,
  LockClosedIcon,
  SwatchIcon,
  ArrowRightStartOnRectangleIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  ShieldCheckIcon,
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()
const uiStore = useUiStore()

const showLogoutModal = ref(false)

const currencyOptions: SelectOption[] = [
  { value: 'IDR', label: 'IDR — Rupiah Indonesia' },
  { value: 'USD', label: 'USD — US Dollar' },
  { value: 'SGD', label: 'SGD — Singapore Dollar' },
  { value: 'MYR', label: 'MYR — Malaysian Ringgit' },
]

const profileForm = ref({
  name: authStore.user?.name ?? '',
  currency: authStore.user?.currency ?? 'IDR',
})
const profileSuccess = ref('')
const profileError = ref('')
const profileLoading = ref(false)

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
})
const passwordSuccess = ref('')
const passwordError = ref('')
const passwordLoading = ref(false)

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}

async function updateProfile() {
  profileSuccess.value = ''
  profileError.value = ''
  profileLoading.value = true
  try {
    const { data } = await authApi.updateProfile(profileForm.value)
    authStore.user = data.user
    profileSuccess.value = 'Profil berhasil diperbarui.'
    uiStore.showToast('Profil berhasil diperbarui!')
  } catch (err: any) {
    profileError.value = err.response?.data?.message ?? 'Terjadi kesalahan.'
  } finally {
    profileLoading.value = false
  }
}

async function changePassword() {
  passwordSuccess.value = ''
  passwordError.value = ''
  passwordLoading.value = true
  try {
    await authApi.changePassword(passwordForm.value)
    passwordSuccess.value = 'Password berhasil diubah.'
    uiStore.showToast('Password berhasil diubah!')
    passwordForm.value = { current_password: '', password: '', password_confirmation: '' }
  } catch (err: any) {
    passwordError.value = err.response?.data?.message ?? 'Terjadi kesalahan.'
  } finally {
    passwordLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-4 sm:space-y-5 max-w-2xl">
    <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">Pengaturan</h1>

    <!-- Profile Section -->
    <div class="card p-4 sm:p-6">
      <div class="flex items-center gap-2.5 mb-5">
        <div class="w-8 h-8 bg-primary-50 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
          <UserCircleIcon class="w-5 h-5 text-primary-600 dark:text-primary-400" />
        </div>
        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Profil</h2>
      </div>

      <!-- Avatar display -->
      <div class="flex items-center gap-4 mb-6 p-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl">
        <div class="w-14 h-14 bg-primary-100 dark:bg-primary-900/50 rounded-2xl flex items-center justify-center shrink-0">
          <span class="text-xl font-bold text-primary-700 dark:text-primary-300">
            {{ authStore.user?.name?.charAt(0).toUpperCase() }}
          </span>
        </div>
        <div>
          <p class="font-semibold text-slate-900 dark:text-white">{{ authStore.user?.name }}</p>
          <p class="text-sm text-slate-500 dark:text-slate-400">{{ authStore.user?.email }}</p>
        </div>
      </div>

      <form @submit.prevent="updateProfile" class="space-y-4">
        <div>
          <label class="label">Nama Lengkap</label>
          <input v-model="profileForm.name" type="text" class="input" required />
        </div>
        <div>
          <label class="label">Mata Uang Default</label>
          <SelectInput
            v-model="profileForm.currency"
            :options="currencyOptions"
            placeholder="Pilih mata uang"
          />
        </div>
        <div v-if="profileSuccess" class="flex items-center gap-2 text-sm text-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-400 rounded-lg px-3 py-2">
          <CheckCircleIcon class="w-4 h-4 shrink-0" />
          {{ profileSuccess }}
        </div>
        <div v-if="profileError" class="flex items-center gap-2 text-sm text-rose-600 bg-rose-50 dark:bg-rose-900/20 rounded-lg px-3 py-2">
          <ExclamationCircleIcon class="w-4 h-4 shrink-0" />
          {{ profileError }}
        </div>
        <button type="submit" :disabled="profileLoading" class="btn-primary">
          {{ profileLoading ? 'Menyimpan...' : 'Simpan Profil' }}
        </button>
      </form>
    </div>

    <!-- Theme Section -->
    <div class="card p-4 sm:p-6">
      <div class="flex items-center gap-2.5 mb-4 sm:mb-5">
        <div class="w-8 h-8 bg-amber-50 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
          <SwatchIcon class="w-5 h-5 text-amber-600 dark:text-amber-400" />
        </div>
        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Tampilan</h2>
      </div>
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Mode Gelap</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gunakan tampilan gelap untuk kenyamanan mata</p>
        </div>
        <button
          @click="uiStore.toggleTheme"
          :class="[
            'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2',
            uiStore.theme === 'dark' ? 'bg-primary-600' : 'bg-slate-200 dark:bg-slate-600',
          ]"
        >
          <span
            :class="[
              'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
              uiStore.theme === 'dark' ? 'translate-x-6' : 'translate-x-1',
            ]"
          />
        </button>
      </div>
    </div>

    <!-- Security Status Widget -->
    <div class="card p-4 sm:p-5 bg-gradient-to-r from-emerald-50/80 via-blue-50/40 to-white dark:from-slate-800/90 dark:via-slate-800/60 dark:to-slate-800/40 border border-emerald-200/60 dark:border-slate-700/60 flex items-center justify-between gap-4 overflow-hidden relative">
      <div class="space-y-1.5 z-10">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100/70 dark:bg-emerald-900/40 text-[10px] font-bold text-emerald-700 dark:text-emerald-300">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Aktif & Terlindungi</span>
        </div>
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
          Akun Kamu Aman & Terenkripsi
        </h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm leading-relaxed">
          Autentikasi menggunakan Laravel Sanctum API Token. Data finansialmu tersimpan secara aman dan terenkripsi.
        </p>
      </div>
      <div class="shrink-0 hidden sm:flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-100/70 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/40">
        <ShieldCheckIcon class="w-8 h-8" />
      </div>
    </div>

    <!-- Change Password -->
    <div class="card p-4 sm:p-6">
      <div class="flex items-center gap-2.5 mb-4 sm:mb-5">
        <div class="w-8 h-8 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center">
          <LockClosedIcon class="w-5 h-5 text-slate-500 dark:text-slate-400" />
        </div>
        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Ubah Password</h2>
      </div>
      <form @submit.prevent="changePassword" class="space-y-4">
        <div>
          <label class="label">Password Saat Ini</label>
          <input v-model="passwordForm.current_password" type="password" class="input" required />
        </div>
        <div>
          <label class="label">Password Baru</label>
          <input v-model="passwordForm.password" type="password" class="input" required />
        </div>
        <div>
          <label class="label">Konfirmasi Password Baru</label>
          <input v-model="passwordForm.password_confirmation" type="password" class="input" required />
        </div>
        <div v-if="passwordSuccess" class="flex items-center gap-2 text-sm text-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-400 rounded-lg px-3 py-2">
          <CheckCircleIcon class="w-4 h-4 shrink-0" />
          {{ passwordSuccess }}
        </div>
        <div v-if="passwordError" class="flex items-center gap-2 text-sm text-rose-600 bg-rose-50 dark:bg-rose-900/20 rounded-lg px-3 py-2">
          <ExclamationCircleIcon class="w-4 h-4 shrink-0" />
          {{ passwordError }}
        </div>
        <button type="submit" :disabled="passwordLoading" class="btn-primary">
          {{ passwordLoading ? 'Mengubah...' : 'Ubah Password' }}
        </button>
      </form>
    </div>

    <!-- Logout -->
    <div class="card p-4 sm:p-6 border border-rose-100 dark:border-rose-900/40">
      <div class="flex items-center gap-2.5 mb-4">
        <div class="w-8 h-8 bg-rose-50 dark:bg-rose-900/30 rounded-lg flex items-center justify-center">
          <ArrowRightStartOnRectangleIcon class="w-5 h-5 text-rose-500" />
        </div>
        <h2 class="text-sm font-semibold text-rose-600 dark:text-rose-400">Keluar</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
        Anda akan keluar dari semua sesi pada perangkat ini.
      </p>
      <button @click="showLogoutModal = true" class="btn-danger text-sm flex items-center gap-2">
        <ArrowRightStartOnRectangleIcon class="w-4 h-4" />
        Keluar dari Akun
      </button>
    </div>

    <!-- Logout Confirmation Modal -->
    <LogoutConfirmModal v-model="showLogoutModal" />
  </div>
</template>
