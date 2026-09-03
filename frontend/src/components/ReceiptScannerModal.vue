<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { receiptApi } from '@/api/receipt'
import { transactionsApi } from '@/api/transactions'
import { budgetsApi } from '@/api/budgets'
import { useAccountsStore } from '@/stores/accounts'
import { useCategoriesStore } from '@/stores/categories'
import { useUiStore } from '@/stores/ui'
import type { ReceiptDraft } from '@/types/receipt'
import type { BudgetImpact } from '@/types/budget'
import type { SelectOption } from '@/components/SelectInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import DateInput from '@/components/DateInput.vue'
import CurrencyInput from '@/components/CurrencyInput.vue'
import {
  CameraIcon,
  PhotoIcon,
  ClipboardDocumentIcon,
  XMarkIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  SparklesIcon,
  ExclamationTriangleIcon,
  ExclamationCircleIcon,
  ArrowsRightLeftIcon,
  CheckIcon,
  DocumentMagnifyingGlassIcon,
  BuildingStorefrontIcon,
  BanknotesIcon,
  TagIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void
  (e: 'saved', tx: any): void
}>()

const accountsStore = useAccountsStore()
const categoriesStore = useCategoriesStore()
const uiStore = useUiStore()

// ── State ──────────────────────────────────────────────────────
type Mode = 'camera' | 'upload' | 'paste'
const activeMode = ref<Mode>('upload')
const step = ref<'input' | 'scanning' | 'review'>('input')

// Camera
const videoRef = ref<HTMLVideoElement | null>(null)
const mediaStream = ref<MediaStream | null>(null)
const cameraError = ref('')
const facingMode = ref<'environment' | 'user'>('environment')
const hasCamera = ref(true)

// Captured / Uploaded Image
const previewUrl = ref<string | null>(null)
const imageFile = ref<File | null>(null)
const receiptPath = ref<string | null>(null)

// Scanning Progress & Animated Steps
const scanPhaseIndex = ref(0)
const scanProgress = ref(15)
let scanTimer: any = null

const scanPhases = [
  { icon: DocumentMagnifyingGlassIcon, title: 'Membaca teks struk & OCR...', sub: 'Memindai karakter, baris, dan angka' },
  { icon: BuildingStorefrontIcon,      title: 'Mendeteksi nama toko & tanggal...', sub: 'Mengenali merchant dan tanggal struk' },
  { icon: BanknotesIcon,              title: 'Mengekstrak nominal & rincian...', sub: 'Menghitung total pembayaran dan diskon' },
  { icon: TagIcon,                    title: 'Menyesuaikan kategori & rekening...', sub: 'Mencocokkan ke pos keuangan akun Anda' },
]

// AI Draft & Editing
const draft = ref<ReceiptDraft | null>(null)
const budgetImpact = ref<BudgetImpact | null>(null)
const scanError = ref('')
const saving = ref(false)
const saveError = ref('')

// Form model for Review Step
const formAmount = ref<number>(0)
const formDate = ref<string>(new Date().toISOString().split('T')[0])
const formType = ref<'income' | 'expense'>('expense')
const formCategoryId = ref<string>('')
const formAccountId = ref<string>('')
const formMerchant = ref<string>('')
const formDescription = ref<string>('')

async function checkBudgetImpact() {
  if (!formCategoryId.value || !formAmount.value || formType.value !== 'expense') {
    budgetImpact.value = null
    return
  }
  try {
    const { data } = await budgetsApi.impact({
      category_id: Number(formCategoryId.value),
      amount: Number(formAmount.value),
    })
    budgetImpact.value = data.impact
  } catch (e) {
    budgetImpact.value = null
  }
}

watch([formCategoryId, formAmount, formType, step], () => {
  if (step.value === 'review') {
    checkBudgetImpact()
  }
})

// ── Options ────────────────────────────────────────────────────
const typeOptions: SelectOption[] = [
  { value: 'expense', label: 'Pengeluaran' },
  { value: 'income',  label: 'Pemasukan' },
]

const categoryOptions = computed<SelectOption[]>(() => {
  const list = formType.value === 'income'
    ? categoriesStore.incomeCategories
    : categoriesStore.expenseCategories
  return list.map((c) => ({ value: String(c.id), label: c.name, icon: c.icon }))
})

