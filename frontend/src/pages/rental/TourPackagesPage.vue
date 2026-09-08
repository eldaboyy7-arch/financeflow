<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useTourPackagesStore, type TourPackage } from '@/stores/tourPackages'
import { useVehiclesStore } from '@/stores/vehicles'
import { useUiStore } from '@/stores/ui'
import { useAuthStore } from '@/stores/auth'
import { uploadFleetPhoto } from '@/services/supabaseStorage'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import MoneySpinner from '@/components/MoneySpinner.vue'
import CurrencyInput from '@/components/CurrencyInput.vue'
import SelectInput, { type SelectOption } from '@/components/SelectInput.vue'
import api from '@/api/axios'
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  XMarkIcon,
  CheckIcon,
  MagnifyingGlassIcon,
  CameraIcon,
  ArrowPathIcon,
  PhotoIcon,
  MapPinIcon,
  ClockIcon,
  UsersIcon,
  TruckIcon,
  ShieldCheckIcon,
  SparklesIcon,
  ChatBubbleLeftRightIcon,
  ListBulletIcon,
  InformationCircleIcon,
  BanknotesIcon,
} from '@heroicons/vue/24/outline'

const store = useTourPackagesStore()
const vehiclesStore = useVehiclesStore()
const uiStore = useUiStore()
const authStore = useAuthStore()
const { formatCurrency } = useFormatCurrency()

const searchQuery = ref('')
const selectedStatus = ref<'all' | 'active' | 'inactive'>('all')

const activeCount = computed(() => store.packages.filter(p => p.is_active).length)
const inactiveCount = computed(() => store.packages.filter(p => !p.is_active).length)

const filteredPackages = computed(() => {
  let list = store.packages

  if (selectedStatus.value === 'active') {
    list = list.filter(p => p.is_active)
  } else if (selectedStatus.value === 'inactive') {
    list = list.filter(p => !p.is_active)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter(p =>
      (p.title || '').toLowerCase().includes(q) ||
      (p.vehicle_name || '').toLowerCase().includes(q) ||
      (p.tour_route || '').toLowerCase().includes(q)
    )
  }

  return list
})

const showModal = ref(false)
const editingId = ref<number | null>(null)
const submitting = ref(false)
const modalError = ref('')
const activeTab = ref<'basic' | 'route' | 'itinerary' | 'inclusions' | 'media'>('basic')

const badgeColorOptions: SelectOption[] = [
  { value: 'amber', label: 'Emas / Amber (Paling Populer)' },
  { value: 'indigo', label: 'Indigo / Ungu (Luxury VIP)' },
  { value: 'blue', label: 'Biru (Standar / Fleksibel)' },
  { value: 'emerald', label: 'Hijau / Emerald (Hemat / Promo)' },
]

const vehicleOptions = computed<SelectOption[]>(() => {
  const opts: SelectOption[] = [{ value: '', label: 'Tanpa Armada Terhubung (Fleksibel / Custom)' }]
  vehiclesStore.vehicles.forEach(v => {
    opts.push({
      value: String(v.id),
      label: `${v.name} (${v.brand || ''} - ${v.plate_number || 'Tanpa Plat'})`
    })
  })
  return opts
})

// ── Quick Transaction Modal (Pencatatan Pemasukan 1-Klik) ────
const showQuickTxModal = ref(false)
const quickTxSubmitting = ref(false)
const quickTxError = ref('')
const selectedPackageForTx = ref<TourPackage | null>(null)

const accounts = ref<any[]>([])
const categories = ref<any[]>([])

const accountOptions = computed<SelectOption[]>(() =>
  accounts.value.map((a: any) => ({
    value: a.id,
    label: a.name,
    icon: '💳',
  }))
)

const rentalCategoryOptions = computed<SelectOption[]>(() =>
  categories.value
    .filter((c: any) => c.type === 'income')
    .map((c: any) => ({
      value: c.id,
      label: c.name,
      icon: '💰',
    }))
)

const quickTxForm = ref({
  amount: 0,
  date: new Date().toISOString().slice(0, 10),
  account_id: '' as string | number,
  category_id: '' as string | number,
  vehicle_id: '' as string | number,
  description: '',
  guest_name: '',
})

function openQuickTransaction(pkg: TourPackage) {
  selectedPackageForTx.value = pkg
  quickTxError.value = ''

  // 1. Cari armada yang cocok (dari pkg.vehicle_id atau nama mobil)
  let matchedVehicleId: string | number = ''
  if (pkg.vehicle_id) {
    matchedVehicleId = String(pkg.vehicle_id)
  } else if (vehiclesStore.vehicles.length > 0) {
    const found = vehiclesStore.vehicles.find(v =>
      pkg.vehicle_name && v.name.toLowerCase().includes(pkg.vehicle_name.toLowerCase().slice(0, 6))
    )
    matchedVehicleId = found ? String(found.id) : String(vehiclesStore.vehicles[0].id)
  }

  // 2. Kategori pemasukan otomatis: Paket Tour / Wisata (All-in include supir, non-lepas kunci)
  const incomeCats = categories.value.filter((c: any) => c.type === 'income')
  const defaultCat = incomeCats.find((c: any) =>
    c.name.toLowerCase().includes('tour') || c.name.toLowerCase().includes('wisata')
  ) || incomeCats.find((c: any) => c.name.toLowerCase().includes('supir')) || incomeCats[0]

  quickTxForm.value = {
    amount: Number(pkg.price) || 1000000,
    date: new Date().toISOString().slice(0, 10),
    account_id: accounts.value.length ? accounts.value[0].id : '',
    category_id: defaultCat ? defaultCat.id : '',
    vehicle_id: matchedVehicleId,
    description: `Pemesanan Paket Tour ${pkg.title}`,
    guest_name: '',
  }

  showQuickTxModal.value = true
}

