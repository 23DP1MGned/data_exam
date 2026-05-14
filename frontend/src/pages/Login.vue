<template>
  <div class="login-page">
    <div class="login-backdrop" aria-hidden="true"></div>
    <div class="login-shape login-shape-one" aria-hidden="true"></div>
    <div class="login-shape login-shape-two" aria-hidden="true"></div>
    <div class="login-shape login-shape-three" aria-hidden="true"></div>

    <div class="login-topbar">
      <div class="login-brand">
        <div class="login-brand-mark">
          <v-icon size="24" color="white">mdi-school-outline</v-icon>
        </div>
        <div class="login-wordmark">SportSystem</div>
      </div>
      <AppLanguageSwitch />
    </div>

    <main class="login-stage">
      <section class="login-card">
        <div class="login-avatar">
          <v-icon size="44">mdi-account-circle-outline</v-icon>
        </div>

        <div class="login-head">
          <p class="login-overline">{{ t('login.platform') }}</p>
          <h1 class="login-title">{{ t('login.loginTitle') }}</h1>
        </div>

        <v-alert
          v-if="generalError"
          type="error"
          variant="tonal"
          density="comfortable"
          class="login-alert"
        >
          {{ generalError }}
        </v-alert>

        <div class="login-form">
          <v-text-field
            v-model="form.email"
            :label="t('login.email')"
            variant="underlined"
            type="email"
            autocomplete="email"
            prepend-inner-icon="mdi-account"
            class="login-field"
            :error-messages="fieldErrors.email"
            @keyup.enter="submit"
          />

          <v-text-field
            v-model="form.password"
            :label="t('login.password')"
            :type="showPassword ? 'text' : 'password'"
            variant="underlined"
            autocomplete="current-password"
            prepend-inner-icon="mdi-key"
            class="login-field"
            :error-messages="fieldErrors.password"
            :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
            @click:append-inner="showPassword = !showPassword"
            @keyup.enter="submit"
          />

          <div class="login-actions-row">
            <v-checkbox
              v-model="rememberMe"
              :label="t('login.rememberMe')"
              density="compact"
              hide-details
              class="remember-check"
            />

            <button type="button" class="login-support-link" @click="supportDialog = true">
              {{ t('login.needHelp') }}
            </button>
          </div>

          <v-btn
            color="primary"
            size="large"
            block
            class="login-btn"
            :loading="submitting"
            @click="submit"
          >
            <span class="login-btn-glow" aria-hidden="true"></span>
            <span class="login-btn-content">
              <v-icon size="18" class="login-btn-icon">mdi-lightning-bolt</v-icon>
              <span>{{ t('login.loginButton') }}</span>
              <v-icon size="18" class="login-btn-arrow">mdi-arrow-right</v-icon>
            </span>
          </v-btn>
        </div>

        <div class="login-meta">
          <div class="login-meta-title">{{ t('login.noteTitle') }}</div>
          <p class="login-meta-text">{{ t('login.note') }}</p>

          <div class="login-meta-links">
            <a class="login-meta-link" href="mailto:admin@sportsystem.app">
              admin@sportsystem.app
            </a>
            <a class="login-meta-link" href="tel:+37120000000">
              +371 20 000 000
            </a>
          </div>
        </div>
      </section>
    </main>

    <v-dialog v-model="supportDialog" max-width="420">
      <div class="support-dialog">
        <div class="support-dialog-head">
          <div>
            <div class="support-dialog-kicker">{{ t('login.noteTitle') }}</div>
            <h2 class="support-dialog-title">{{ t('login.supportDialogTitle') }}</h2>
          </div>

          <v-btn
            icon
            variant="text"
            size="small"
            class="support-dialog-close"
            @click="supportDialog = false"
          >
            <v-icon size="18">mdi-close</v-icon>
          </v-btn>
        </div>

        <p class="support-dialog-text">{{ t('login.supportDialogText') }}</p>

        <div class="support-dialog-actions">
          <a class="support-dialog-link" href="mailto:admin@sportsystem.app">
            <v-icon size="18">mdi-email-outline</v-icon>
            <span>admin@sportsystem.app</span>
          </a>

          <a class="support-dialog-link" href="tel:+37120000000">
            <v-icon size="18">mdi-phone-outline</v-icon>
            <span>+371 20 000 000</span>
          </a>
        </div>
      </div>
    </v-dialog>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppLanguageSwitch from '../components/AppLanguageSwitch.vue'
