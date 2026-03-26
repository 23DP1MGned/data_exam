<template>
  <div class="login-page">
    <div class="login-aurora login-aurora-left" aria-hidden="true"></div>
    <div class="login-aurora login-aurora-right" aria-hidden="true"></div>
    <div class="login-grid">
      <section class="login-showcase">
        <div class="showcase-badge">
          <v-icon size="18">mdi-school-outline</v-icon>
          SportSystem
        </div>

        <div class="showcase-copy">
          <p class="showcase-eyebrow">Training platform</p>
          <h1 class="showcase-title">Manage sessions, attendance and payments in one calm workspace.</h1>
          <p class="showcase-text">
            Built for admins, coaches and families who need one reliable system for the daily training flow.
          </p>
        </div>

        <div class="showcase-highlights">
          <article class="showcase-card">
            <div class="showcase-card-label">Scheduling</div>
            <div class="showcase-card-value">Weekly plans with real dated sessions</div>
          </article>

          <article class="showcase-card">
            <div class="showcase-card-label">Attendance</div>
            <div class="showcase-card-value">Coach marking, history and calendar view</div>
          </article>

          <article class="showcase-card">
            <div class="showcase-card-label">Payments</div>
            <div class="showcase-card-value">Per training, monthly plans and refunds</div>
          </article>
        </div>

        <div class="showcase-roles">
          <span class="showcase-role">Admin</span>
          <span class="showcase-role">Coach</span>
          <span class="showcase-role">Parent</span>
          <span class="showcase-role">Child</span>
        </div>
      </section>

      <section class="login-panel">
        <div class="login-panel-head">
          <div class="login-eyebrow">Welcome back</div>
          <h2 class="login-title">Log in to your workspace</h2>
          <p class="login-subtitle">Use your account to open your role-based dashboard.</p>
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
            label="Email"
            variant="outlined"
            type="email"
            autocomplete="email"
            class="login-field"
            :error-messages="fieldErrors.email"
            @keyup.enter="submit"
          />

          <v-text-field
            v-model="form.password"
            label="Password"
            :type="showPassword ? 'text' : 'password'"
            variant="outlined"
            autocomplete="current-password"
            class="login-field"
            :error-messages="fieldErrors.password"
            :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
            @click:append-inner="showPassword = !showPassword"
            @keyup.enter="submit"
          />

          <v-btn
            color="primary"
            size="large"
            block
            class="login-btn"
            :loading="submitting"
            @click="submit"
          >
            Log in
          </v-btn>
        </div>

        <div class="login-note">
          <div class="login-note-copy">
            User accounts are created by the administrator. If you need access, please contact your system admin.
          </div>

          <div class="login-note-contacts">
            <a class="login-note-contact" href="mailto:admin@sportsystem.app">
              <v-icon size="18">mdi-email-outline</v-icon>
              admin@sportsystem.app
            </a>
            <a class="login-note-contact" href="tel:+37120000000">
              <v-icon size="18">mdi-phone-outline</v-icon>
              +371 20 000 000
            </a>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { login } from '../services/auth'

const router = useRouter()

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

function resetErrors() {
  fieldErrors.email = []
  fieldErrors.password = []
  generalError.value = ''
}

async function submit() {
  resetErrors()

  if (!form.email) fieldErrors.email = ['Email is required.']
  if (!form.password) fieldErrors.password = ['Password is required.']
  if (fieldErrors.email.length || fieldErrors.password.length) return

  submitting.value = true

  try {
    await login({
      email: form.email,
      password: form.password
    })

    router.push('/home')
  } catch (error) {
    const errors = error?.response?.data?.errors ?? {}
    fieldErrors.email = errors.email ?? []
    fieldErrors.password = errors.password ?? []
    generalError.value = error?.response?.data?.message || 'Login failed.'
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
  padding: 28px;
  background:
    radial-gradient(circle at top left, rgba(31, 111, 235, 0.32), transparent 28%),
    radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.2), transparent 32%),
    linear-gradient(180deg, #d6e6fd 0%, #c7dcfb 46%, #b8d2f5 100%);
}

.login-aurora {
  position: absolute;
  border-radius: 999px;
  filter: blur(100px);
  pointer-events: none;
  opacity: 0.68;
}

.login-aurora-left {
  top: -90px;
  left: -70px;
  width: 320px;
  height: 320px;
  background: rgba(35, 99, 235, 0.28);
}

.login-aurora-right {
  right: -80px;
  bottom: -120px;
  width: 360px;
  height: 360px;
  background: rgba(14, 165, 233, 0.2);
}

.login-grid {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: minmax(320px, 1.05fr) minmax(360px, 0.95fr);
  gap: 24px;
  align-items: stretch;
  max-width: 1220px;
  min-height: calc(100vh - 56px);
  margin: 0 auto;
}

.login-showcase,
.login-panel {
  border-radius: 34px;
  border: 1px solid rgba(255, 255, 255, 0.62);
  background: rgba(248, 251, 255, 0.68);
  backdrop-filter: blur(20px);
  box-shadow: 0 28px 60px rgba(71, 92, 132, 0.16);
}