async function submitQuickTransaction() {
  quickTxSubmitting.value = true
  quickTxError.value = ''

  try {
    const finalDesc = quickTxForm.value.guest_name.trim()
      ? `${quickTxForm.value.description} - Rombongan ${quickTxForm.value.guest_name.trim()}`
      : quickTxForm.value.description

    await api.post('/transactions', {
      type: 'income',
      amount: quickTxForm.value.amount,
      date: quickTxForm.value.date,
      description: finalDesc,
      account_id: quickTxForm.value.account_id,
      category_id: quickTxForm.value.category_id,
      vehicle_id: quickTxForm.value.vehicle_id ? Number(quickTxForm.value.vehicle_id) : null,
    })

    showQuickTxModal.value = false
    uiStore.showToast(`Pemasukan paket tour berhasil dicatat & masuk ke laporan!`)
  } catch (e: any) {
    quickTxError.value = e?.response?.data?.message || 'Gagal menyimpan transaksi pemasukan.'
  } finally {
    quickTxSubmitting.value = false
  }
}

const defaultForm = {
  title: '',
  subtitle: '',
  badge: '',
  badge_color: 'amber' as 'blue' | 'indigo' | 'amber' | 'emerald',
  price: 1000000,
  price_label: 'HARGA MULAI',
  duration: 'Full Day Tour (8 - 10 Jam)',
  capacity: '15 Penumpang',
  vehicle_name: '',
  vehicle_id: '' as string,
  description: '',
  tour_route: '',
  cover_photo_path: '',
  cover_photo_url: '',
  gallery_photos: [] as string[],
  facilities: [] as string[],
  itinerary: [] as string[],
  included: [] as string[],
  excluded: [] as string[],
  cta_whatsapp_text: '',
  sort_order: 0,
  is_active: true,
}

const form = ref({ ...defaultForm })
const newFacilityInput = ref('')
const newIncludedInput = ref('')
const newExcludedInput = ref('')
const uploadingCover = ref(false)
const uploadingGallery = ref(false)
const coverFileInput = ref<HTMLInputElement | null>(null)
const galleryFileInput = ref<HTMLInputElement | null>(null)

onMounted(async () => {
  const [_, __, accs, cats] = await Promise.all([
    store.fetchPackages(),
    vehiclesStore.fetchVehicles(),
    api.get('/accounts').catch(() => ({ data: { data: [] } })),
    api.get('/categories', { params: { mode: 'rental' } }).catch(() => ({ data: { data: [] } })),
  ])
  accounts.value = accs.data?.data ?? accs.data ?? []
  categories.value = cats.data?.data ?? cats.data ?? []
})

// Sinkronisasi otomatis template WhatsApp dengan nama paket jika admin belum kustom manual
watch(() => form.value.title, (newTitle) => {
  if (!editingId.value && newTitle) {
    if (!form.value.cta_whatsapp_text || form.value.cta_whatsapp_text.includes('booking') || form.value.cta_whatsapp_text.includes('Paket')) {
      form.value.cta_whatsapp_text = `Halo Admin Bintan Travel, saya tertarik booking Paket Tour ${newTitle.trim()} untuk tanggal...`
    }
  }
})

function openCreate() {
  editingId.value = null
  form.value = {
    ...defaultForm,
    facilities: [
      'Supir Lokal Ramah & Berpengalaman',
      'BBM Selama Tur Penuh',
      'Air Mineral Dingin',
      'Biaya Parkir Destinasi Utama'
    ],
    itinerary: [
      '08:00 - 08:30: Penjemputan di Hotel / Pelabuhan',
      '09:00 - 11:30: Mengunjungi Destinasi Pertama & Sesi Foto',
      '12:00 - 13:30: Istirahat Makan Siang Kuliner Lokal',
      '14:00 - 16:30: Eksplorasi Kawasan Wisata Bahari / Budaya',
      '17:00 - 18:00: Pengantaran Kembali ke Hotel / Pelabuhan'
    ],
    included: [
      'Unit armada bersih & full AC',
      'Supir ramah merangkap pemandu',
      'BBM selama rute tour berlangsung',
      'Air mineral botol'
    ],
    excluded: [
      'Tiket masuk objek wisata berbayar',
      'Makan & minum pribadi',
      'Tip sukarela untuk supir'
    ],
    cta_whatsapp_text: 'Halo Admin Bintan Travel, saya tertarik booking paket tour ini untuk tanggal...'
  }
  activeTab.value = 'basic'
  modalError.value = ''
  showModal.value = true
}

function openEdit(p: TourPackage) {
  editingId.value = p.id
  form.value = {
    title: p.title || '',
    subtitle: p.subtitle || '',
    badge: p.badge || '',
    badge_color: p.badge_color || 'amber',
    price: Number(p.price) || 0,
    price_label: p.price_label || 'HARGA MULAI',
    duration: p.duration || '',
    capacity: p.capacity || '',
    vehicle_name: p.vehicle_name || '',
    vehicle_id: p.vehicle_id ? String(p.vehicle_id) : '',
    description: p.description || '',
    tour_route: p.tour_route || '',
    cover_photo_path: p.cover_photo_path || '',
    cover_photo_url: p.cover_photo_url || '',
    gallery_photos: Array.isArray(p.gallery_photos)
      ? p.gallery_photos.map(item => (typeof item === 'string' ? item : item.path || item.url || ''))
      : [],
    facilities: Array.isArray(p.facilities) ? [...p.facilities] : [],
    itinerary: Array.isArray(p.itinerary) ? [...p.itinerary] : [],
    included: Array.isArray(p.included) ? [...p.included] : [],
    excluded: Array.isArray(p.excluded) ? [...p.excluded] : [],
    cta_whatsapp_text: p.cta_whatsapp_text || '',
    sort_order: Number(p.sort_order) || 0,
    is_active: p.is_active ?? true,
  }
  activeTab.value = 'basic'
  modalError.value = ''
  showModal.value = true
}