const accountOptions = computed<SelectOption[]>(() =>
  accountsStore.activeAccounts.map((a) => ({ value: String(a.id), label: a.name }))
)

// ── Ensure stores are ready ────────────────────────────────────
async function initStores() {
  if (!categoriesStore.categories.length || !accountsStore.accounts.length) {
    try {
      await Promise.all([
        categoriesStore.fetchCategories(),
        accountsStore.fetchAccounts(),
      ])
    } catch (e) {
      console.warn('Failed to pre-fetch categories/accounts in scanner:', e)
    }
  }
}

// ── Lifecycle & Watchers ───────────────────────────────────────
watch(
  () => props.modelValue,
  async (open) => {
    if (open) {
      resetAll()
      await initStores()
      if (activeMode.value === 'camera') startCamera()
      window.addEventListener('paste', handleGlobalPaste)
    } else {
      stopCamera()
      stopScanningAnimation()
      window.removeEventListener('paste', handleGlobalPaste)
    }
  }
)

watch(activeMode, (newMode) => {
  if (newMode === 'camera') {
    startCamera()
  } else {
    stopCamera()
  }
})

onMounted(async () => {
  await initStores()
  if (props.modelValue && activeMode.value === 'camera') {
    startCamera()
  }
})

onUnmounted(() => {
  stopCamera()
  stopScanningAnimation()
  window.removeEventListener('paste', handleGlobalPaste)
})

// ── Camera Handling ────────────────────────────────────────────
async function startCamera() {
  stopCamera()
  cameraError.value = ''
  hasCamera.value = true

  try {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      throw new Error('Kamera tidak didukung pada browser ini.')
    }

    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: facingMode.value,
        width: { ideal: 1920 },
        height: { ideal: 1080 },
      },
      audio: false,
    })

    mediaStream.value = stream
    if (videoRef.value) {
      videoRef.value.srcObject = stream
      await videoRef.value.play()
    }
  } catch (err: any) {
    console.warn('Camera access failed:', err)
    hasCamera.value = false
    cameraError.value = err?.message?.includes('denied')
      ? 'Izin kamera ditolak. Silakan izinkan akses kamera di browser atau gunakan opsi Unggah Foto.'
      : 'Kamera tidak dapat diakses pada perangkat ini.'
    activeMode.value = 'upload'
  }
}

function stopCamera() {
  if (mediaStream.value) {
    mediaStream.value.getTracks().forEach((t) => t.stop())
    mediaStream.value = null
  }
  if (videoRef.value) {
    videoRef.value.srcObject = null
  }
}

function flipCamera() {
  facingMode.value = facingMode.value === 'environment' ? 'user' : 'environment'
  startCamera()
}

function captureFromCamera() {
  if (!videoRef.value) return
  const video = videoRef.value
  const canvas = document.createElement('canvas')
  canvas.width = video.videoWidth || 1280
  canvas.height = video.videoHeight || 720
  const ctx = canvas.getContext('2d')
  if (!ctx) return

  ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
  canvas.toBlob(
    async (blob) => {
      if (!blob) return
      const file = new File([blob], `receipt_${Date.now()}.jpg`, { type: 'image/jpeg' })
      stopCamera()
      await processImage(file)
    },
    'image/jpeg',
    0.88
  )
}

// ── File Upload & Clipboard Paste ──────────────────────────────
async function onFileSelected(e: Event) {
  const input = e.target as HTMLInputElement
  if (input.files && input.files[0]) {
    await processImage(input.files[0])
    input.value = ''
  }
}

function onDrop(e: DragEvent) {
  e.preventDefault()
  if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
    processImage(e.dataTransfer.files[0])
  }
}

function handleGlobalPaste(e: ClipboardEvent) {
  if (!props.modelValue || step.value !== 'input') return
  const items = e.clipboardData?.items
  if (!items) return

  for (let i = 0; i < items.length; i++) {
    if (items[i].type.indexOf('image') !== -1) {
      const file = items[i].getAsFile()
      if (file) {
        e.preventDefault()
        processImage(file)
        break
      }
    }
  }
}