import { login } from '../services/auth'
import { useLocale } from '../i18n'

const router = useRouter()
const { t } = useLocale()

const form = reactive({
  email: '',
  password: ''
})

const fieldErrors = reactive({
  email: [],
  password: []
})

const generalError = ref('')
const submitting = ref(false)
const showPassword = ref(false)
const rememberMe = ref(true)
const supportDialog = ref(false)

function resetErrors() {
  fieldErrors.email = []
  fieldErrors.password = []
  generalError.value = ''
}

async function submit() {
  resetErrors()

  if (!form.email) fieldErrors.email = [t('login.requiredEmail')]
  if (!form.password) fieldErrors.password = [t('login.requiredPassword')]
  if (fieldErrors.email.length || fieldErrors.password.length) return

  submitting.value = true

  try {
    await login({
      email: form.email,
      password: form.password,
      remember: rememberMe.value
    })

    router.push('/home')
  } catch (error) {
    const errors = error?.response?.data?.errors ?? {}
    fieldErrors.email = errors.email ?? []
    fieldErrors.password = errors.password ?? []
    generalError.value = error?.response?.data?.message || t('login.failed')
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.login-page {
  position: relative;
  min-height: 100vh;
  overflow: hidden;
  padding: 24px;
  background:
    radial-gradient(circle at 16% 14%, rgba(214, 237, 255, 0.78), transparent 24%),
    radial-gradient(circle at 84% 18%, rgba(171, 214, 245, 0.7), transparent 28%),
    radial-gradient(circle at 50% 82%, rgba(143, 194, 236, 0.24), transparent 34%),
    linear-gradient(180deg, #dff0ff 0%, #c9e4fb 40%, #b7d7f4 100%);
}

.login-backdrop {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.06)),
    radial-gradient(circle at 50% 22%, rgba(255, 255, 255, 0.24), transparent 34%),
    radial-gradient(circle at 30% 72%, rgba(255, 255, 255, 0.12), transparent 30%);
  pointer-events: none;
}

.login-shape {
  position: absolute;
  z-index: 0;
  pointer-events: none;
  opacity: 0.72;
  filter: blur(22px);
  border-radius: 999px;
}

.login-shape-one {
  top: 120px;
  left: 10%;
  width: 240px;
  height: 240px;
  background: radial-gradient(circle at 35% 35%, rgba(255, 255, 255, 0.72), rgba(132, 197, 244, 0.42) 58%, rgba(132, 197, 244, 0.06) 100%);
}

.login-shape-two {
  right: 12%;
  top: 180px;
  width: 190px;
  height: 190px;
  background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.62), rgba(116, 181, 236, 0.4) 60%, rgba(116, 181, 236, 0.04) 100%);
}

.login-shape-three {
  left: 50%;
  bottom: 70px;
  width: 320px;
  height: 120px;
  transform: translateX(-50%) rotate(-10deg);
  border-radius: 38px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.28), rgba(110, 180, 236, 0.24));
}

.login-topbar {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  max-width: 1180px;
  margin: 0 auto 18px;
  padding: 0 6px;
}

.login-brand {
  display: inline-flex;
  align-items: center;
  gap: 14px;
}

.login-brand-mark {
  display: grid;
  place-items: center;
  position: relative;
  width: 54px;
  height: 54px;
  overflow: hidden;
  isolation: isolate;
  border-radius: 18px;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  border: 1px solid rgba(255, 255, 255, 0.18);
  box-shadow: none;
}

.login-wordmark {
  color: #315472;
  font-size: 1rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.45);
}

.login-stage {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: calc(100vh - 86px);
}

.login-card {
  position: relative;
  width: min(100%, 560px);
  padding: 40px 42px 30px;
  overflow: hidden;
  border: 1px solid rgba(219, 234, 246, 0.96);
  border-radius: 34px;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.84), rgba(243, 249, 255, 0.76));
  backdrop-filter: blur(20px);
  box-shadow:
    0 28px 68px rgba(72, 112, 146, 0.14),
    inset 0 1px 0 rgba(255, 255, 255, 0.72);
}

.login-card::before {
  content: '';
  position: absolute;
  inset: 0 0 auto 0;
  height: 140px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.48), transparent);
  pointer-events: none;
}

.login-card::after {
  content: '';
  position: absolute;
  top: 18px;
  left: 18px;
  right: 18px;
  height: 1px;
  background: rgba(255, 255, 255, 0.75);
  pointer-events: none;
}