function addFacility() {
  const val = newFacilityInput.value.trim()
  if (val && !form.value.facilities.includes(val)) {
    form.value.facilities.push(val)
    newFacilityInput.value = ''
  }
}

function removeFacility(idx: number) {
  form.value.facilities.splice(idx, 1)
}

function addItineraryStep() {
  form.value.itinerary.push('')
}

function removeItineraryStep(idx: number) {
  form.value.itinerary.splice(idx, 1)
}

function addIncluded() {
  const val = newIncludedInput.value.trim()
  if (val && !form.value.included.includes(val)) {
    form.value.included.push(val)
    newIncludedInput.value = ''
  }
}

function removeIncluded(idx: number) {
  form.value.included.splice(idx, 1)
}

function addExcluded() {
  const val = newExcludedInput.value.trim()
  if (val && !form.value.excluded.includes(val)) {
    form.value.excluded.push(val)
    newExcludedInput.value = ''
  }
}

function removeExcluded(idx: number) {
  form.value.excluded.splice(idx, 1)
}

function generateRouteFromItinerary() {
  const destinations = form.value.itinerary
    .map(step => {
      const parts = step.split(':')
      return parts.length > 1 ? parts[1].trim() : step.trim()
    })
    .filter(Boolean)
    .slice(0, 5)

  if (destinations.length > 0) {
    form.value.tour_route = destinations.join(' - ')
    uiStore.showToast('Rute ringkas berhasil dibuat dari itinerary!')
  }
}

async function handleCoverChange(e: Event) {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  uploadingCover.value = true
  modalError.value = ''
  try {
    const userId = authStore.user?.id || 1
    const result = await uploadFleetPhoto(file, userId, 'tour_packages')
    form.value.cover_photo_path = result.path
    form.value.cover_photo_url = result.url
    uiStore.showToast('Foto cover berhasil diunggah!')
  } catch (err: any) {
    modalError.value = err.message || 'Gagal mengunggah cover foto.'
  } finally {
    uploadingCover.value = false
    if (coverFileInput.value) coverFileInput.value.value = ''
  }
}

async function handleGalleryChange(e: Event) {
  const target = e.target as HTMLInputElement
  const files = target.files
  if (!files || files.length === 0) return

  uploadingGallery.value = true
  modalError.value = ''
  try {
    const userId = authStore.user?.id || 1
    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      const result = await uploadFleetPhoto(file, userId, 'tour_packages')
      form.value.gallery_photos.push(result.path)
    }
    uiStore.showToast('Foto galeri berhasil ditambahkan!')
  } catch (err: any) {
    modalError.value = err.message || 'Gagal mengunggah foto galeri.'
  } finally {
    uploadingGallery.value = false
    if (galleryFileInput.value) galleryFileInput.value.value = ''
  }
}

function removeGalleryPhoto(idx: number) {
  form.value.gallery_photos.splice(idx, 1)
}

async function handleToggleStatus(pkg: TourPackage) {
  try {
    await store.toggleStatus(pkg.id)
    uiStore.showToast(`Paket "${pkg.title}" sekarang ${pkg.is_active ? 'Nonaktif' : 'Aktif'}!`)
  } catch (err: any) {
    uiStore.showToast(err.message || 'Gagal mengubah status paket.', 'error')
  }
}

async function handleDelete(pkg: TourPackage) {
  if (!confirm(`Yakin ingin menghapus paket "${pkg.title}"?`)) return
  try {
    await store.deletePackage(pkg.id)
    uiStore.showToast('Paket tour berhasil dihapus.')
  } catch (err: any) {
    uiStore.showToast(err.message || 'Gagal menghapus paket tour.', 'error')
  }
}

async function submitPackage() {
  if (!form.value.title.trim()) {
    modalError.value = 'Judul paket tour wajib diisi.'
    return
  }

  submitting.value = true
  modalError.value = ''

  const payload: any = {
    ...form.value,
    vehicle_id: form.value.vehicle_id ? Number(form.value.vehicle_id) : null,
    price: Number(form.value.price) || 0,
    sort_order: Number(form.value.sort_order) || 0,
  }

  try {
    if (editingId.value) {
      await store.updatePackage(editingId.value, payload)
      uiStore.showToast('Paket tour berhasil diperbarui!')
    } else {
      await store.createPackage(payload)
      uiStore.showToast('Paket tour baru berhasil ditambahkan!')
    }
    showModal.value = false
  } catch (err: any) {
    modalError.value = err.response?.data?.message || err.message || 'Gagal menyimpan paket tour.'
  } finally {
    submitting.value = false
  }
}

