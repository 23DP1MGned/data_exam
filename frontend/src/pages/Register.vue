<template>
  <div class="login-bg">
    <v-container fluid class="fill-height d-flex align-center justify-center">
      <v-card width="460" class="login-card pa-8">
        <h1 class="login-title">{{ t('register.title') }}</h1>
        <p class="login-subtitle">{{ t('register.subtitle') }}</p>

        <v-alert
          v-if="generalError"
          type="error"
          variant="tonal"
          density="comfortable"
          class="mb-4"
        >
          {{ generalError }}
        </v-alert>

        <div class="register-grid">
          <v-text-field
            v-model="form.name"
            :label="t('pages.adminUsers.name')"
            variant="outlined"
            :error-messages="fieldErrors.name"
          />

          <v-text-field
            v-model="form.surname"
            :label="t('pages.adminUsers.surname')"
            variant="outlined"
            :error-messages="fieldErrors.surname"
          />
        </div>

        <v-select
          v-model="form.role"
          :label="t('pages.adminUsers.roleLabel')"
          :items="roleOptions"
          item-title="label"
          item-value="value"
          variant="outlined"
          :menu-props="roleMenuProps"
          :error-messages="fieldErrors.role"
        />

        <v-text-field
          v-model="form.email"
          :label="t('login.email')"
          type="email"
          variant="outlined"
          :error-messages="fieldErrors.email"
        />

        <v-text-field
          v-model="form.password"
          :label="t('login.password')"
          :type="showPassword ? 'text' : 'password'"
          variant="outlined"
          :error-messages="fieldErrors.password"
          :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
          @click:append-inner="showPassword = !showPassword"
        />

        <v-text-field
          v-model="form.password_confirmation"
          :label="t('register.confirmPassword')"
          :type="showConfirmPassword ? 'text' : 'password'"
          variant="outlined"
          :error-messages="fieldErrors.password_confirmation"
          :append-inner-icon="showConfirmPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
          @click:append-inner="showConfirmPassword = !showConfirmPassword"
        />

        <v-text-field
          v-if="needsPhone"
          v-model="form.phone"
          :label="t('pages.adminUsers.phone')"
          variant="outlined"
          :error-messages="fieldErrors.phone"
        />

        <v-text-field
          v-if="needsBirthDate"
          v-model="form.birth_date"
          :label="t('pages.adminUsers.birthDate')"
          type="date"
          variant="outlined"
          :error-messages="fieldErrors.birth_date"
        />

        <v-text-field
          v-if="form.role === 'coach'"
          v-model="form.specialization"
          :label="t('pages.adminUsers.specialization')"
          variant="outlined"
          :error-messages="fieldErrors.specialization"
        />

        <v-text-field
          v-if="form.role === 'child'"
          v-model="form.personal_code"
          :label="t('pages.adminUsers.personalCode')"
          variant="outlined"
          :error-messages="fieldErrors.personal_code"
        />

        <v-text-field
          v-if="form.role === 'parent'"
          v-model="form.child_identifier"
          :label="t('pages.adminUsers.childIdentifier')"
          variant="outlined"
          :hint="t('register.optionalChildLink')"
          persistent-hint
          :error-messages="fieldErrors.child_identifier"
        />

        <v-btn
          color="primary"
          size="large"
          block
          class="login-btn"
          :loading="submitting"
          @click="submit"
        >
          {{ t('register.createAccount') }} →
        </v-btn>

        <div class="signup">
          {{ t('register.alreadyHaveAccount') }}
          <router-link to="/login">{{ t('register.loginLink') }}</router-link>
        </div>
      </v-card>
    </v-container>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useLocale } from '../i18n'
import { register } from '../services/auth'

const router = useRouter()
const { t } = useLocale()

const roleOptions = computed(() => [
  { label: t('pages.adminUsers.adminRole'), value: 'admin' },
  { label: t('pages.adminUsers.coachRole'), value: 'coach' },
  { label: t('pages.adminUsers.parentRole'), value: 'parent' },
  { label: t('pages.adminUsers.childRole'), value: 'child' }
])

const roleMenuProps = {
  contentClass: 'app-select-menu',
  theme: 'light'
}