.login-showcase {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 28px;
  padding: 36px;
  background:
    radial-gradient(circle at top right, rgba(161, 195, 255, 0.42), transparent 28%),
    linear-gradient(160deg, rgba(246, 250, 255, 0.86), rgba(232, 242, 255, 0.74));
}

.showcase-badge {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  width: fit-content;
  min-height: 40px;
  padding: 0 16px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.74);
  border: 1px solid rgba(221, 232, 248, 0.9);
  color: #36557f;
  font-weight: 700;
}

.showcase-copy {
  max-width: 620px;
}

.showcase-eyebrow {
  margin: 0;
  color: #6180ac;
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.showcase-title {
  margin: 18px 0 0;
  font-size: clamp(2.2rem, 3vw, 3.7rem);
  line-height: 1.04;
  color: #172033;
}

.showcase-text {
  max-width: 560px;
  margin: 18px 0 0;
  color: #60738f;
  font-size: 1.04rem;
  line-height: 1.7;
}

.showcase-highlights {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}

.showcase-card {
  min-height: 132px;
  padding: 18px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(224, 233, 246, 0.92);
}

.showcase-card-label {
  color: #6b82a5;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

.showcase-card-value {
  margin-top: 12px;
  color: #172033;
  font-size: 1rem;
  font-weight: 600;
  line-height: 1.5;
}

.showcase-roles {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.showcase-role {
  min-height: 38px;
  padding: 0 14px;
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  border: 1px solid rgba(209, 223, 244, 0.96);
  background: rgba(255, 255, 255, 0.66);
  color: #405d86;
  font-size: 0.92rem;
  font-weight: 700;
}

.login-panel {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 40px 36px;
}

.login-panel-head {
  margin-bottom: 22px;
}

.login-eyebrow {
  color: #5f7ba6;
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.login-title {
  margin: 14px 0 0;
  color: #172033;
  font-size: clamp(2rem, 2.5vw, 2.9rem);
  line-height: 1.06;
}

.login-subtitle {
  margin: 14px 0 0;
  color: #6b7f9c;
  font-size: 1rem;
  line-height: 1.6;
}

.login-alert {
  margin-bottom: 18px;
  border-radius: 18px;
}

.login-form {
  display: grid;
  gap: 8px;
}

.login-field :deep(.v-field) {
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.86);
  box-shadow: inset 0 0 0 1px rgba(220, 231, 246, 0.96);
}

.login-field :deep(.v-field__field),
.login-field :deep(.v-field__input),
.login-field :deep(.v-field__append-inner),
.login-field :deep(.v-field__prepend-inner) {
  background: transparent;
}

.login-field :deep(.v-field--focused) {
  box-shadow:
    inset 0 0 0 1px rgba(74, 144, 255, 0.78),
    0 0 0 4px rgba(22, 119, 255, 0.08);
}

.login-field :deep(.v-field__outline) {
  --v-field-border-opacity: 0;
}

.login-field :deep(input:-webkit-autofill),
.login-field :deep(input:-webkit-autofill:hover),
.login-field :deep(input:-webkit-autofill:focus),
.login-field :deep(input:-webkit-autofill:active) {
  -webkit-text-fill-color: #172033;
  transition: background-color 9999s ease-out 0s;
  box-shadow: 0 0 0 1000px rgba(255, 255, 255, 0.001) inset;
  caret-color: #172033;
}

.login-field :deep(input:-webkit-autofill) + *,
.login-field :deep(input:-webkit-autofill:hover) + *,
.login-field :deep(input:-webkit-autofill:focus) + * {
  color: #172033;
}

.login-btn {
  min-height: 54px;
  margin-top: 10px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-size: 1rem;
  font-weight: 700;
  box-shadow: 0 18px 34px rgba(22, 119, 255, 0.26);
}

.login-note {
  margin-top: 24px;
  padding: 18px 20px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.56);
  border: 1px solid rgba(223, 231, 243, 0.92);
  color: #5f718c;
  line-height: 1.7;
}

.login-note-copy {
  margin: 0;
}

.login-note-contacts {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 14px;
}

.login-note-contact {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 38px;
  padding: 0 12px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.72);
  border: 1px solid rgba(214, 226, 243, 0.96);
  color: #44648f;
  font-size: 0.92rem;
  font-weight: 600;
  text-decoration: none;
}

@media (max-width: 1100px) {
  .login-page {
    padding: 20px;
  }

  .login-grid {
    grid-template-columns: 1fr;
    min-height: auto;
  }

  .login-showcase {
    gap: 24px;
  }
}

@media (max-width: 760px) {
  .login-page {
    padding: 14px;
  }

  .login-showcase,
  .login-panel {
    padding: 24px 20px;
    border-radius: 28px;
  }

  .showcase-highlights {
    grid-template-columns: 1fr;
  }

}

@media (max-width: 420px) {
  .login-title {
    font-size: 1.9rem;
  }

  .showcase-title {
    font-size: 2rem;
  }

  .login-showcase,
  .login-panel {
    padding: 20px 16px;
  }
}
</style>
