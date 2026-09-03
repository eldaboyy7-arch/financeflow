<script setup lang="ts">
import { ref, nextTick } from 'vue'
import { advisorApi } from '@/api/advisor'
import type { ChatMessage } from '@/types/advisor'
import {
  SparklesIcon,
  XMarkIcon,
  PaperAirplaneIcon,
  ArrowPathIcon,
  ClipboardDocumentIcon,
  CheckIcon,
} from '@heroicons/vue/24/outline'

const isOpen = ref(false)
const inputMessage = ref('')
const loading = ref(false)
const copiedId = ref<string | null>(null)
const messagesContainer = ref<HTMLElement | null>(null)

const messages = ref<ChatMessage[]>([
  {
    id: '1',
    role: 'assistant',
    text: 'Halo! Saya **FinanceFlow AI**, konsultan keuangan pribadimu.\n\nKamu bisa bertanya apa saja tentang strategi berhemat, evaluasi budget bulanan, atau simulasi target tabunganmu. Ada yang ingin kamu diskusikan hari ini?',
    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
  },
])

const quickQuestions = [
  'Bagaimana evaluasi keuanganku bulan ini?',
  'Berapa yang aman kutabung bulan ini?',
  'Kategori apa yang paling boros?',
  'Tips menghemat pos pengeluaran terbesarku?',
]

function formatText(text: string): string {
  // Simple, safe markdown bold formatter
  const escaped = text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
  return escaped.replace(/\*\*(.*?)\*\*/g, '<strong class="font-extrabold text-slate-900 dark:text-white">$1</strong>')
}

async function scrollToBottom() {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

function toggleDrawer() {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    scrollToBottom()
  }
}

async function copyMessage(msg: ChatMessage) {
  await navigator.clipboard.writeText(msg.text)
  copiedId.value = msg.id
  setTimeout(() => {
    copiedId.value = null
  }, 2000)
}

async function sendMessage(textToSend?: string) {
  const text = (textToSend || inputMessage.value).trim()
  if (!text || loading.value) return

  inputMessage.value = ''

  // 1. Add user message
  const userMsg: ChatMessage = {
    id: String(Date.now()),
    role: 'user',
    text,
    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
  }
  messages.value.push(userMsg)
  scrollToBottom()

  // 2. Call Gemini Advisor API
  loading.value = true
  try {
    const history = messages.value.slice(-6).map((m) => ({
      role: m.role,
      text: m.text,
    }))

    const { data } = await advisorApi.ask(text, history)

    const botMsg: ChatMessage = {
      id: String(Date.now() + 1),
      role: 'assistant',
      text: data.reply,
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    }
    messages.value.push(botMsg)
  } catch (e: any) {
    messages.value.push({
      id: String(Date.now() + 1),
      role: 'assistant',
      text: 'Maaf, terjadi kendala saat menganalisis data keuanganmu. Silakan coba ajukan pertanyaan kembali.',
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    })
  } finally {
    loading.value = false
    scrollToBottom()
  }
}

defineExpose({
  toggleDrawer,
  openDrawer: () => { isOpen.value = true; scrollToBottom() },
  closeDrawer: () => { isOpen.value = false },
  isOpen,
})
</script>

<template>
  <div>
    <!-- Chat Drawer Modal -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform translate-y-8 opacity-0 scale-95"
      enter-to-class="transform translate-y-0 opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform translate-y-0 opacity-100 scale-100"
      leave-to-class="transform translate-y-8 opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="fixed bottom-20 md:bottom-24 right-3 md:right-8 z-50 w-[94vw] sm:w-[420px] max-h-[80vh] h-[540px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl flex flex-col overflow-hidden backdrop-blur-xl"
      >
        <!-- Header -->
        <div class="px-4 py-3.5 bg-gradient-to-r from-[#0066FF] to-[#0052CC] text-white flex items-center justify-between shadow-md">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
              <SparklesIcon class="w-5 h-5 text-amber-300" />
            </div>
            <div>
              <h3 class="text-sm font-bold leading-tight">FinanceFlow AI</h3>
              <p class="text-[10px] text-white/80">Konsultan Keuangan Pribadi</p>
            </div>
          </div>
          <button
            @click="isOpen = false"
            class="p-1 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Quick Questions Carousel -->
        <div class="px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800 flex gap-1.5 overflow-x-auto no-scrollbar">
          <button
            v-for="q in quickQuestions"
            :key="q"
            @click="sendMessage(q)"
            :disabled="loading"
            class="px-2.5 py-1 text-[11px] whitespace-nowrap bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-full border border-slate-200/80 dark:border-slate-700 shadow-2xs font-medium transition-colors hover:bg-slate-50 dark:hover:bg-slate-700"
          >
            {{ q }}
          </button>
        </div>

        <!-- Chat Messages Container -->
        <div ref="messagesContainer" class="flex-1 p-4 overflow-y-auto space-y-3.5 bg-slate-50/50 dark:bg-slate-900/50">
          <div
            v-for="msg in messages"
            :key="msg.id"
            :class="[
              'flex gap-2.5 max-w-[90%]',
              msg.role === 'user' ? 'ml-auto flex-row-reverse' : 'mr-auto',
            ]"
          >
            <div
              v-if="msg.role === 'assistant'"
              class="w-7 h-7 rounded-lg bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 flex items-center justify-center shrink-0 text-xs font-bold"
            >
              AI
            </div>

            <div class="group relative">
              <div
                :class="[
                  'p-3.5 rounded-2xl text-xs leading-relaxed',
                  msg.role === 'user'
                    ? 'bg-primary-600 text-white rounded-br-xs shadow-sm font-medium'
                    : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded-bl-xs border border-slate-200/80 dark:border-slate-700/80 shadow-2xs whitespace-pre-line',
                ]"
              >
                <!-- Rendered Formatted Content -->
                <div v-html="formatText(msg.text)"></div>
                <div class="flex items-center justify-between gap-3 mt-1.5 text-[9px] opacity-60">
                  <span>{{ msg.timestamp }}</span>
                  <button
                    v-if="msg.role === 'assistant'"
                    @click="copyMessage(msg)"
                    class="opacity-0 group-hover:opacity-100 transition-opacity hover:opacity-100 flex items-center gap-1 text-[9px] text-slate-500 hover:text-slate-800 dark:hover:text-white"
                    title="Salin Pesan"
                  >
                    <component :is="copiedId === msg.id ? CheckIcon : ClipboardDocumentIcon" class="w-3 h-3" />
                    <span>{{ copiedId === msg.id ? 'Disalin' : 'Salin' }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading Indicator -->
          <div v-if="loading" class="flex items-center gap-2 mr-auto bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 p-3 rounded-2xl text-xs text-slate-500 shadow-2xs">
            <ArrowPathIcon class="w-4 h-4 animate-spin text-primary-600" />
            <span>AI sedang menganalisis data keuanganmu...</span>
          </div>
        </div>

        <!-- Input Box -->
        <form @submit.prevent="sendMessage()" class="p-3 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center gap-2">
          <input
            v-model="inputMessage"
            type="text"
            placeholder="Tanya perencana keuangan..."
            :disabled="loading"
            class="flex-1 px-3.5 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500"
          />
          <button
            type="submit"
            :disabled="!inputMessage.trim() || loading"
            class="p-2.5 rounded-xl bg-primary-600 text-white disabled:opacity-40 hover:bg-primary-700 transition-colors shadow-sm"
          >
            <PaperAirplaneIcon class="w-4 h-4" />
          </button>
        </form>
      </div>
    </Transition>
  </div>
</template>