function getBadgeStyle(color: string) {
  switch (color) {
    case 'amber':
      return 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30'
    case 'indigo':
      return 'bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-indigo-500/30'
    case 'emerald':
      return 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30'
    case 'blue':
    default:
      return 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/30'
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
          <MapPinIcon class="w-7 h-7 text-primary-600 dark:text-primary-400" />
          Kelola Paket Tour Bintan
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
          Atur harga all-in, rute perjalanan, itinerary detail, dan ketersediaan paket tour untuk website rental.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button
          @click="store.fetchPackages()"
          class="p-2.5 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition border border-slate-200 dark:border-slate-700"
          title="Segarkan Data"
        >
          <ArrowPathIcon class="w-5 h-5" :class="{ 'animate-spin': store.loading }" />
        </button>
        <button
          @click="openCreate"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-sm transition"
        >
          <PlusIcon class="w-5 h-5" />
          <span>Tambah Paket Tour</span>
        </button>
      </div>
    </div>

    <!-- SOP Privasi Armada & Blur Plat Nomor Banner -->
    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-3 text-amber-900 dark:text-amber-200">
      <ShieldCheckIcon class="w-6 h-6 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
      <div class="text-xs sm:text-sm leading-relaxed">
        <span class="font-semibold text-amber-800 dark:text-amber-300">SOP Privasi Armada & Foto Publik:</span>
        Pastikan setiap foto unit armada atau galeri yang diunggah telah disamarkan/diblur nomor plat polisinya, atau diambil dari sudut interior/sudut miring yang tidak mengekspos plat nomor secara terbuka demi privasi dan keamanan armada.
      </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center">
          <MapPinIcon class="w-6 h-6" />
        </div>
        <div>
          <div class="text-xs text-slate-500 dark:text-slate-400">Total Paket Tour</div>
          <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ store.packages.length }}</div>
        </div>
      </div>

      <div class="p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
          <CheckIcon class="w-6 h-6" />
        </div>
        <div>
          <div class="text-xs text-slate-500 dark:text-slate-400">Paket Aktif di Website</div>
          <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ activeCount }}</div>
        </div>
      </div>

      <div class="p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center">
          <ClockIcon class="w-6 h-6" />
        </div>
        <div>
          <div class="text-xs text-slate-500 dark:text-slate-400">Paket Nonaktif / Draft</div>
          <div class="text-2xl font-bold text-slate-600 dark:text-slate-300">{{ inactiveCount }}</div>
        </div>
      </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
      <div class="relative w-full sm:w-80">
        <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari judul, armada, atau rute..."
          class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
        />
      </div>

      <div class="flex items-center gap-2 self-start sm:self-auto">
        <button
          @click="selectedStatus = 'all'"
          :class="selectedStatus === 'all' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
          class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
        >
          Semua ({{ store.packages.length }})
        </button>
        <button
          @click="selectedStatus = 'active'"
          :class="selectedStatus === 'active' ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
          class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
        >
          Aktif ({{ activeCount }})
        </button>
        <button
          @click="selectedStatus = 'inactive'"
          :class="selectedStatus === 'inactive' ? 'bg-slate-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
          class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
        >
          Nonaktif ({{ inactiveCount }})
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="store.loading && store.packages.length === 0" class="py-16 flex justify-center">
      <MoneySpinner text="Memuat data paket tour..." />
    </div>

    <!-- Empty State -->
    <div
      v-else-if="filteredPackages.length === 0"
      class="p-12 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700"
    >
      <MapPinIcon class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" />
      <h3 class="text-base font-semibold text-slate-900 dark:text-white">Tidak ada paket tour</h3>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
        Belum ada paket tour yang sesuai dengan filter pencarian Anda. Klik tombol "Tambah Paket Tour" untuk membuat paket baru.
      </p>
      <button
        @click="openCreate"
        class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-xs font-medium rounded-xl hover:bg-primary-700 transition"
      >
        <PlusIcon class="w-4 h-4" />
        <span>Buat Paket Tour</span>
      </button>
    </div>

    <!-- Cards Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div
        v-for="pkg in filteredPackages"
        :key="pkg.id"
        class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col justify-between transition hover:border-primary-500/50"
      >
        <div>
          <!-- Card Header / Image Preview -->
          <div class="relative h-44 bg-slate-100 dark:bg-slate-900 overflow-hidden group">
            <img
              v-if="pkg.cover_photo_url"
              :src="pkg.cover_photo_url"
              :alt="pkg.title"
              class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
              loading="lazy"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
              <PhotoIcon class="w-12 h-12 stroke-1" />
            </div>

            <!-- Badges -->
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
              <span
                v-if="pkg.badge"
                :class="getBadgeStyle(pkg.badge_color)"
                class="px-2.5 py-1 rounded-full text-xs font-semibold border backdrop-blur-md"
              >
                {{ pkg.badge }}
              </span>
            </div>

            <!-- Active Status Switch -->
            <div class="absolute top-3 right-3">
              <button
                @click.stop="handleToggleStatus(pkg)"
                :class="pkg.is_active ? 'bg-emerald-600 text-white' : 'bg-slate-900/80 text-slate-300'"
                class="px-2.5 py-1 rounded-full text-xs font-medium border border-white/20 backdrop-blur-md shadow-sm transition flex items-center gap-1.5"
                :title="pkg.is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="pkg.is_active ? 'bg-white animate-pulse' : 'bg-slate-400'"></span>
                <span>{{ pkg.is_active ? 'Aktif di Web' : 'Nonaktif' }}</span>
              </button>
            </div>

            <!-- Price Tag Overlay -->
            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between bg-slate-950/70 backdrop-blur-md px-3 py-1.5 rounded-xl text-white">
              <span class="text-[11px] font-medium text-slate-300 uppercase tracking-wider">{{ pkg.price_label || 'HARGA MULAI' }}</span>
              <span class="text-sm font-bold text-primary-300">
                {{ pkg.price > 0 ? (pkg.formatted_price || formatCurrency(pkg.price)) : 'Gratis / Nego' }}
              </span>
            </div>
          </div>

          <!-- Card Body -->
          <div class="p-4 space-y-3">
            <div>
              <h3 class="font-bold text-base text-slate-900 dark:text-white leading-snug">
                {{ pkg.title }}
              </h3>
              <p v-if="pkg.subtitle" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                {{ pkg.subtitle }}
              </p>
            </div>

            <!-- Specs line -->
            <div class="flex items-center gap-3 text-xs text-slate-600 dark:text-slate-300 pt-1">
              <span v-if="pkg.duration" class="flex items-center gap-1">
                <ClockIcon class="w-3.5 h-3.5 text-slate-400" />
                <span>{{ pkg.duration }}</span>
              </span>
              <span v-if="pkg.capacity" class="flex items-center gap-1">
                <UsersIcon class="w-3.5 h-3.5 text-slate-400" />
                <span>{{ pkg.capacity }}</span>
              </span>
            </div>

            <!-- Route Summary -->
            <div v-if="pkg.tour_route" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700/60">
              <div class="text-[10px] uppercase font-semibold text-slate-400 mb-1 flex items-center gap-1">
                <MapPinIcon class="w-3 h-3 text-primary-500" />
                <span>Ringkasan Rute</span>
              </div>
              <p class="text-xs text-slate-700 dark:text-slate-300 line-clamp-2 leading-relaxed">
                {{ pkg.tour_route }}
              </p>
            </div>

            <!-- Highlights count -->
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 pt-1">
              <span>{{ (pkg.itinerary || []).length }} Titik Jadwal</span>
              <span>{{ (pkg.facilities || []).length }} Fasilitas All-in</span>
            </div>
          </div>
        </div>

        <!-- Card Footer Actions: Quick Transaction + Edit + Delete -->
        <div class="p-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 mt-1 flex items-center justify-between gap-2">
          <button
            type="button"
            @click="openQuickTransaction(pkg)"
            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs font-bold border border-emerald-200 dark:border-emerald-800/50 transition-colors shadow-xs"
            title="Catat Pemasukan Paket Ini ke Laporan Keuangan"
          >
            <BanknotesIcon class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
            <span>Catat Pemasukan</span>
          </button>
          <div class="flex items-center gap-1 shrink-0">
            <button
              type="button"
              @click="openEdit(pkg)"
              class="p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition"
              title="Edit Paket Tour"
            >
              <PencilSquareIcon class="w-4 h-4" />
            </button>
            <button
              type="button"
              @click="handleDelete(pkg)"
              class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition"
              title="Hapus Paket"
            >
              <TrashIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah / Edit -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm overflow-y-auto"
    >
      <div class="w-full max-w-3xl bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden my-8">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
              {{ editingId ? 'Edit Paket Tour' : 'Tambah Paket Tour Baru' }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Data yang disimpan akan langsung terhubung ke website rental publik dalam ≤90 detik.
            </p>
          </div>
          <button
            @click="showModal = false"
            class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Tabs Bar -->
        <div class="flex border-b border-slate-200 dark:border-slate-700 px-6 bg-slate-50 dark:bg-slate-800/60 overflow-x-auto">
          <button
            @click="activeTab = 'basic'"
            :class="activeTab === 'basic' ? 'border-primary-600 text-primary-600 dark:text-primary-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'"
            class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition"
          >
            Info Dasar & Harga
          </button>
          <button
            @click="activeTab = 'route'"
            :class="activeTab === 'route' ? 'border-primary-600 text-primary-600 dark:text-primary-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'"
            class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition"
          >
            Rute Kartu & Fasilitas
          </button>
          <button
            @click="activeTab = 'itinerary'"
            :class="activeTab === 'itinerary' ? 'border-primary-600 text-primary-600 dark:text-primary-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'"
            class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition"
          >
            Itinerary Titik Demi Titik ({{ form.itinerary.length }})
          </button>
          <button
            @click="activeTab = 'inclusions'"
            :class="activeTab === 'inclusions' ? 'border-primary-600 text-primary-600 dark:text-primary-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'"
            class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition"
          >
            Termasuk & Tidak
          </button>
          <button
            @click="activeTab = 'media'"
            :class="activeTab === 'media' ? 'border-primary-600 text-primary-600 dark:text-primary-400 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'"
            class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition"
          >
            Foto & WhatsApp
          </button>
        </div>

        <!-- Modal Error Alert -->
        <div v-if="modalError" class="mx-6 mt-4 p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs">
          {{ modalError }}
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 max-h-[65vh] overflow-y-auto space-y-4">
          <!-- TAB 1: BASIC INFO -->
          <div v-show="activeTab === 'basic'" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Judul Paket Tour <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="form.title"
                  type="text"
                  placeholder="Contoh: Tour Bintan — HiAce Commuter"
                  class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Sub-Judul / Keterangan Singkat
                </label>
                <input
                  v-model="form.subtitle"
                  type="text"
                  placeholder="Contoh: Include Supir & BBM, Kapasitas 15 Kursi"
                  class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Badge Promosi (Opsional)
                </label>
                <input
                  v-model="form.badge"
                  type="text"
                  placeholder="Contoh: Paling Populer / Luxury VIP"
                  class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Warna Badge <span class="text-rose-500">*</span>
                </label>
                <SelectInput
                  v-model="form.badge_color"
                  :options="badgeColorOptions"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Tarif Paket All-In (Rp) <span class="text-rose-500">*</span>
                </label>
                <CurrencyInput
                  v-model="form.price"
                  placeholder="1.000.000"
                />
                <span class="text-[10px] text-slate-400 mt-0.5 block">Isi 0 jika tarif berupa konsultasi / penawaran fleksibel.</span>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Label Harga
                </label>
                <input
                  v-model="form.price_label"
                  type="text"
                  placeholder="HARGA MULAI / KONSULTASI GRATIS"
                  class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Estimasi Durasi
                </label>
                <input
                  v-model="form.duration"
                  type="text"
                  placeholder="Contoh: Full Day Tour (8 - 10 Jam)"
                  class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Kapasitas Penumpang
                </label>
                <input
                  v-model="form.capacity"
                  type="text"
                  placeholder="Contoh: 15 Penumpang / 11 - 14 Kursi"
                  class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Nama Armada yang Digunakan
                </label>
                <input
                  v-model="form.vehicle_name"
                  type="text"
                  placeholder="Contoh: Toyota HiAce Commuter (15 Kursi)"
                  class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Tautkan dengan Armada di Database (Opsional)
                </label>
                <SelectInput
                  v-model="form.vehicle_id"
                  :options="vehicleOptions"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Deskripsi Lengkap Paket
                </label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  placeholder="Jelaskan kenyamanan armada, kelebihan tur, dan rekomendasi peserta..."
                  class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                ></textarea>
              </div>

              <div class="flex items-center gap-6 sm:col-span-2 pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="w-4 h-4 rounded text-primary-600 focus:ring-primary-500 border-slate-300 dark:border-slate-600"
                  />
                  <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Tampilkan Paket ini di Website Rental (Aktif)</span>
                </label>

                <div class="flex items-center gap-2">
                  <label class="text-xs text-slate-500">Urutan Tampil:</label>
                  <input
                    v-model.number="form.sort_order"
                    type="number"
                    min="0"
                    class="w-16 px-2 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-center text-slate-900 dark:text-white"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 2: ROUTE & FACILITIES -->
          <div v-show="activeTab === 'route'" class="space-y-5">
            <!-- tour_route -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 space-y-2">
              <div class="flex items-center justify-between">
                <label class="block text-xs font-semibold text-slate-900 dark:text-white">
                  Rute Ringkas untuk Kartu Website (tour_route)
                </label>
                <button
                  type="button"
                  @click="generateRouteFromItinerary"
                  class="text-[11px] font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1"
                >
                  <SparklesIcon class="w-3.5 h-3.5" />
                  <span>Generate dari Itinerary</span>
                </button>
              </div>
              <textarea
                v-model="form.tour_route"
                rows="2"
                placeholder="Contoh: Sleeping Buddha - Danau Biru & Gurun Pasir - Lagoi Bay - Patung Gonggong - Patung 1000 Wajah"
                class="w-full px-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
              ></textarea>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Teks ini ditampilkan sebagai deretan chip ringkas di kartu paket tour pada halaman utama & etalase. Pisahkan destinasi dengan tanda strip (-) atau panah (→).
              </p>
            </div>

            <!-- facilities -->
            <div class="space-y-2">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                Daftar Fasilitas Utama / Keunggulan
              </label>
              <div class="flex gap-2">
                <input
                  v-model="newFacilityInput"
                  @keydown.enter.prevent="addFacility"
                  type="text"
                  placeholder="Ketik fasilitas (misal: Unit AC Dingin Merata, Supir Berpengalaman) lalu Enter"
                  class="flex-1 px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
                <button
                  type="button"
                  @click="addFacility"
                  class="px-4 py-2 bg-primary-600 text-white rounded-xl text-xs font-medium hover:bg-primary-700"
                >
                  Tambah
                </button>
              </div>

              <!-- Tags container -->
              <div class="flex flex-wrap gap-2 pt-2">
                <span
                  v-for="(fac, idx) in form.facilities"
                  :key="idx"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-600"
                >
                  <span>{{ fac }}</span>
                  <button
                    type="button"
                    @click="removeFacility(idx)"
                    class="text-slate-400 hover:text-rose-500"
                  >
                    <XMarkIcon class="w-3.5 h-3.5" />
                  </button>
                </span>
                <span v-if="form.facilities.length === 0" class="text-xs text-slate-400 italic">
                  Belum ada fasilitas ditambahkan.
                </span>
              </div>
            </div>
          </div>

          <!-- TAB 3: ITINERARY DETAILS -->
          <div v-show="activeTab === 'itinerary'" class="space-y-4">
            <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-start gap-2.5 text-blue-900 dark:text-blue-200 text-xs">
              <InformationCircleIcon class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
              <div>
                Itinerary ini ditampilkan pada <strong>accordion titik rute perjalanan bertahap (01 s/d 08)</strong> di website rental. Anda bisa menambahkan waktu dan nama kegiatan pada tiap baris.
              </div>
            </div>

            <div class="space-y-2.5">
              <div
                v-for="(step, idx) in form.itinerary"
                :key="idx"
                class="flex items-center gap-2"
              >
                <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold flex items-center justify-center flex-shrink-0">
                  {{ String(idx + 1).padStart(2, '0') }}
                </span>
                <input
                  v-model="form.itinerary[idx]"
                  type="text"
                  placeholder="08:00 - 09:30: Penjemputan di Hotel / Wisata Vihara Sleeping Buddha"
                  class="flex-1 px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
                <button
                  type="button"
                  @click="removeItineraryStep(idx)"
                  class="p-2 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"
                  title="Hapus baris ini"
                >
                  <TrashIcon class="w-4 h-4" />
                </button>
              </div>
            </div>

            <button
              type="button"
              @click="addItineraryStep"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/40 hover:bg-primary-100 dark:hover:bg-primary-900/40 rounded-xl transition"
            >
              <PlusIcon class="w-4 h-4" />
              <span>Tambah Titik Jadwal Baru</span>
            </button>
          </div>

          <!-- TAB 4: INCLUSIONS & EXCLUSIONS -->
          <div v-show="activeTab === 'inclusions'" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Included -->
            <div class="space-y-3">
              <div class="flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                <CheckIcon class="w-4 h-4" />
                <span>Hal yang Termasuk (Included)</span>
              </div>
              <div class="flex gap-2">
                <input
                  v-model="newIncludedInput"
                  @keydown.enter.prevent="addIncluded"
                  type="text"
                  placeholder="Misal: BBM, Supir, Air mineral"
                  class="flex-1 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white"
                />
                <button
                  type="button"
                  @click="addIncluded"
                  class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-medium hover:bg-emerald-700"
                >
                  +
                </button>
              </div>
              <ul class="space-y-1.5 max-h-48 overflow-y-auto">
                <li
                  v-for="(inc, idx) in form.included"
                  :key="idx"
                  class="flex items-center justify-between text-xs p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-200 border border-emerald-500/20"
                >
                  <span>{{ inc }}</span>
                  <button type="button" @click="removeIncluded(idx)" class="text-slate-400 hover:text-rose-500">
                    <XMarkIcon class="w-3.5 h-3.5" />
                  </button>
                </li>
              </ul>
            </div>

            <!-- Excluded -->
            <div class="space-y-3">
              <div class="flex items-center gap-2 text-xs font-bold text-rose-700 dark:text-rose-400">
                <XMarkIcon class="w-4 h-4" />
                <span>Tidak Termasuk (Excluded)</span>
              </div>
              <div class="flex gap-2">
                <input
                  v-model="newExcludedInput"
                  @keydown.enter.prevent="addExcluded"
                  type="text"
                  placeholder="Misal: Tiket wahana, Makan siang"
                  class="flex-1 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white"
                />
                <button
                  type="button"
                  @click="addExcluded"
                  class="px-3 py-1.5 bg-rose-600 text-white rounded-xl text-xs font-medium hover:bg-rose-700"
                >
                  +
                </button>
              </div>
              <ul class="space-y-1.5 max-h-48 overflow-y-auto">
                <li
                  v-for="(exc, idx) in form.excluded"
                  :key="idx"
                  class="flex items-center justify-between text-xs p-2 rounded-lg bg-rose-50 dark:bg-rose-950/30 text-rose-900 dark:text-rose-200 border border-rose-500/20"
                >
                  <span>{{ exc }}</span>
                  <button type="button" @click="removeExcluded(idx)" class="text-slate-400 hover:text-rose-500">
                    <XMarkIcon class="w-3.5 h-3.5" />
                  </button>
                </li>
              </ul>
            </div>
          </div>

          <!-- TAB 5: MEDIA & WHATSAPP -->
          <div v-show="activeTab === 'media'" class="space-y-5">
            <!-- SOP Plate blur alert -->
            <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-2.5 text-amber-900 dark:text-amber-200 text-xs">
              <ShieldCheckIcon class="w-5 h-5 text-amber-600 flex-shrink-0" />
              <div>
                <strong>Peringatan Privasi Plat Nomor:</strong> Harap pastikan foto unit tidak memperlihatkan nomor polisi kendaraan secara terbuka. Blur atau crop nomor plat terlebih dahulu sebelum upload.
              </div>
            </div>

            <!-- Cover Photo -->
            <div class="space-y-2">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                Foto Utama (Cover Photo)
              </label>
              <div class="flex items-center gap-4">
                <div class="w-32 h-20 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 overflow-hidden flex items-center justify-center flex-shrink-0">
                  <img
                    v-if="form.cover_photo_url || form.cover_photo_path"
                    :src="form.cover_photo_url || form.cover_photo_path"
                    alt="Cover Preview"
                    class="w-full h-full object-cover"
                  />
                  <CameraIcon v-else class="w-6 h-6 text-slate-400" />
                </div>
                <div class="space-y-1.5 flex-1">
                  <input
                    ref="coverFileInput"
                    type="file"
                    accept="image/jpeg,image/png,image/webp,image/avif"
                    class="hidden"
                    @change="handleCoverChange"
                  />
                  <div class="flex gap-2">
                    <button
                      type="button"
                      :disabled="uploadingCover"
                      @click="coverFileInput?.click()"
                      class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-medium transition flex items-center gap-1.5"
                    >
                      <CameraIcon class="w-4 h-4" />
                      <span>{{ uploadingCover ? 'Mengunggah...' : 'Pilih Foto Cover' }}</span>
                    </button>
                    <button
                      v-if="form.cover_photo_path || form.cover_photo_url"
                      type="button"
                      @click="form.cover_photo_path = ''; form.cover_photo_url = ''"
                      class="px-3 py-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl text-xs font-medium"
                    >
                      Hapus Cover
                    </button>
                  </div>
                  <p class="text-[10px] text-slate-400">
                    Bisa berupa URL lokal (/images/...) atau file baru yang diunggah langsung ke Supabase Storage.
                  </p>
                </div>
              </div>
            </div>

            <!-- Gallery Photos -->
            <div class="space-y-2 pt-2">
              <div class="flex items-center justify-between">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                  Galeri Foto Tambahan (Interior & Multi-Angle)
                </label>
                <input
                  ref="galleryFileInput"
                  type="file"
                  multiple
                  accept="image/jpeg,image/png,image/webp,image/avif"
                  class="hidden"
                  @change="handleGalleryChange"
                />
                <button
                  type="button"
                  :disabled="uploadingGallery"
                  @click="galleryFileInput?.click()"
                  class="text-xs text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1"
                >
                  <PlusIcon class="w-3.5 h-3.5" />
                  <span>{{ uploadingGallery ? 'Mengunggah...' : 'Upload Foto Galeri' }}</span>
                </button>
              </div>

              <!-- Gallery thumbnails -->
              <div class="flex flex-wrap gap-3 pt-1">
                <div
                  v-for="(photo, idx) in form.gallery_photos"
                  :key="idx"
                  class="relative w-24 h-16 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 overflow-hidden group"
                >
                  <img
                    :src="photo"
                    alt="Gallery"
                    class="w-full h-full object-cover"
                  />
                  <button
                    type="button"
                    @click="removeGalleryPhoto(idx)"
                    class="absolute top-1 right-1 p-1 rounded bg-rose-600 text-white opacity-0 group-hover:opacity-100 transition"
                  >
                    <XMarkIcon class="w-3 h-3" />
                  </button>
                </div>
                <div v-if="form.gallery_photos.length === 0" class="text-xs text-slate-400 italic py-2">
                  Belum ada foto galeri tambahan.
                </div>
              </div>
            </div>

            <!-- WhatsApp Template -->
            <div class="space-y-2 pt-2">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <ChatBubbleLeftRightIcon class="w-4 h-4 text-emerald-600" />
                <span>Template Teks WhatsApp (CTA Button)</span>
              </label>
              <textarea
                v-model="form.cta_whatsapp_text"
                rows="2"
                placeholder="Halo Admin Bintan Travel, saya ingin reservasi Paket Tour HiAce untuk tanggal..."
                class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
              ></textarea>
              <p class="text-[10px] text-slate-400">
                Teks ini akan otomatis terisi di chat WhatsApp pengunjung saat mereka mengklik tombol "Booking via WhatsApp" di website.
              </p>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 flex items-center justify-end gap-3">
          <button
            type="button"
            @click="showModal = false"
            class="px-4 py-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl text-sm font-medium transition"
          >
            Batal
          </button>
          <button
            type="button"
            :disabled="submitting"
            @click="submitPackage"
            class="px-5 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-xl text-sm font-medium shadow-sm transition flex items-center gap-2"
          >
            <ArrowPathIcon v-if="submitting" class="w-4 h-4 animate-spin" />
            <span>{{ submitting ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Buat Paket Tour') }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Catat Pemasukan Paket Tour (Quick Log 1-Klik) -->
    <Teleport to="body">
      <div
        v-if="showQuickTxModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm overflow-y-auto"
      >
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 w-full max-w-lg shadow-2xl border border-slate-200 dark:border-slate-700">
          <div class="flex items-start justify-between mb-4">
            <div>
              <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 mb-1.5">
                <BanknotesIcon class="w-3.5 h-3.5" />
                <span>Pencatatan Cepat Transaksi</span>
              </div>
              <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                Catat Pemasukan Paket Tour
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Otomatis menambah saldo kas/bank dan masuk ke Laporan Rental & Cashflow.
              </p>
            </div>
            <button
              @click="showQuickTxModal = false"
              class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
            >
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <!-- Banner Paket Terpilih: Tegaskan Layanan All-in Non Lepas Kunci -->
          <div
            v-if="selectedPackageForTx"
            class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 mb-4 flex items-center justify-between"
          >
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="text-[10px] uppercase font-bold text-slate-400">Paket Wisata All-In</span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300">
                  Wajib Supir + BBM (Non Lepas Kunci)
                </span>
              </div>
              <span class="text-sm font-bold text-slate-900 dark:text-white">{{ selectedPackageForTx.title }}</span>
            </div>
            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-2.5 py-1 rounded-lg">
              {{ selectedPackageForTx.duration }}
            </span>
          </div>

          <form @submit.prevent="submitQuickTransaction" class="space-y-3.5">
            <!-- Armada -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Alokasi Unit Armada <span class="text-rose-500">*</span>
              </label>
              <SelectInput
                v-model="quickTxForm.vehicle_id"
                :options="vehicleOptions"
                placeholder="— Pilih Armada —"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <!-- Nominal -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Nominal Diterima (Rp) <span class="text-rose-500">*</span>
                </label>
                <CurrencyInput
                  v-model="quickTxForm.amount"
                  placeholder="1.000.000"
                />
                <span class="text-[10px] text-slate-400 block mt-0.5">Bisa disesuaikan jika DP / negosiasi.</span>
              </div>

              <!-- Tanggal -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Tanggal Pembayaran <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="quickTxForm.date"
                  type="date"
                  class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                  required
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <!-- Rekening -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Rekening Penampung (Kas/Bank) <span class="text-rose-500">*</span>
                </label>
                <SelectInput
                  v-model="quickTxForm.account_id"
                  :options="accountOptions"
                  placeholder="— Pilih Rekening —"
                />
              </div>

              <!-- Nama Tamu / Catatan -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Nama Tamu / Rombongan (Opsional)
                </label>
                <input
                  v-model="quickTxForm.guest_name"
                  type="text"
                  placeholder="cth: Bpk. Joko (15 Orang)"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 placeholder-slate-400"
                />
              </div>
            </div>

            <div
              v-if="quickTxError"
              class="text-xs text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 rounded-xl px-3.5 py-2.5 border border-rose-200/60 dark:border-rose-800/40"
            >
              {{ quickTxError }}
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex gap-2.5 pt-2">
              <button
                type="button"
                @click="showQuickTxModal = false"
                class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="quickTxSubmitting"
                class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-sm font-bold transition-all shadow-sm flex items-center justify-center gap-2"
              >
                <ArrowPathIcon v-if="quickTxSubmitting" class="w-4 h-4 animate-spin" />
                <span>{{ quickTxSubmitting ? 'Menyimpan...' : 'Simpan & Masukkan ke Laporan' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>
