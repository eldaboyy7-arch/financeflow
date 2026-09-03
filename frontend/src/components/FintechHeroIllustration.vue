<script setup lang="ts">
withDefaults(
  defineProps<{
    size?: 'sm' | 'md' | 'lg'
  }>(),
  {
    size: 'md',
  }
)
</script>

<template>
  <div
    :class="[
      'relative flex items-center justify-center select-none pointer-events-none',
      size === 'sm' ? 'w-48 h-36' : '',
      size === 'md' ? 'w-full max-w-[420px] h-[280px]' : '',
      size === 'lg' ? 'w-full max-w-[500px] h-[340px]' : '',
    ]"
  >
    <svg
      viewBox="0 0 500 360"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      class="w-full h-full filter drop-shadow-2xl overflow-visible"
    >
      <defs>
        <!-- Gradients -->
        <linearGradient id="cardGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#0066FF" />
          <stop offset="50%" stop-color="#0047BA" />
          <stop offset="100%" stop-color="#001F54" />
        </linearGradient>

        <linearGradient id="cardGrad2" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#38BDF8" />
          <stop offset="100%" stop-color="#0066FF" />
        </linearGradient>

        <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#FEF08A" />
          <stop offset="40%" stop-color="#FACC15" />
          <stop offset="100%" stop-color="#B45309" />
        </linearGradient>

        <linearGradient id="chartGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#10B981" stop-opacity="0.5" />
          <stop offset="100%" stop-color="#10B981" stop-opacity="0.0" />
        </linearGradient>

        <linearGradient id="glassGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.25" />
          <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.08" />
        </linearGradient>

        <linearGradient id="phoneGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#1E293B" />
          <stop offset="100%" stop-color="#0F172A" />
        </linearGradient>

        <!-- Glow Filters -->
        <filter id="glowBlue" x="-20%" y="-20%" width="140%" height="140%">
          <feGaussianBlur stdDeviation="8" result="blur" />
          <feComposite in="SourceGraphic" in2="blur" operator="over" />
        </filter>

        <filter id="shadowSoft" x="-10%" y="-10%" width="130%" height="130%">
          <feDropShadow dx="0" dy="12" stdDeviation="10" flood-color="#000000" flood-opacity="0.25" />
        </filter>
      </defs>

      <!-- Ambient Glow Behind Phone -->
      <circle cx="250" cy="180" r="140" fill="#38BDF8" opacity="0.15" filter="url(#glowBlue)" />
      <circle cx="360" cy="120" r="80" fill="#0066FF" opacity="0.2" filter="url(#glowBlue)" />

      <!-- Isometric Base Platform / Shadow -->
      <ellipse cx="250" cy="300" rx="190" ry="40" fill="#000000" opacity="0.18" filter="url(#glowBlue)" />

      <!-- ================= 1. CENTRAL SMARTPHONE MOCKUP ================= -->
      <g class="anim-float-main" filter="url(#shadowSoft)">
        <!-- Phone Outer Body -->
        <rect x="175" y="45" width="150" height="250" rx="28" fill="url(#phoneGrad)" stroke="#38BDF8" stroke-opacity="0.4" stroke-width="2" />

        <!-- Phone Inner Screen -->
        <rect x="183" y="53" width="134" height="234" rx="22" fill="#0B132B" />

        <!-- Phone Notch / Dynamic Island -->
        <rect x="225" y="58" width="50" height="10" rx="5" fill="#020617" />
        <circle cx="264" cy="63" r="2.5" fill="#38BDF8" opacity="0.8" />

        <!-- Screen Content: Top Mini Balance Card -->
        <rect x="193" y="76" width="114" height="60" rx="14" fill="url(#cardGrad)" />
        <text x="203" y="93" fill="#93C5FD" font-size="8" font-family="sans-serif" font-weight="600">TOTAL SALDO</text>
        <text x="203" y="110" fill="#FFFFFF" font-size="13" font-family="sans-serif" font-weight="900">Rp 48.5M</text>
        <text x="203" y="124" fill="#34D399" font-size="8" font-family="sans-serif" font-weight="700">▲ +24.8% bln ini</text>

        <!-- Mini Sparkline Area inside screen -->
        <path d="M 193 175 Q 215 155 240 168 T 280 145 T 307 138 L 307 190 L 193 190 Z" fill="url(#chartGrad)" />
        <path d="M 193 175 Q 215 155 240 168 T 280 145 T 307 138" stroke="#10B981" stroke-width="2" fill="none" />
        <circle cx="307" cy="138" r="3.5" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />

        <!-- Quick Action Buttons on Phone -->
        <rect x="193" y="198" width="54" height="24" rx="8" fill="#1E293B" />
        <text x="208" y="214" fill="#38BDF8" font-size="8" font-family="sans-serif" font-weight="700">⚡ Kirim</text>

        <rect x="253" y="198" width="54" height="24" rx="8" fill="#1E293B" />
        <text x="268" y="214" fill="#34D399" font-size="8" font-family="sans-serif" font-weight="700">📷 Scan</text>

        <!-- Bottom Transaction Row inside Screen -->
        <rect x="193" y="230" width="114" height="26" rx="9" fill="#1E293B" opacity="0.8" />
        <circle cx="206" cy="243" r="6" fill="#10B981" opacity="0.2" />
        <text x="203" y="246" fill="#10B981" font-size="9">💰</text>
        <text x="218" y="241" fill="#F8FAFC" font-size="8" font-weight="bold" font-family="sans-serif">Gaji Bulanan</text>
        <text x="218" y="250" fill="#64748B" font-size="6.5" font-family="sans-serif">Hari ini</text>
        <text x="274" y="245" fill="#34D399" font-size="8" font-weight="bold" font-family="sans-serif">+12.5M</text>
      </g>

      <!-- ================= 2. FLOATING PREMIUM CREDIT CARD (LEFT) ================= -->
      <g class="anim-float-card-left" filter="url(#shadowSoft)">
        <!-- Tilted Credit Card -->
        <g transform="translate(60, 110) rotate(-14)">
          <rect width="130" height="82" rx="14" fill="url(#cardGrad2)" stroke="#FFFFFF" stroke-opacity="0.35" stroke-width="1.5" />
          
          <!-- Gloss wave highlight -->
          <path d="M 0 35 C 30 15 70 45 130 20 L 130 0 L 0 0 Z" fill="#FFFFFF" opacity="0.15" />

          <!-- Card Chip -->
          <rect x="14" y="18" width="18" height="14" rx="3.5" fill="#FEF08A" stroke="#CA8A04" stroke-width="0.8" />
          <line x1="20" y1="18" x2="20" y2="32" stroke="#CA8A04" stroke-width="0.5" />
          <line x1="26" y1="18" x2="26" y2="32" stroke="#CA8A04" stroke-width="0.5" />
          <line x1="14" y1="25" x2="32" y2="25" stroke="#CA8A04" stroke-width="0.5" />

          <!-- Contactless Wave -->
          <path d="M 38 21 A 6 6 0 0 1 38 29 M 41 18 A 10 10 0 0 1 41 32" stroke="#FFFFFF" stroke-width="1.2" stroke-linecap="round" fill="none" opacity="0.8" />

          <!-- Card Text -->
          <text x="14" y="54" fill="#FFFFFF" font-size="9" font-family="monospace" font-weight="bold" letter-spacing="1">•••• 8892</text>
          <text x="14" y="68" fill="#E0F2FE" font-size="7" font-family="sans-serif" font-weight="600">FINANCEFLOW PLATINUM</text>

          <!-- Dual Circle Logo -->
          <circle cx="108" cy="62" r="8" fill="#EF4444" opacity="0.85" />
          <circle cx="116" cy="62" r="8" fill="#F59E0B" opacity="0.85" />
        </g>
      </g>

      <!-- ================= 3. FLOATING SMART RECEIPT / INVOICE (RIGHT) ================= -->
      <g class="anim-float-receipt" filter="url(#shadowSoft)">
        <g transform="translate(325, 95) rotate(10)">
          <!-- Glass Receipt Body -->
          <rect width="115" height="135" rx="16" fill="url(#glassGrad)" backdrop-filter="blur(10px)" stroke="#FFFFFF" stroke-opacity="0.4" stroke-width="1.5" />

          <!-- Header Icon & Title -->
          <circle cx="24" cy="24" r="10" fill="#10B981" fill-opacity="0.2" />
          <text x="19" y="28" fill="#10B981" font-size="12">✓</text>
          <text x="40" y="22" fill="#FFFFFF" font-size="9" font-weight="bold" font-family="sans-serif">Struk OCR AI</text>
          <text x="40" y="31" fill="#93C5FD" font-size="7" font-family="sans-serif">Terverifikasi</text>

          <!-- Divider Line -->
          <line x1="14" y1="42" x2="101" y2="42" stroke="#FFFFFF" stroke-opacity="0.2" stroke-dasharray="3 3" />

          <!-- Receipt Rows -->
          <text x="16" y="58" fill="#E2E8F0" font-size="8" font-family="sans-serif">Kopi Kenangan</text>
          <text x="76" y="58" fill="#FFFFFF" font-size="8" font-weight="bold" font-family="sans-serif">38.000</text>

          <text x="16" y="73" fill="#E2E8F0" font-size="8" font-family="sans-serif">Pajak Resto (10%)</text>
          <text x="82" y="73" fill="#FFFFFF" font-size="8" font-weight="bold" font-family="sans-serif">3.800</text>

          <line x1="14" y1="83" x2="101" y2="83" stroke="#FFFFFF" stroke-opacity="0.2" />

          <text x="16" y="98" fill="#93C5FD" font-size="8" font-weight="bold" font-family="sans-serif">TOTAL</text>
          <text x="64" y="98" fill="#34D399" font-size="10" font-weight="900" font-family="sans-serif">Rp 41.800</text>

          <!-- Mini Barcode -->
          <g transform="translate(18, 108)">
            <rect x="0" y="0" width="2" height="14" fill="#FFFFFF" opacity="0.6" />
            <rect x="4" y="0" width="3" height="14" fill="#FFFFFF" opacity="0.8" />
            <rect x="9" y="0" width="1" height="14" fill="#FFFFFF" opacity="0.6" />
            <rect x="12" y="0" width="4" height="14" fill="#FFFFFF" opacity="0.9" />
            <rect x="18" y="0" width="2" height="14" fill="#FFFFFF" opacity="0.7" />
            <rect x="22" y="0" width="3" height="14" fill="#FFFFFF" opacity="0.8" />
            <rect x="27" y="0" width="1" height="14" fill="#FFFFFF" opacity="0.5" />
            <rect x="30" y="0" width="4" height="14" fill="#FFFFFF" opacity="0.9" />
            <rect x="36" y="0" width="2" height="14" fill="#FFFFFF" opacity="0.7" />
            <rect x="40" y="0" width="3" height="14" fill="#FFFFFF" opacity="0.8" />
            <rect x="45" y="0" width="2" height="14" fill="#FFFFFF" opacity="0.6" />
            <rect x="49" y="0" width="4" height="14" fill="#FFFFFF" opacity="0.9" />
            <rect x="55" y="0" width="2" height="14" fill="#FFFFFF" opacity="0.7" />
            <rect x="59" y="0" width="3" height="14" fill="#FFFFFF" opacity="0.8" />
            <rect x="64" y="0" width="1" height="14" fill="#FFFFFF" opacity="0.5" />
            <rect x="67" y="0" width="4" height="14" fill="#FFFFFF" opacity="0.9" />
            <rect x="73" y="0" width="2" height="14" fill="#FFFFFF" opacity="0.7" />
          </g>
        </g>
      </g>

      <!-- ================= 4. 3D GOLDEN FLOATING COINS & PARTICLES ================= -->
      <!-- Big Golden Coin 1 -->
      <g class="anim-float-coin-1" filter="url(#shadowSoft)">
        <g transform="translate(130, 240)">
          <circle cx="20" cy="20" r="22" fill="url(#goldGrad)" stroke="#FFFFFF" stroke-opacity="0.5" stroke-width="1.5" />
          <circle cx="20" cy="20" r="17" fill="none" stroke="#78350F" stroke-opacity="0.35" stroke-dasharray="2 2" stroke-width="1.2" />
          <text x="12" y="26" fill="#78350F" font-size="14" font-family="sans-serif" font-weight="900">Rp</text>
        </g>
      </g>

      <!-- Medium Golden Coin 2 -->
      <g class="anim-float-coin-2" filter="url(#shadowSoft)">
        <g transform="translate(365, 235)">
          <circle cx="16" cy="16" r="16" fill="url(#goldGrad)" stroke="#FFFFFF" stroke-opacity="0.5" stroke-width="1.2" />
          <circle cx="16" cy="16" r="12" fill="none" stroke="#78350F" stroke-opacity="0.35" stroke-dasharray="2 2" stroke-width="1" />
          <text x="10" y="21" fill="#78350F" font-size="10" font-family="sans-serif" font-weight="900">Rp</text>
        </g>
      </g>

      <!-- Small Shiny Golden Coin 3 -->
      <g class="anim-float-coin-3" filter="url(#shadowSoft)">
        <g transform="translate(290, 30)">
          <circle cx="12" cy="12" r="12" fill="url(#goldGrad)" stroke="#FFFFFF" stroke-opacity="0.6" stroke-width="1" />
          <text x="7" y="16" fill="#78350F" font-size="9" font-family="sans-serif" font-weight="900">★</text>
        </g>
      </g>

      <!-- Sparkle Stars (Flutter Style) -->
      <g class="anim-sparkle">
        <path d="M 85 85 Q 90 90 95 85 Q 90 80 85 85 Z" fill="#FACC15" />
        <circle cx="90" cy="85" r="2" fill="#FFFFFF" />

        <path d="M 420 70 Q 425 75 430 70 Q 425 65 420 70 Z" fill="#38BDF8" />
        <circle cx="425" cy="70" r="1.5" fill="#FFFFFF" />

        <path d="M 120 200 Q 123 203 126 200 Q 123 197 120 200 Z" fill="#34D399" />
      </g>
    </svg>
  </div>