.login-avatar {
  position: relative;
  z-index: 1;
  display: grid;
  place-items: center;
  width: 86px;
  height: 86px;
  margin: 0 auto 18px;
  border-radius: 999px;
  color: #3872a0;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(225, 240, 251, 0.78));
  border: 1px solid rgba(225, 238, 248, 0.95);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.82),
    0 12px 24px rgba(96, 135, 171, 0.12);
}

.login-head {
  position: relative;
  z-index: 1;
  text-align: center;
}

.login-overline {
  margin: 0;
  color: #7390aa;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.login-title {
  margin: 10px 0 0;
  color: #172433;
  font-size: clamp(2.2rem, 4vw, 3rem);
  line-height: 1.08;
  font-weight: 400;
  letter-spacing: 0;
}

.login-subtitle {
  max-width: 360px;
  margin: 14px auto 0;
  color: #6f8297;
  font-size: 1rem;
  line-height: 1.55;
}

.login-alert {
  position: relative;
  z-index: 1;
  margin-top: 26px;
  margin-bottom: 10px;
}

.login-form {
  position: relative;
  z-index: 1;
  margin-top: 34px;
}

.login-field {
  margin-bottom: 12px;
}

.login-field :deep(.v-field) {
  border-radius: 18px 18px 14px 14px;
  background: rgba(255, 255, 255, 0.42);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.56),
    0 8px 18px rgba(121, 156, 190, 0.08);
}

.login-field :deep(.v-field__prepend-inner),
.login-field :deep(.v-field__append-inner),
.login-field :deep(.v-label),
.login-field :deep(input) {
  color: #314557;
}

.login-field :deep(.v-field__outline::before),
.login-field :deep(.v-field__outline::after) {
  border-color: rgba(128, 160, 189, 0.58) !important;
}

.login-field :deep(.v-field--focused .v-field__outline::before),
.login-field :deep(.v-field--focused .v-field__outline::after) {
  border-color: #2282d0 !important;
}

.login-field :deep(input:-webkit-autofill),
.login-field :deep(input:-webkit-autofill:hover),
.login-field :deep(input:-webkit-autofill:focus),
.login-field :deep(input:-webkit-autofill:active) {
  -webkit-text-fill-color: #314557;
  caret-color: #314557;
  transition: background-color 9999s ease-out 0s;
  box-shadow:
    0 0 0 1000px rgba(236, 245, 252, 0.9) inset,
    inset 0 1px 0 rgba(255, 255, 255, 0.56),
    0 8px 18px rgba(121, 156, 190, 0.08);
  border-radius: 18px 18px 14px 14px;
}

.login-field :deep(input:-webkit-autofill) + *,
.login-field :deep(input:-webkit-autofill:hover) + *,
.login-field :deep(input:-webkit-autofill:focus) + * {
  color: #314557 !important;
}

.login-field :deep(.v-messages) {
  min-height: 18px;
}

.login-actions-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin: 14px 0 22px;
}

.remember-check {
  margin: 0;
  color: #425669;
}

.remember-check :deep(.v-selection-control) {
  min-height: 28px;
}

.remember-check :deep(.v-label) {
  color: #465a6d;
  opacity: 1;
}

.login-support-link {
  border: 0;
  padding: 0;
  background: transparent;
  cursor: pointer;
  color: #496683;
  font-size: 0.92rem;
  text-decoration: none;
}

.login-support-link:hover {
  color: #183f66;
  text-decoration: underline;
}