// ── Image Preprocessing & API Scan ─────────────────────────────
async function compressImage(file: File, maxWidth = 1280, quality = 0.85): Promise<File> {
  return new Promise((resolve) => {
    const img = new Image()
    const reader = new FileReader()

    reader.onload = (e) => {
      img.src = e.target?.result as string
    }

    img.onload = () => {
      const canvas = document.createElement('canvas')
      let { width, height } = img

      if (width > maxWidth) {
        height = Math.round((height * maxWidth) / width)
        width = maxWidth
      }

      canvas.width = width
      canvas.height = height

      const ctx = canvas.getContext('2d')
      if (!ctx) {
        resolve(file)
        return
      }

      ctx.drawImage(img, 0, 0, width, height)
      canvas.toBlob(
        (blob) => {
          if (!blob) {
            resolve(file)
            return
          }
          const compressed = new File([blob], file.name.replace(/\.[^/.]+$/, '') + '.jpg', {
            type: 'image/jpeg',
            lastModified: Date.now(),
          })
          resolve(compressed)
        },
        'image/jpeg',
        quality
      )
    }

    img.onerror = () => resolve(file)
    reader.readAsDataURL(file)
  })
}

function startScanningAnimation() {
  scanPhaseIndex.value = 0
  scanProgress.value = 20
  scanTimer = setInterval(() => {
    if (scanPhaseIndex.value < scanPhases.length - 1) {
      scanPhaseIndex.value++
      scanProgress.value = Math.min(88, scanProgress.value + 22)
    }
  }, 1200)
}

function stopScanningAnimation() {
  if (scanTimer) {
    clearInterval(scanTimer)
    scanTimer = null
  }
}

async function processImage(rawFile: File) {
  scanError.value = ''
  step.value = 'scanning'
  startScanningAnimation()

  // Make sure stores are populated
  await initStores()

  // Compress on client
  const compressed = await compressImage(rawFile)
  imageFile.value = compressed
  previewUrl.value = URL.createObjectURL(compressed)

  const formData = new FormData()
  formData.append('image', compressed)

  try {
    const { data } = await receiptApi.scan(formData)
    stopScanningAnimation()
    scanProgress.value = 100

    draft.value = data.draft
    receiptPath.value = data.receipt_path

    // Prefill form from AI draft
    formAmount.value = data.draft.amount || 0
    formDate.value = data.draft.date || new Date().toISOString().split('T')[0]
    formType.value = data.draft.type || 'expense'
    formMerchant.value = data.draft.merchant || ''
    formDescription.value = data.draft.description || ''

    // Set Category ID with safe fallback
    if (data.draft.category_id) {
      formCategoryId.value = String(data.draft.category_id)
    } else if (categoryOptions.value.length) {
      formCategoryId.value = String(categoryOptions.value[0].value)
    }

    // Set Account ID with safe fallback
    if (data.draft.account_id) {
      formAccountId.value = String(data.draft.account_id)
    } else if (accountOptions.value.length) {
      formAccountId.value = String(accountOptions.value[0].value)
    }

    step.value = 'review'
  } catch (err: any) {
    stopScanningAnimation()
    const msg = err.response?.data?.message ?? 'Gagal memproses struk dengan AI. Silakan coba foto yang lebih jelas.'
    scanError.value = msg
    step.value = 'input'
  }
}

// ── Save Transaction ───────────────────────────────────────────
async function saveTransaction() {
  saveError.value = ''
  saving.value = true

  try {
    const payload = {
      type: formType.value,
      amount: formAmount.value,
      date: formDate.value,
      category_id: Number(formCategoryId.value),
      account_id: Number(formAccountId.value),
      description: formDescription.value || formMerchant.value || 'Transaksi dari scan struk',
      receipt_path: receiptPath.value || undefined,
    }

    const { data } = await transactionsApi.create(payload)
    uiStore.showToast('Transaksi dari struk berhasil disimpan!')
    emit('saved', data.data)
    closeModal()
    await accountsStore.fetchAccounts()
  } catch (err: any) {
    const d = err.response?.data
    if (d?.errors) {
      saveError.value = Object.values(d.errors as Record<string, string[]>).flat().join(' ')
    } else {
      saveError.value = d?.message ?? 'Gagal menyimpan transaksi.'
    }
  } finally {
    saving.value = false
  }
}