const form = reactive({
  name: '',
  surname: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'parent',
  phone: '',
  birth_date: '',
  specialization: '',
  personal_code: '',
  child_identifier: ''
})

const fieldErrors = reactive({
  name: [],
  surname: [],
  email: [],
  password: [],
  password_confirmation: [],
  role: [],
  phone: [],
  birth_date: [],
  specialization: [],
  personal_code: [],
  child_identifier: []
})

const generalError = ref('')
const submitting = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const needsPhone = computed(() => ['parent', 'coach'].includes(form.role))
const needsBirthDate = computed(() => ['parent', 'child', 'coach'].includes(form.role))

watch(() => form.role, () => {
  form.phone = ''
  form.birth_date = ''
  form.specialization = ''
  form.personal_code = ''
  form.child_identifier = ''
})

function resetErrors() {
  Object.keys(fieldErrors).forEach((key) => {
    fieldErrors[key] = []
  })
  generalError.value = ''
}

function validateForm() {
  resetErrors()

  if (!form.name) fieldErrors.name = [t('register.nameRequired')]
  if (!form.surname) fieldErrors.surname = [t('register.surnameRequired')]
  if (!form.email) fieldErrors.email = [t('login.requiredEmail')]
  if (!form.password) fieldErrors.password = [t('login.requiredPassword')]
  if (!form.password_confirmation) fieldErrors.password_confirmation = [t('register.confirmPasswordRequired')]
  if (form.password && form.password_confirmation && form.password !== form.password_confirmation) {
    fieldErrors.password_confirmation = [t('register.passwordMismatch')]
  }
  if (form.role === 'child' && !form.personal_code) {
    fieldErrors.personal_code = [t('register.childCodeRequired')]
  }

  return Object.values(fieldErrors).every((messages) => messages.length === 0)
}

async function submit() {
  if (!validateForm()) return

  submitting.value = true

  try {
    await register({
      ...form
    })

    router.push('/home')
  } catch (error) {
    const errors = error?.response?.data?.errors ?? {}

    Object.keys(fieldErrors).forEach((key) => {
      fieldErrors[key] = errors[key] ?? []
    })

    generalError.value = error?.response?.data?.message || t('register.failed')
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.login-bg{
height:100vh;
width:100%;
position:relative;
overflow:auto;
background:
linear-gradient(160deg, #bfdcfb 0%, #b4d4f8 48%, #a4c7f0 100%);
}

.login-bg::before,
.login-bg::after{
content:"";
position:absolute;
width:600px;
height:600px;
border-radius:50%;
filter:blur(120px);
opacity:0.6;
animation:liquidMove 18s infinite alternate ease-in-out;
}

.login-bg::before{
background:rgba(29, 78, 216, 0.34);
top:-140px;
left:-140px;
}

.login-bg::after{
background:rgba(14, 165, 233, 0.3);
bottom:-140px;
right:-140px;
animation-duration:22s;
}

@keyframes liquidMove{
0%{
transform:translate(0,0) scale(1);
}
100%{
transform:translate(120px,80px) scale(1.2);
}
}

.login-card{
margin: 32px 0;
border-radius:18px;
background:rgba(255,255,255,0.74);
backdrop-filter:blur(20px);
border:1px solid rgba(255,255,255,0.55);
box-shadow:0 30px 60px rgba(81, 97, 128, 0.18);
}

.login-title{
font-size:40px;
font-weight:700;
margin-bottom:4px;
}

.login-subtitle{
color:#64748b;
margin-bottom:10px;
}

.register-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

.login-btn{
height:48px;
font-size:16px;
border-radius:10px;
margin-top:10px;
transition:all .25s;
}

.login-btn:hover{
transform:translateY(-2px);
box-shadow:0 12px 25px rgba(0,0,0,0.15);
}

.signup{
margin-top:20px;
font-size:14px;
color:#475569;
text-align:center;
}

.signup a{
font-weight:600;
text-decoration:none;
color:#1d4ed8;
}

@media (max-width: 600px) {
  .register-grid {
    grid-template-columns: 1fr;
  }
}
</style>