.login-btn {
  position: relative;
  overflow: hidden;
  min-height: 56px;
  background: linear-gradient(180deg, #79ccfb 0%, #49ace5 54%, #3298d6 100%) !important;
  border-radius: 18px;
  border: 1px solid rgba(144, 207, 243, 0.9);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 700;
  box-shadow:
    0 18px 34px rgba(62, 160, 220, 0.22),
    0 8px 18px rgba(79, 185, 238, 0.16),
    inset 0 1px 0 rgba(255, 255, 255, 0.42);
  transition:
    transform 0.18s ease,
    box-shadow 0.18s ease,
    filter 0.18s ease,
    background 0.18s ease;
}

.login-btn-glow {
  position: absolute;
  inset: 0;
  background: linear-gradient(115deg, transparent 18%, rgba(255, 255, 255, 0.36) 48%, transparent 72%);
  transform: translateX(-135%);
  transition: transform 0.75s ease;
  pointer-events: none;
}

.login-btn-content {
  position: relative;
  z-index: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
}

.login-btn-icon,
.login-btn-arrow {
  opacity: 0.96;
}

.login-btn-arrow {
  transition: transform 0.18s ease;
}

.login-btn:hover {
  transform: translateY(-2px);
  filter: saturate(1.08);
  background: linear-gradient(180deg, #89d2fd 0%, #55b4ea 54%, #3298d6 100%) !important;
  box-shadow:
    0 22px 38px rgba(62, 160, 220, 0.24),
    0 10px 22px rgba(79, 185, 238, 0.18),
    inset 0 1px 0 rgba(255, 255, 255, 0.46);
}

.login-btn:hover .login-btn-glow {
  transform: translateX(135%);
}

.login-btn:hover .login-btn-arrow {
  transform: translateX(3px);
}

.login-btn:active {
  transform: translateY(0);
}

.login-btn :deep(.v-btn__overlay) {
  opacity: 0 !important;
}

.login-btn :deep(.v-progress-circular) {
  position: relative;
  z-index: 1;
}

.login-meta {
  position: relative;
  z-index: 1;
  margin-top: 28px;
  padding-top: 18px;
  border-top: 1px solid rgba(87, 108, 129, 0.18);
  text-align: center;
}

.login-meta-title {
  color: #48617a;
  font-size: 0.84rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.login-meta-text {
  max-width: 420px;
  margin: 10px auto 0;
  color: #6d8195;
  font-size: 0.94rem;
  line-height: 1.55;
}

.login-meta-links {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 14px;
  margin-top: 14px;
}

.login-meta-link {
  color: #6281a0;
  font-size: 0.92rem;
  text-decoration: none;
}

.login-meta-link:hover {
  color: #163e65;
  text-decoration: underline;
}

.support-dialog {
  padding: 22px;
  border: 1px solid rgba(219, 234, 246, 0.96);
  border-radius: 24px;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(243, 249, 255, 0.92));
  box-shadow: 0 24px 50px rgba(72, 112, 146, 0.16);
}

.support-dialog-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
}

.support-dialog-kicker {
  color: #7390aa;
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.support-dialog-title {
  margin: 8px 0 0;
  color: #1e3144;
  font-size: 1.32rem;
  line-height: 1.2;
  font-weight: 700;
}

.support-dialog-close {
  color: #5e7690;
}

.support-dialog-text {
  margin: 14px 0 0;
  color: #667d94;
  font-size: 0.95rem;
  line-height: 1.6;
}

.support-dialog-actions {
  display: grid;
  gap: 10px;
  margin-top: 18px;
}

.support-dialog-link {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  min-height: 46px;
  padding: 0 14px;
  border: 1px solid rgba(214, 228, 241, 0.96);
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.72);
  color: #4f6d89;
  text-decoration: none;
  transition: background-color 0.16s ease, border-color 0.16s ease, color 0.16s ease;
}

.support-dialog-link:hover {
  border-color: rgba(159, 195, 222, 0.96);
  background: rgba(244, 250, 255, 0.96);
  color: #214e74;
}

@media (max-width: 760px) {
  .login-page {
    padding: 16px;
  }

  .login-stage {
    min-height: auto;
    padding: 16px 0 24px;
  }

  .login-card {
    padding: 30px 22px 24px;
    border-radius: 28px;
  }

  .login-shape-one {
    top: 90px;
    left: -30px;
    width: 170px;
    height: 170px;
  }

  .login-shape-two {
    top: auto;
    right: -20px;
    bottom: 170px;
    width: 150px;
    height: 150px;
  }

  .login-shape-three {
    bottom: 24px;
    width: 240px;
    height: 90px;
  }

  .login-actions-row {
    align-items: flex-start;
    flex-direction: column;
  }
}

@media (max-width: 520px) {
  .login-page {
    padding: 12px;
  }

  .login-title {
    font-size: 2rem;
  }

  .login-subtitle {
    font-size: 0.94rem;
  }

  .login-topbar {
    margin-bottom: 12px;
  }

  .login-brand-mark {
    width: 46px;
    height: 46px;
    border-radius: 16px;
  }

  .login-card {
    padding: 26px 18px 20px;
    border-radius: 24px;
  }

  .login-shape {
    filter: blur(18px);
  }
}

</style>