</template>

<style scoped>
@keyframes floatMain {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-8px);
  }
}

@keyframes floatCardLeft {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-12px) rotate(-2deg);
  }
}

@keyframes floatReceipt {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-10px) rotate(2deg);
  }
}

@keyframes floatCoin1 {
  0%, 100% {
    transform: translateY(0px) scale(1);
  }
  50% {
    transform: translateY(-14px) scale(1.04);
  }
}

@keyframes floatCoin2 {
  0%, 100% {
    transform: translateY(0px) scale(1);
  }
  50% {
    transform: translateY(-9px) scale(1.03);
  }
}

@keyframes floatCoin3 {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-7px) rotate(15deg);
  }
}

@keyframes sparkleGlow {
  0%, 100% {
    opacity: 0.4;
    transform: scale(0.8);
  }
  50% {
    opacity: 1;
    transform: scale(1.2);
  }
}

.anim-float-main {
  animation: floatMain 4.5s ease-in-out infinite;
}

.anim-float-card-left {
  animation: floatCardLeft 5s ease-in-out infinite;
  animation-delay: 0.5s;
}

.anim-float-receipt {
  animation: floatReceipt 5.5s ease-in-out infinite;
  animation-delay: 1s;
}

.anim-float-coin-1 {
  animation: floatCoin1 4s ease-in-out infinite;
  animation-delay: 0.3s;
}

.anim-float-coin-2 {
  animation: floatCoin2 4.8s ease-in-out infinite;
  animation-delay: 1.2s;
}

.anim-float-coin-3 {
  animation: floatCoin3 3.8s ease-in-out infinite;
  animation-delay: 0.7s;
}

.anim-sparkle {
  animation: sparkleGlow 2.5s ease-in-out infinite;
}
</style>
