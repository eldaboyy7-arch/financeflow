<script setup lang="ts">
withDefaults(
  defineProps<{
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
    text?: string
    subtext?: string
    fullPage?: boolean
  }>(),
  {
    size: 'md',
    text: '',
    subtext: '',
    fullPage: false,
  }
)
</script>

<template>
  <div
    :class="[
      'flex flex-col items-center justify-center select-none transition-all duration-300',
      fullPage
        ? 'fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-md'
        : 'py-3.5 px-2 sm:py-5 sm:px-4',
    ]"
  >
    <!-- 3D Coin Stage -->
    <div class="relative flex flex-col items-center justify-center">
      <!-- Soft Breathing Halo -->
      <div
        :class="[
          'absolute rounded-full pointer-events-none opacity-40 dark:opacity-30 animate-pulse-gentle',
          size === 'xs' ? 'w-5 h-5 bg-blue-400/20 blur-sm' : '',
          size === 'sm' ? 'w-7 h-7 bg-blue-400/25 blur-sm' : '',
          size === 'md' ? 'w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-tr from-[#0066FF]/25 to-amber-400/25 blur-md' : '',
          size === 'lg' ? 'w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-tr from-[#0066FF]/30 to-amber-400/30 blur-lg' : '',
          size === 'xl' ? 'w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-tr from-[#0066FF]/35 to-amber-400/35 blur-xl' : '',
        ]"
      ></div>

      <!-- 3D Coin Wrapper -->
      <div
        :class="[
          'coin-wrapper relative z-10',
          size === 'xs' ? 'w-4 h-4' : '',
          size === 'sm' ? 'w-6 h-6' : '',
          size === 'md' ? 'w-8 h-8 sm:w-9 sm:h-9' : '',
          size === 'lg' ? 'w-9 h-9 sm:w-11 sm:h-11' : '',
          size === 'xl' ? 'w-11 h-11 sm:w-14 sm:h-14' : '',
        ]"
      >
        <div class="coin">
          <!-- Front Face: 3D Gold Coin with 'Rp' -->
          <div class="coin-face coin-front">
            <div class="coin-inner-rim">
              <span
                :class="[
                  'font-black font-mono tracking-tighter text-amber-950 leading-none select-none',
                  size === 'xs' ? 'text-[6px]' : '',
                  size === 'sm' ? 'text-[8px]' : '',
                  size === 'md' ? 'text-[10px] sm:text-[11px]' : '',
                  size === 'lg' ? 'text-xs sm:text-[13px]' : '',
                  size === 'xl' ? 'text-[13px] sm:text-base' : '',
                ]"
              >
                Rp
              </span>
            </div>
            <!-- Specular Glint -->
            <div class="coin-glint"></div>
          </div>

          <!-- Back Face: FinanceFlow Electric Blue Coin -->
          <div class="coin-face coin-back">
            <div class="coin-inner-rim-back">
              <svg
                viewBox="0 0 100 100"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                :class="[
                  size === 'xs' ? 'w-2 h-2' : '',
                  size === 'sm' ? 'w-2.5 h-2.5' : '',
                  size === 'md' ? 'w-3.5 h-3.5 sm:w-4 sm:h-4' : '',
                  size === 'lg' ? 'w-4 h-4 sm:w-4.5 sm:h-4.5' : '',
                  size === 'xl' ? 'w-5 h-5 sm:w-6 sm:h-6' : '',
                ]"
              >
                <path
                  d="M 16 38 C 24 16 52 8 92 8 C 96 8 97 12 94 16 C 80 30 58 38 36 38 C 26 38 18 38 16 38 Z"
                  fill="#FFFFFF"
                />
                <path
                  d="M 6 92 L 22 46 C 36 46 64 46 80 46 C 86 46 87 51 82 56 C 68 68 46 72 26 72 L 15 92 H 6 Z"
                  fill="#93C5FD"
                />
              </svg>
            </div>
            <div class="coin-glint"></div>
          </div>
        </div>
      </div>

      <!-- Synchronized Ground Shadow -->
      <div
        :class="[
          'coin-shadow rounded-full bg-slate-900/25 dark:bg-black/60 blur-[1px] mt-1',
          size === 'xs' ? 'w-3 h-0.5' : '',
          size === 'sm' ? 'w-4 h-0.5' : '',
          size === 'md' ? 'w-6 h-1 sm:w-7 sm:h-1' : '',
          size === 'lg' ? 'w-7 h-1 sm:w-8 sm:h-1.5' : '',
          size === 'xl' ? 'w-9 h-1.5 sm:w-11 sm:h-2' : '',
        ]"
      ></div>
    </div>

    <!-- Title & Subtitle -->
    <div v-if="text || subtext" class="text-center mt-2.5 sm:mt-3 space-y-0.5 max-w-[240px] sm:max-w-xs">
      <p
        v-if="text"
        :class="[
          'font-bold tracking-tight text-xs sm:text-sm flex items-center justify-center gap-1',
          fullPage ? 'text-white' : 'text-slate-800 dark:text-slate-200',
        ]"
      >
        <span>{{ text }}</span>
        <span class="inline-flex gap-0.5">
          <span class="w-1 h-1 rounded-full bg-current animate-bounce" style="animation-delay: 0ms"></span>
          <span class="w-1 h-1 rounded-full bg-current animate-bounce" style="animation-delay: 150ms"></span>
          <span class="w-1 h-1 rounded-full bg-current animate-bounce" style="animation-delay: 300ms"></span>
        </span>
      </p>
      <p
        v-if="subtext"
        :class="[
          'text-[10px] sm:text-xs leading-tight',
          fullPage ? 'text-slate-300' : 'text-slate-400 dark:text-slate-500',
        ]"
      >
        {{ subtext }}
      </p>
    </div>
  </div>