function resetAll() {
  stopScanningAnimation()
  step.value = 'input'
  activeMode.value = 'upload'
  previewUrl.value = null
  imageFile.value = null
  draft.value = null
  receiptPath.value = null
  scanError.value = ''
  saveError.value = ''
  saving.value = false
  scanPhaseIndex.value = 0
  scanProgress.value = 15
}

function closeModal() {
  stopCamera()
  stopScanningAnimation()
  emit('update:modelValue', false)
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto"
    >
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-md transition-opacity" @click="closeModal"></div>

      <!-- Modal Card -->
      <div
        class="relative bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden flex flex-col max-h-[94vh] z-10 transition-all"
      >
        <!-- Mobile drag indicator -->
        <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mt-2.5 mb-1 sm:hidden"></div>

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400 flex items-center justify-center shadow-inner">
              <SparklesIcon class="w-5 h-5" />
            </div>
            <div>
              <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight">
                {{ step === 'review' ? 'Konfirmasi Transaksi' : 'Smart Receipt Scanner' }}
              </h2>
              <p class="text-xs text-slate-400 dark:text-slate-500">
                {{ step === 'review' ? 'Periksa dan sesuaikan data sebelum disimpan' : 'Pindai struk, nota, atau screenshot bukti transfer' }}
              </p>
            </div>
          </div>

          <button
            @click="closeModal"
            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
          <!-- ERROR ALERT -->
          <div
            v-if="scanError"
            class="flex items-start gap-2.5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-900/40 text-rose-600 dark:text-rose-400 text-xs sm:text-sm"
          >
            <ExclamationTriangleIcon class="w-5 h-5 shrink-0 mt-0.5" />
            <div class="flex-1">
              <p class="font-semibold">Gagal memproses gambar</p>
              <p class="mt-0.5 opacity-90">{{ scanError }}</p>
            </div>
          </div>

          <!-- STEP 1: INPUT MODE (Camera, Upload, Paste) -->
          <div v-if="step === 'input'" class="space-y-4">
            <!-- Mode Tabs -->
            <div class="flex gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
              <button
                type="button"
                @click="activeMode = 'upload'"
                :class="[
                  'flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold rounded-lg transition-all',
                  activeMode === 'upload'
                    ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
                    : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200',
                ]"
              >
                <PhotoIcon class="w-4 h-4" />
                <span>Unggah Foto</span>
              </button>

              <button
                type="button"
                @click="activeMode = 'camera'"
                :class="[
                  'flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold rounded-lg transition-all',
                  activeMode === 'camera'
                    ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
                    : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200',
                ]"
              >
                <CameraIcon class="w-4 h-4" />
                <span>Kamera HP</span>
              </button>

              <button
                type="button"
                @click="activeMode = 'paste'"
                :class="[
                  'flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold rounded-lg transition-all',
                  activeMode === 'paste'
                    ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
                    : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200',
                ]"
              >
                <ClipboardDocumentIcon class="w-4 h-4" />
                <span>Screenshot (Paste)</span>
              </button>
            </div>

            <!-- MODE 1: UPLOAD / DRAG & DROP -->
            <div
              v-if="activeMode === 'upload'"
              @dragover.prevent
              @drop="onDrop"
              class="border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-primary-500 dark:hover:border-primary-500 rounded-2xl p-8 flex flex-col items-center justify-center text-center transition-all bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer relative group"
            >
              <input
                type="file"
                accept="image/*"
                capture="environment"
                @change="onFileSelected"
                class="absolute inset-0 opacity-0 cursor-pointer z-10"
              />
              <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-inner">
                <PhotoIcon class="w-7 h-7" />
              </div>
              <p class="text-sm font-bold text-slate-800 dark:text-slate-200">
                Pilih foto struk / nota dari galeri
              </p>
              <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-xs">
                Mendukung struk kasir, bon makanan, atau screenshot QRIS & M-Banking (JPG, PNG, WEBP)
              </p>
              <span class="mt-4 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all">
                Pilih File Gambar
              </span>
            </div>

            <!-- MODE 2: LIVE CAMERA -->
            <div v-else-if="activeMode === 'camera'" class="space-y-3">
              <div class="relative bg-black rounded-2xl overflow-hidden aspect-[4/3] flex items-center justify-center">
                <!-- Video stream -->
                <video
                  ref="videoRef"
                  playsinline
                  autoplay
                  muted
                  class="w-full h-full object-cover"
                ></video>

                <!-- Framing viewfinder overlay -->
                <div class="absolute inset-6 border-2 border-white/70 rounded-xl pointer-events-none flex flex-col justify-between p-2">
                  <div class="flex justify-between">
                    <span class="w-4 h-4 border-t-2 border-l-2 border-primary-500 -mt-1 -ml-1"></span>
                    <span class="w-4 h-4 border-t-2 border-r-2 border-primary-500 -mt-1 -mr-1"></span>
                  </div>
                  <p class="text-center text-[11px] font-medium text-white/90 drop-shadow-md bg-black/40 py-1 px-2.5 rounded-full mx-auto">
                    Posisikan struk di dalam kotak
                  </p>
                  <div class="flex justify-between">
                    <span class="w-4 h-4 border-b-2 border-l-2 border-primary-500 -mb-1 -ml-1"></span>
                    <span class="w-4 h-4 border-b-2 border-r-2 border-primary-500 -mb-1 -mr-1"></span>
                  </div>
                </div>

                <!-- Flip Camera Button -->
                <button
                  type="button"
                  @click="flipCamera"
                  class="absolute top-3 right-3 p-2 bg-black/50 text-white rounded-full backdrop-blur-sm hover:bg-black/70 transition-colors z-20"
                  title="Ganti Kamera Depan/Belakang"
                >
                  <ArrowPathIcon class="w-4 h-4" />
                </button>
              </div>

              <!-- Shutter Capture Button -->
              <div class="flex items-center justify-center pt-2">
                <button
                  type="button"
                  @click="captureFromCamera"
                  class="w-16 h-16 rounded-full border-4 border-primary-600 bg-white p-1 hover:scale-105 active:scale-95 transition-all flex items-center justify-center shadow-lg"
                >
                  <div class="w-12 h-12 rounded-full bg-primary-600"></div>
                </button>
              </div>
            </div>

            <!-- MODE 3: PASTE SCREENSHOT -->
            <div
              v-else-if="activeMode === 'paste'"
              class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-8 flex flex-col items-center justify-center text-center bg-slate-50/50 dark:bg-slate-800/40"
            >
              <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center mb-3 shadow-inner">
                <ClipboardDocumentIcon class="w-7 h-7" />
              </div>
              <p class="text-sm font-bold text-slate-800 dark:text-slate-200">
                Tempelkan Screenshot Bukti Transfer
              </p>
              <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-xs">
                Ambil screenshot di HP/Laptop, lalu tekan <kbd class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-700 rounded font-mono text-[11px]">Ctrl + V</kbd> di keyboard atau tahan dan tempel gambar di sini.
              </p>
            </div>
          </div>

          <!-- STEP 2: HIGH-TECH REALISTIC SCANNING ANIMATION -->
          <div v-else-if="step === 'scanning'" class="py-6 sm:py-8 flex flex-col items-center justify-center space-y-6">
            <!-- HUD Scanner Box -->
            <div class="relative w-56 sm:w-64 aspect-[3/4] rounded-2xl overflow-hidden border-2 border-primary-500/60 shadow-[0_0_30px_rgba(0,102,255,0.3)] bg-slate-950 flex items-center justify-center">
              <!-- Thumbnail Image -->
              <img
                v-if="previewUrl"
                :src="previewUrl"
                class="w-full h-full object-cover opacity-85"
                alt="Scanning..."
              />

              <!-- Cyberpunk / High-Tech Grid Pattern Overlay -->
              <div
                class="absolute inset-0 pointer-events-none opacity-30"
                style="background-image: linear-gradient(rgba(0,102,255,0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(0,102,255,0.3) 1px, transparent 1px); background-size: 20px 20px;"
              ></div>

              <!-- Laser Scanning Beam (Full Vertical Sweep Animation) -->
              <div class="absolute inset-x-0 h-28 pointer-events-none animate-scanner-sweep flex flex-col justify-end">
                <div class="w-full h-full bg-gradient-to-b from-transparent via-primary-500/20 to-primary-500/40"></div>
                <div class="w-full h-1 bg-cyan-300 shadow-[0_0_12px_#38bdf8,0_0_24px_#0066FF]"></div>
              </div>

              <!-- Viewfinder Corner Markers -->
              <div class="absolute inset-3 pointer-events-none flex flex-col justify-between">
                <div class="flex justify-between">
                  <span class="w-5 h-5 border-t-2 border-l-2 border-cyan-400"></span>
                  <span class="w-5 h-5 border-t-2 border-r-2 border-cyan-400"></span>
                </div>
                <!-- Dynamic Tag Badge in middle of image -->
                <div class="mx-auto bg-slate-900/80 backdrop-blur-md px-3 py-1 rounded-full border border-cyan-500/50 shadow-lg text-[11px] font-mono text-cyan-300 flex items-center gap-1.5 animate-pulse">
                  <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                  <span>AI OCR ACTIVE</span>
                </div>
                <div class="flex justify-between">
                  <span class="w-5 h-5 border-b-2 border-l-2 border-cyan-400"></span>
                  <span class="w-5 h-5 border-b-2 border-r-2 border-cyan-400"></span>
                </div>
              </div>
            </div>

            <!-- Dynamic Scan Phase Text & Progress Bar -->
            <div class="w-full max-w-sm space-y-3 text-center px-4">
              <div class="flex items-center justify-center gap-2">
                <component :is="scanPhases[scanPhaseIndex].icon" class="w-5 h-5 text-primary-500 animate-bounce" />
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                  {{ scanPhases[scanPhaseIndex].title }}
                </h3>
              </div>
              <p class="text-xs text-slate-400 dark:text-slate-500">
                {{ scanPhases[scanPhaseIndex].sub }}
              </p>

              <!-- Progress Bar -->
              <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                <div
                  class="h-full bg-gradient-to-r from-primary-600 via-sky-400 to-blue-600 rounded-full transition-all duration-500 ease-out"
                  :style="{ width: `${scanProgress}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- STEP 3: TRANSACTION DRAFT REVIEW & CONFIRMATION -->
          <div v-else-if="step === 'review' && draft" class="space-y-4">
            <!-- Confidence & Note Badge -->
            <div class="flex flex-wrap items-center justify-between gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
              <div class="flex items-center gap-2">
                <SparklesIcon class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                  Data Berhasil Diekstrak AI
                </span>
              </div>
              <span
                class="text-[11px] font-bold px-2.5 py-0.5 rounded-full"
                :class="draft.confidence === 'high' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40' : 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40'"
              >
                {{ draft.confidence === 'high' ? '✓ Akurasi Tinggi' : '⚠️ Perlu Diperiksa' }}
              </span>
            </div>

            <!-- Review Form -->
            <form @submit.prevent="saveTransaction" class="space-y-3.5">
              <!-- Top Row: Thumbnail + Core Fields -->
              <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <!-- Thumbnail preview -->
                <div v-if="previewUrl" class="sm:col-span-4 flex flex-col items-center">
                  <div class="w-full aspect-[3/4] max-h-48 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm bg-slate-100 dark:bg-slate-900">
                    <img :src="previewUrl" class="w-full h-full object-cover" alt="Receipt preview" />
                  </div>
                  <button
                    type="button"
                    @click="step = 'input'"
                    class="text-[11px] font-medium text-primary-600 dark:text-primary-400 hover:underline mt-2 flex items-center gap-1"
                  >
                    <ArrowPathIcon class="w-3 h-3" /> Foto Ulang
                  </button>
                </div>

                <!-- Form Fields -->
                <div :class="previewUrl ? 'sm:col-span-8' : 'sm:col-span-12'" class="space-y-3">
                  <!-- Merchant / Toko -->
                  <div>
                    <label class="label">Nama Toko / Merchant</label>
                    <input
                      v-model="formMerchant"
                      type="text"
                      class="input"
                      placeholder="Contoh: Indomaret, Warung ABC, Kopi Kenangan..."
                      required
                    />
                  </div>

                  <!-- Amount & Type -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label class="label">Total Nominal</label>
                      <CurrencyInput v-model="formAmount" :required="true" />
                    </div>
                    <div>
                      <label class="label">Jenis</label>
                      <SelectInput v-model="formType" :options="typeOptions" placeholder="Jenis" />
                    </div>
                  </div>

                  <!-- Date & Account -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label class="label">Tanggal</label>
                      <DateInput v-model="formDate" placeholder="Pilih tanggal" />
                    </div>
                    <div>
                      <label class="label">Rekening</label>
                      <SelectInput v-model="formAccountId" :options="accountOptions" placeholder="Pilih rekening" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Category -->
              <div>
                <label class="label">Kategori</label>
                <SelectInput v-model="formCategoryId" :options="categoryOptions" placeholder="Pilih kategori" />
              </div>

              <!-- Live Smart Budget Impact Alert (Interconnected UX) -->
              <div
                v-if="budgetImpact"
                class="p-3 rounded-xl border flex items-start gap-2.5 transition-all text-xs"
                :class="[
                  budgetImpact.status === 'exceeded'
                    ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-200'
                    : budgetImpact.status === 'warning'
                    ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-900/50 text-amber-800 dark:text-amber-200'
                    : 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-200',
                ]"
              >
                <div class="shrink-0 mt-0.5">
                  <ExclamationCircleIcon v-if="budgetImpact.status === 'exceeded'" class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                  <ExclamationTriangleIcon v-else-if="budgetImpact.status === 'warning'" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                  <CheckCircleIcon v-else class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-bold leading-tight">{{ budgetImpact.message }}</p>
                  <p class="text-[11px] opacity-80 mt-1">
                    Anggaran {{ budgetImpact.category_name }}: Rp{{ Number(budgetImpact.budget_amount).toLocaleString('id-ID') }}
                    • Terpakai: Rp{{ Number(budgetImpact.current_spent).toLocaleString('id-ID') }}
                    ➔ Menjadi Rp{{ Number(budgetImpact.projected_spent).toLocaleString('id-ID') }} ({{ budgetImpact.projected_percentage }}%)
                  </p>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label class="label">Keterangan / Rincian Belanja <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input
                  v-model="formDescription"
                  type="text"
                  class="input"
                  placeholder="Catatan belanja..."
                />
              </div>

              <!-- Itemized List (if extracted) -->
              <div v-if="draft.items && draft.items.length" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl space-y-1.5 border border-slate-200/40 dark:border-slate-700">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Rincian Item yang Terbaca:</p>
                <div class="space-y-1 max-h-32 overflow-y-auto pr-1">
                  <div
                    v-for="(item, idx) in draft.items"
                    :key="idx"
                    class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 py-0.5"
                  >
                    <span class="truncate">{{ item.qty > 1 ? item.qty + 'x ' : '' }}{{ item.name }}</span>
                    <span v-if="item.price" class="font-semibold shrink-0 ml-2 tabular-nums">
                      Rp{{ Number(item.price).toLocaleString('id-ID') }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Save error message -->
              <div v-if="saveError" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 text-rose-600 text-xs">
                {{ saveError }}
              </div>

              <!-- Footer Buttons -->
              <div class="flex gap-2.5 pt-2">
                <button
                  type="button"
                  @click="step = 'input'"
                  class="btn-secondary flex-1 py-2.5 text-xs sm:text-sm"
                >
                  Batal / Foto Ulang
                </button>
                <button
                  type="submit"
                  :disabled="saving || !formAmount || !formCategoryId || !formAccountId"
                  class="btn-primary flex-1 py-2.5 text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-md"
                >
                  <CheckIcon class="w-4 h-4 stroke-2" />
                  <span>{{ saving ? 'Menyimpan...' : 'Simpan Transaksi' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
@keyframes scannerSweep {
  0% {
    top: -20%;
  }
  50% {
    top: 75%;
  }
  100% {
    top: -20%;
  }
}

.animate-scanner-sweep {
  animation: scannerSweep 2.2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}
</style>