</template>

<style scoped>
.coin-wrapper {
  perspective: 600px;
  transform: translateZ(0);
  will-change: transform;
}

.coin {
  width: 100%;
  height: 100%;
  position: relative;
  transform-style: preserve-3d;
  animation: spinCoinSmooth 1.35s cubic-bezier(0.42, 0, 0.58, 1) infinite;
  will-change: transform;
}

.coin-face {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  box-shadow: inset 0 0 0 1.2px rgba(255, 255, 255, 0.4),
              0 2px 6px rgba(0, 0, 0, 0.14);
  transform: translateZ(0);
}

/* Front Face: Polished Gold Coin */
.coin-front {
  background: radial-gradient(circle at 35% 25%, #fef08a 0%, #eab308 40%, #ca8a04 80%, #78350f 100%);
  transform: rotateY(0deg) translateZ(0);
}

.coin-inner-rim {
  width: 80%;
  height: 80%;
  border-radius: 50%;
  border: 1px dashed rgba(120, 53, 15, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at 38% 30%, #fef9c3 0%, #facc15 50%, #b45309 100%);
  box-shadow: inset 0 1px 1.5px rgba(0, 0, 0, 0.2);
}

/* Back Face: FinanceFlow Electric Blue Coin */
.coin-back {
  background: radial-gradient(circle at 35% 25%, #93c5fd 0%, #0066ff 45%, #0047ba 80%, #001a44 100%);
  transform: rotateY(180deg) translateZ(0);
}

.coin-inner-rim-back {
  width: 80%;
  height: 80%;
  border-radius: 50%;
  border: 1px dashed rgba(255, 255, 255, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at 38% 30%, #60a5fa 0%, #0066ff 60%, #003882 100%);
  box-shadow: inset 0 1px 1.5px rgba(0, 0, 0, 0.25);
}

/* Specular Light Sweep */
.coin-glint {
  position: absolute;
  top: -60%;
  left: -60%;
  width: 220%;
  height: 220%;
  background: linear-gradient(
    45deg,
    transparent 38%,
    rgba(255, 255, 255, 0.45) 50%,
    transparent 62%
  );
  pointer-events: none;
  animation: glintSweep 2.7s ease-in-out infinite;
}

.coin-shadow {
  animation: shadowPulse 1.35s cubic-bezier(0.42, 0, 0.58, 1) infinite;
  will-change: transform, opacity;
}

@keyframes spinCoinSmooth {
  0% {
    transform: rotateY(0deg) translateY(0px);
  }
  50% {
    transform: rotateY(180deg) translateY(-3px);
  }
  100% {
    transform: rotateY(360deg) translateY(0px);
  }
}

@keyframes shadowPulse {
  0%, 100% {
    transform: scale(0.9);
    opacity: 0.25;
  }
  50% {
    transform: scale(1.15);
    opacity: 0.5;
  }
}

@keyframes glintSweep {
  0%, 100% {
    transform: translateX(-30%) translateY(-30%);
  }
  50% {
    transform: translateX(30%) translateY(30%);
  }
}

@keyframes pulseGentle {
  0%, 100% {
    transform: scale(1);
    opacity: 0.25;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.45;
  }
}

.animate-pulse-gentle {
  animation: pulseGentle 2.2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
