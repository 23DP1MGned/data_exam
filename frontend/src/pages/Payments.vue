<template>
  <v-app>
    <v-main class="payments-page">
      <div class="payments-shell" :class="{ 'payments-shell-dark': darkMode }">
        <v-navigation-drawer
          v-if="isCompactNav && mobileMenuOpen"
          v-model="mobileMenuOpen"
          temporary
          location="left"
          width="286"
          class="mobile-drawer"
          :class="{ 'mobile-drawer-dark': darkMode }"
        >
          <div class="mobile-drawer-inner">
            <div class="brand-block mobile-drawer-brand">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">Coach workspace</div>
              </div>
            </div>

            <nav class="mobile-nav-list">
              <v-btn
                v-for="item in navItems"
                :key="`mobile-${item.label}`"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/payments' }"
                block
                @click="mobileMenuOpen = false"
              >
                <template #prepend>
                  <v-icon>{{ item.icon }}</v-icon>
                </template>
                {{ item.label }}
              </v-btn>
            </nav>

            <div class="mobile-drawer-profile">
              <v-avatar size="44">
                <img :src="avatarFor('maksims-richards', 'Maksims Richards')" alt="Coach profile">
              </v-avatar>
              <div>
                <div class="profile-name">Maksims Richards</div>
                <div class="profile-email">maksims@sportsystem.app</div>
              </div>
            </div>
          </div>
        </v-navigation-drawer>

        <div class="payments-panel">
          <aside class="sidebar-card">
            <div class="brand-block">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">Coach workspace</div>
              </div>
            </div>

            <nav class="nav-list">
              <v-btn
                v-for="item in navItems"
                :key="item.label"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/payments' }"
                block
              >
                <template #prepend>
                  <v-icon>{{ item.icon }}</v-icon>
                </template>
                {{ item.label }}
              </v-btn>
            </nav>
          </aside>

          <section class="content-shell">
            <div class="mobile-header-card">
              <button type="button" class="mobile-menu-btn" @click="mobileMenuOpen = true">
                <v-icon size="24">mdi-menu</v-icon>
              </button>

              <div class="mobile-brand-inline">
                <div class="brand-icon mobile-brand-icon">
                  <v-icon color="white">mdi-school-outline</v-icon>
                </div>
                <div class="mobile-brand-copy">
                  <div class="brand-name">SportSystem</div>
                  <div class="brand-caption">Payments</div>
                </div>
              </div>

              <div class="mobile-header-actions">
                <v-btn
                  icon
                  variant="text"
                  class="top-icon-btn mobile-theme-btn"
                  :class="{ 'top-icon-btn-active': darkMode }"
                  @click="darkMode = !darkMode"
                >
                  <v-icon>{{ darkMode ? 'mdi-weather-night' : 'mdi-white-balance-sunny' }}</v-icon>
                </v-btn>

                <div class="icon-badge-wrap">
                  <v-btn icon variant="text" class="top-icon-btn" @click="notificationsDialog = true">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-btn>
                  <span class="icon-badge">6</span>
                </div>
              </div>
            </div>

            <div class="mobile-utility-card">
              <div class="mobile-profile-row">
                <div class="profile-pill mobile-profile-pill">
                  <v-avatar size="42">
                    <img :src="avatarFor('maksims-richards', 'Maksims Richards')" alt="Coach profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">Maksims Richards</div>
                    <div class="profile-email">maksims@sportsystem.app</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="topbar-card">
              <div class="search-wrap">
                <div class="search-shell">
                  <v-icon size="20" class="search-shell-icon">mdi-magnify</v-icon>
                  <v-text-field
                    v-model="search"
                    placeholder="Search payments"
                    variant="plain"
                    hide-details
                    density="comfortable"
                    class="search-field"
                  />
                </div>
              </div>

              <div class="topbar-tools">
                <div class="icon-badge-wrap">
                  <v-btn
                    icon
                    variant="text"
                    class="top-icon-btn"
                    :class="{ 'top-icon-btn-active': darkMode }"
                    @click="darkMode = !darkMode"
                  >
                    <v-icon>{{ darkMode ? 'mdi-weather-night' : 'mdi-white-balance-sunny' }}</v-icon>
                  </v-btn>
                </div>

                <div class="icon-badge-wrap">
                  <v-btn icon variant="text" class="top-icon-btn" @click="notificationsDialog = true">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-btn>
                  <span class="icon-badge">6</span>
                </div>

                <div class="profile-pill">
                  <v-avatar size="48">
                    <img :src="avatarFor('maksims-richards', 'Maksims Richards')" alt="Coach profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">Maksims Richards</div>
                    <div class="profile-email">maksims@sportsystem.app</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="payments-card">
              <div class="payments-header">
                <div>
                  <h1 class="payments-title">Payments</h1>
                  <div class="payments-subtitle">Overview of balances, recent activity and spending breakdown</div>
                </div>
              </div>

              <div class="summary-grid">
                <article v-for="item in summaryCards" :key="item.label" class="summary-card">
                  <div class="summary-label">{{ item.label }}</div>
                  <div class="summary-value">{{ item.value }}</div>
                </article>
              </div>

              <div class="feature-grid">
                <section class="feature-card balance-card">
                  <div class="feature-header">
                    <div>
                      <div class="feature-title">Balance</div>
                      <div class="feature-value">{{ formatCurrency(accountBalance) }}</div>
                    </div>
                  </div>
                  <div class="feature-text">
                    This balance is credited for missed trainings and can be used to pay for other upcoming sessions.
                  </div>
                </section>

                <section class="feature-card">
                  <div class="feature-title">Payment chart</div>
                  <div class="chart-placeholder">
                    <svg
                      class="payment-chart"
                      viewBox="0 0 360 180"
                      preserveAspectRatio="none"
                      aria-label="Payment chart"
                    >
                      <defs>
                        <linearGradient id="paymentsAreaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                          <stop offset="0%" stop-color="rgba(22, 119, 255, 0.34)" />
                          <stop offset="100%" stop-color="rgba(22, 119, 255, 0.04)" />
                        </linearGradient>
                      </defs>

                      <line
                        v-for="line in chartGridLines"
                        :key="line"
                        x1="0"
                        :y1="line"
                        x2="360"
                        :y2="line"
                        class="chart-grid-line"
                      />

                      <path :d="chartAreaPath" class="chart-area" />
                      <path :d="chartLinePath" class="chart-line" />

                      <circle
                        v-for="point in chartPoints"
                        :key="point.label"
                        :cx="point.x"
                        :cy="point.y"
                        r="4.5"
                        class="chart-point"
                      />
                    </svg>

                    <div class="chart-labels">
                      <span v-for="point in chartPoints" :key="`${point.label}-label`">{{ point.label }}</span>
                    </div>
                  </div>
                </section>
              </div>

              <section class="list-card due-payments-card">
                <div class="list-header">
                  <div class="list-title">Trainings to pay</div>
                  <button type="button" class="show-all-btn" @click="dueTrainingsDialog = true">
                    Show all
                  </button>
                </div>

                <div class="list-wrap">
                  <article
                    v-for="item in previewDueTrainings"
                    :key="`due-${item.id}`"
                    class="payment-item payment-item-action"
                  >
                    <div class="payment-main">
                      <div class="payment-name">{{ item.name }}</div>
                      <div class="payment-meta">{{ item.date }} · {{ item.group }}</div>
                      <div class="payment-secondary">Coach: {{ item.trainer }}</div>
                      <div class="payment-deadline">Payment deadline: {{ item.deadline }}</div>
                    </div>

                    <div class="payment-side payment-action-side">
                      <div class="payment-status-row">
                        <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                        <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                          {{ item.status }}
                        </v-chip>
                      </div>
                      <v-btn color="primary" class="pay-btn" icon @click="openPayDialog(item)">
                        <v-icon>mdi-credit-card-outline</v-icon>
                      </v-btn>
                    </div>
                  </article>
                </div>
              </section>

              <div class="lists-grid">
                <section class="list-card">
                  <div class="list-header">
                    <div class="list-title">Recent Activity</div>
                    <button type="button" class="show-all-btn" @click="transactionsDialog = true">
                      Show all
                    </button>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="item in previewRecentActivity"
                      :key="item.id"
                      class="payment-item"
                    >
                      <div class="payment-main">
                        <div class="payment-name">{{ item.name }}</div>
                        <div class="payment-meta">
                          {{ item.date }}
                          <span v-if="item.method"> · {{ item.method }}</span>
                        </div>
                        <div v-if="item.detail" class="payment-secondary">{{ item.detail }}</div>
                      </div>

                      <div class="payment-side">
                        <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                        <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                          {{ item.status }}
                        </v-chip>
                      </div>
                    </article>
                  </div>
                </section>

                <section class="list-card">
                  <div class="list-header">
                    <div class="list-title">Spending Breakdown</div>
                    <button type="button" class="show-all-btn" @click="breakdownDialog = true">
                      Show all
                    </button>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="item in spendingBreakdown"
                      :key="item.category"
                      class="payment-item breakdown-item"
                    >
                      <div class="payment-main">
                        <div class="payment-name">{{ item.category }}</div>
                        <div class="payment-meta">{{ item.percentage }} of total spending</div>
                      </div>

                      <div class="payment-side">
                        <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                      </div>
                    </article>
                  </div>
                </section>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="transactionsDialog" max-width="720">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Recent Activity</div>
                <div class="list-dialog-subtitle">Unified timeline of completed trainings and payment actions.</div>
              </div>

              <v-btn icon variant="text" @click="transactionsDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article
                v-for="item in recentActivity"
                :key="`dialog-activity-${item.id}`"
                class="payment-item"
              >
                <div class="payment-main">
                  <div class="payment-name">{{ item.name }}</div>
                  <div class="payment-meta">
                    {{ item.date }}
                    <span v-if="item.method"> · {{ item.method }}</span>
                  </div>
                  <div v-if="item.detail" class="payment-secondary">{{ item.detail }}</div>
                </div>

                <div class="payment-side">
                  <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                  <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                    {{ item.status }}
                  </v-chip>
                </div>
              </article>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dueTrainingsDialog" max-width="760">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">Trainings To Pay</div>
                <div class="list-dialog-subtitle">Pay upcoming trainings with your card or available account balance.</div>
              </div>

              <v-btn icon variant="text" @click="dueTrainingsDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article
                v-for="item in filteredDueTrainings"
                :key="`dialog-due-${item.id}`"
                class="payment-item payment-item-action"
              >
                <div class="payment-main">
                  <div class="payment-name">{{ item.name }}</div>
                  <div class="payment-meta">{{ item.date }} · {{ item.group }}</div>
                  <div class="payment-secondary">Coach: {{ item.trainer }}</div>
                  <div class="payment-deadline">Payment deadline: {{ item.deadline }}</div>
                </div>

                <div class="payment-side payment-action-side">
                  <div class="payment-status-row">
                    <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                    <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                      {{ item.status }}
                    </v-chip>
                  </div>
                  <v-btn color="primary" class="pay-btn" icon @click="openPayDialog(item)">
                    <v-icon>mdi-credit-card-outline</v-icon>
                  </v-btn>
                </div>
              </article>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="breakdownDialog" max-width="720">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Spending Breakdown</div>
                <div class="list-dialog-subtitle">Grouped paid spending by category with total amount and share.</div>
              </div>

              <v-btn icon variant="text" @click="breakdownDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article
                v-for="item in spendingBreakdown"
                :key="`dialog-breakdown-${item.category}`"
                class="payment-item breakdown-item"
              >
                <div class="payment-main">
                  <div class="payment-name">{{ item.category }}</div>
                  <div class="payment-meta">{{ item.percentage }} of total spending</div>
                </div>

                <div class="payment-side">
                  <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                </div>
              </article>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="payDialog" max-width="560">
          <v-card class="dialog-card list-dialog-card pay-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">Pay Training</div>
                <div class="list-dialog-subtitle">Choose how you want to pay for this training.</div>
              </div>

              <v-btn icon variant="text" @click="payDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div v-if="selectedDueTraining" class="pay-dialog-body">
              <div class="pay-training-card">
                <div class="payment-name">{{ selectedDueTraining.name }}</div>
                <div class="payment-meta">{{ selectedDueTraining.date }} · {{ selectedDueTraining.group }}</div>
                <div class="payment-secondary">Coach: {{ selectedDueTraining.trainer }}</div>
                <div class="payment-deadline">Payment deadline: {{ selectedDueTraining.deadline }}</div>
                <div class="pay-training-amount">{{ formatCurrency(selectedDueTraining.amount) }}</div>
              </div>

              <div class="pay-methods-title">Payment method</div>
              <div class="method-options">
                <button
                  type="button"
                  class="method-option"
                  :class="{ 'method-option-active': selectedPaymentMethod === 'Card' }"
                  @click="selectedPaymentMethod = 'Card'"
                >
                  <span class="method-option-label">Card</span>
                  <span class="method-option-meta">Fast one-time payment</span>
                </button>

                <button
                  type="button"
                  class="method-option"
                  :class="{
                    'method-option-active': selectedPaymentMethod === 'Account balance',
                    'method-option-disabled': selectedDueTraining.amount > accountBalance
                  }"
                  :disabled="selectedDueTraining.amount > accountBalance"
                  @click="selectedPaymentMethod = 'Account balance'"
                >
                  <span class="method-option-label">Account balance</span>
                  <span class="method-option-meta">Available: {{ formatCurrency(accountBalance) }}</span>
                </button>
              </div>

              <div class="pay-dialog-actions">
                <v-btn variant="outlined" class="dialog-secondary-btn" @click="payDialog = false">
                  Cancel
                </v-btn>
                <v-btn color="primary" class="dialog-primary-btn" @click="confirmPayment">
                  Pay now
                </v-btn>
              </div>
            </div>
          </v-card>
        </v-dialog>

        <AppNotificationsDialog v-model="notificationsDialog" :dark-mode="darkMode" />

      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import AppNotificationsDialog from '../components/AppNotificationsDialog.vue'
import { createAvatarDataUri } from '../utils/avatar'

const search = ref('')
const darkMode = ref(false)
const transactionsDialog = ref(false)
const breakdownDialog = ref(false)
const dueTrainingsDialog = ref(false)
const payDialog = ref(false)
const notificationsDialog = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const selectedDueTrainingId = ref(null)
const selectedPaymentMethod = ref('Card')
const accountBalance = ref(1560)
const darkModeStorageKey = 'app-dark-mode'
const avatarFor = (seed, label = seed) => createAvatarDataUri(seed, label)

const navItems = [
  { label: 'Home', icon: 'mdi-home-outline', to: '/home' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline', to: '/attendance' },
  { label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }
]

const dueTrainings = ref([
  {
    id: 1,
    name: 'Football Training',
    date: '26 Mar',
    deadline: '25 Mar',
    category: 'Football',
    group: 'Football U14',
    trainer: 'Kristaps Bērziņš',
    amount: 40,
    status: 'Pending'
  },
  {
    id: 2,
    name: 'Swimming Session',
    date: '28 Mar',
    deadline: '27 Mar',
    category: 'Swimming',
    group: 'Swimming Beginners',
    trainer: 'Laura Ozola',
    amount: 35,
    status: 'Pending'
  },
  {
    id: 3,
    name: 'Boxing Practice',
    date: '29 Mar',
    deadline: '28 Mar',
    category: 'Boxing',
    group: 'Junior Boxing',
    trainer: 'Mārtiņš Liepa',
    amount: 50,
    status: 'Overdue'
  },
  {
    id: 4,
    name: 'Running Club',
    date: '31 Mar',
    deadline: '30 Mar',
    category: 'Running',
    group: 'Running Club',
    trainer: 'Elīna Kalniņa',
    amount: 30,
    status: 'Pending'
  },
  {
    id: 5,
    name: 'Basketball Practice',
    date: '2 Apr',
    deadline: '1 Apr',
    category: 'Basketball',
    group: 'Basketball Teens',
    trainer: 'Artūrs Jansons',
    amount: 45,
    status: 'Pending'
  }
])

const chartPoints = [
  { label: 'Jan', x: 24, y: 122 },
  { label: 'Feb', x: 72, y: 102 },
  { label: 'Mar', x: 120, y: 110 },
  { label: 'Apr', x: 168, y: 78 },
  { label: 'May', x: 216, y: 88 },
  { label: 'Jun', x: 264, y: 56 },
  { label: 'Jul', x: 312, y: 70 }
]

const chartGridLines = [32, 72, 112, 152]

const chartLinePath = computed(() =>
  chartPoints.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`).join(' ')
)

const chartAreaPath = computed(() => {
  const line = chartLinePath.value
  const lastPoint = chartPoints[chartPoints.length - 1]
  const firstPoint = chartPoints[0]

  return `${line} L ${lastPoint.x} 160 L ${firstPoint.x} 160 Z`
})

const transactions = ref([
  { id: 1, name: 'Football Training', date: '12 Mar', amount: 40, status: 'Paid', category: 'Football', detail: 'Completed training event' },
  { id: 2, name: 'Basketball Practice', date: '10 Mar', amount: 35, status: 'Missed', category: 'Basketball', detail: 'Completed training event' },
  { id: 3, name: 'Swimming Session', date: '8 Mar', amount: 50, status: 'Paid', category: 'Swimming', detail: 'Completed training event' },
  { id: 4, name: 'Boxing Session', date: '6 Mar', amount: 45, status: 'Paid', category: 'Boxing', detail: 'Completed training event' },
  { id: 5, name: 'Yoga Class', date: '4 Mar', amount: 30, status: 'Missed', category: 'Yoga', detail: 'Completed training event' },
  { id: 6, name: 'Tennis Practice', date: '2 Mar', amount: 55, status: 'Paid', category: 'Tennis', detail: 'Completed training event' },
  { id: 7, name: 'Dance Training', date: '28 Feb', amount: 40, status: 'Paid', category: 'Dance', detail: 'Completed training event' },
  { id: 8, name: 'Athletics Session', date: '26 Feb', amount: 35, status: 'Missed', category: 'Athletics', detail: 'Completed training event' }
])

const payments = ref([
  { id: 1, name: 'Football Training', date: '12 Mar', method: 'Card', amount: 40, status: 'Paid', category: 'Football', detail: 'Payment action' },
  { id: 2, name: 'Swimming Session', date: '11 Mar', method: 'Account balance', amount: 35, status: 'Returned', category: 'Swimming', detail: 'Payment action' },
  { id: 3, name: 'Boxing Session', date: '8 Mar', method: 'Card', amount: 50, status: 'Paid', category: 'Boxing', detail: 'Payment action' },
  { id: 4, name: 'Running Club', date: '5 Mar', method: 'Account balance', amount: 25, status: 'Returned', category: 'Running', detail: 'Payment action' },
  { id: 5, name: 'Basketball Practice', date: '3 Mar', method: 'Card', amount: 60, status: 'Paid', category: 'Basketball', detail: 'Payment action' },
  { id: 6, name: 'Tennis Practice', date: '1 Mar', method: 'Card', amount: 45, status: 'Paid', category: 'Tennis', detail: 'Payment action' }
])

const normalizedSearch = computed(() => search.value.trim().toLowerCase())
const totalPaid = computed(() =>
  payments.value
    .filter((item) => item.status === 'Paid')
    .reduce((sum, item) => sum + item.amount, 0)
)

const pendingTotal = computed(() =>
  dueTrainings.value
    .filter((item) => item.status === 'Pending')
    .reduce((sum, item) => sum + item.amount, 0)
)

const overdueTotal = computed(() =>
  dueTrainings.value
    .filter((item) => item.status === 'Overdue')
    .reduce((sum, item) => sum + item.amount, 0)
)

const summaryCards = computed(() => [
  { label: 'Total Paid', value: formatCurrency(totalPaid.value) },
  { label: 'Pending', value: formatCurrency(pendingTotal.value) },
  { label: 'Overdue', value: formatCurrency(overdueTotal.value) }
])

const recentActivity = computed(() =>
  [...transactions.value, ...payments.value]
    .map((item) => ({
      id: `${item.method ? 'payment' : 'training'}-${item.id}`,
      name: item.name,
      date: item.date,
      amount: item.amount,
      method: item.method ?? '',
      status: item.status,
      detail: item.detail ?? '',
      sortValue: parseShortDate(item.date).getTime()
    }))
    .sort((a, b) => b.sortValue - a.sortValue)
)

const filteredRecentActivity = computed(() => {
  if (!normalizedSearch.value) return recentActivity.value

  return recentActivity.value.filter((item) =>
    [item.name, item.date, item.method, item.status, item.detail]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(normalizedSearch.value))
  )
})

const filteredDueTrainings = computed(() => {
  if (!normalizedSearch.value) return dueTrainings.value

  return dueTrainings.value.filter((item) =>
    [item.name, item.date, item.group, item.trainer, item.status].some((value) =>
      value.toLowerCase().includes(normalizedSearch.value)
    )
  )
})

const selectedDueTraining = computed(() =>
  dueTrainings.value.find((item) => item.id === selectedDueTrainingId.value) ?? null
)

const previewDueTrainings = computed(() => filteredDueTrainings.value.slice(0, 3))
const previewRecentActivity = computed(() => filteredRecentActivity.value.slice(0, 3))
const spendingBreakdown = computed(() => {
  const paidPayments = payments.value.filter((item) => item.status === 'Paid')
  const total = paidPayments.reduce((sum, item) => sum + item.amount, 0)
  const grouped = paidPayments.reduce((acc, item) => {
    acc[item.category] = (acc[item.category] || 0) + item.amount
    return acc
  }, {})

  return Object.entries(grouped)
    .map(([category, amount]) => ({
      category,
      amount,
      percentage: total ? `${Math.round((amount / total) * 100)}%` : '0%'
    }))
    .sort((a, b) => b.amount - a.amount)
})

const getStatusColor = (status) => {
  if (status === 'Paid') return 'green'
  if (status === 'Missed') return 'red'
  if (status === 'Returned') return 'blue-grey'
  if (status === 'Pending') return 'orange'
  if (status === 'Overdue') return 'red'

  return 'grey'
}

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
})

watch(darkMode, (value) => {
  localStorage.setItem(darkModeStorageKey, String(value))
})

watch(isCompactNav, (value) => {
  if (!value) mobileMenuOpen.value = false
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function formatCurrency(amount) {
  return `€${amount}`
}

function openPayDialog(training) {
  selectedDueTrainingId.value = training.id
  selectedPaymentMethod.value = training.amount <= accountBalance.value ? 'Account balance' : 'Card'
  payDialog.value = true
}

function confirmPayment() {
  const training = selectedDueTraining.value

  if (!training) return
  if (selectedPaymentMethod.value === 'Account balance' && training.amount > accountBalance.value) return

  if (selectedPaymentMethod.value === 'Account balance') {
    accountBalance.value -= training.amount
  }

  const nextId = Date.now()
  transactions.value.unshift({
    id: nextId,
    name: training.name,
    date: training.date,
    amount: training.amount,
    status: 'Paid'
  })

  payments.value.unshift({
    id: nextId + 1,
    name: training.name,
    date: formatToday(),
    method: selectedPaymentMethod.value,
    amount: training.amount,
    status: 'Paid',
    category: training.category,
    detail: 'Payment action'
  })

  dueTrainings.value = dueTrainings.value.filter((item) => item.id !== training.id)
  selectedDueTrainingId.value = null
  payDialog.value = false
}

function parseShortDate(value) {
  return new Date(`${value} 2026`)
}

function formatToday() {
  return new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'short' }).replace(',', '')
}
</script>

<style scoped>
.payments-page {
  padding: 24px;
}

.payments-shell {
  position: relative;
  max-width: 1440px;
  margin: 0 auto;
  border-radius: 34px;
  overflow: visible;
  border: 1px solid rgba(255, 255, 255, 0.45);
  background: linear-gradient(135deg, rgba(236, 244, 255, 0.82), rgba(245, 237, 248, 0.78));
  box-shadow: 0 24px 70px rgba(95, 122, 168, 0.18);
  backdrop-filter: blur(18px);
}

.payments-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.payments-panel {
  display: grid;
  grid-template-columns: 232px minmax(0, 1fr);
  gap: 22px;
  padding: 22px;
}

.mobile-header-card,
.mobile-utility-card {
  display: none;
}

.mobile-drawer-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 18px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.98), rgba(238, 245, 255, 0.96));
}

.mobile-drawer :deep(.v-navigation-drawer__scrim),
.mobile-drawer :deep(.v-navigation-drawer__content),
.mobile-drawer :deep(.v-navigation-drawer__prepend),
.mobile-drawer :deep(.v-navigation-drawer__append) {
  background: transparent;
}

.mobile-drawer-dark {
  background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(22, 31, 48, 0.98)) !important;
  color: #eef4ff;
}

.mobile-drawer-dark :deep(.v-navigation-drawer__content),
.mobile-drawer-dark .mobile-drawer-inner {
  background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(22, 31, 48, 0.98));
}

.mobile-drawer-brand {
  margin-bottom: 20px;
}

.mobile-nav-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.mobile-drawer-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: auto;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
}

.payments-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.sidebar-card {
  display: flex;
  flex-direction: column;
  min-height: 100%;
  padding: 18px;
  border-radius: 28px;
  background: rgba(245, 250, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.payments-shell-dark .sidebar-card {
  background: rgba(18, 27, 43, 0.88);
  border-color: rgba(74, 92, 126, 0.42);
}

.brand-block {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 26px;
  padding: 10px 8px;
  min-width: 0;
}

.brand-icon {
  display: grid;
  place-items: center;
  width: 54px;
  height: 54px;
  flex-shrink: 0;
  border-radius: 18px;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 16px 30px rgba(22, 119, 255, 0.28);
}

.brand-text {
  min-width: 0;
  flex: 1;
}

.brand-name {
  font-size: 1.22rem;
  font-weight: 700;
  color: #172033;
  line-height: 1.1;
}

.payments-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.payments-shell-dark .brand-caption {
  color: #94a6c4;
}

.nav-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.nav-item {
  justify-content: flex-start;
  min-height: 58px;
  border-radius: 18px;
  color: #172033;
  text-transform: none;
  letter-spacing: 0;
  font-size: 1rem;
  font-weight: 500;
}

.payments-shell-dark .nav-item {
  color: #d8e2f2;
}

:deep(.nav-item .v-btn__content) {
  justify-content: flex-start;
}

.nav-item-active {
  color: white;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 16px 34px rgba(22, 119, 255, 0.28);
}

.content-shell {
  display: flex;
  flex-direction: column;
  gap: 22px;
  min-width: 0;
}

.mobile-header-card {
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.payments-shell-dark .mobile-header-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-menu-btn {
  display: grid;
  place-items: center;
  width: 46px;
  height: 46px;
  padding: 0;
  border: 1px solid rgba(223, 231, 243, 0.92);
  border-radius: 14px;
  color: #111827;
  background: rgba(255, 255, 255, 0.92);
  cursor: pointer;
  flex-shrink: 0;
}

.payments-shell-dark .mobile-menu-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.mobile-brand-inline {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  flex: 1;
}

.mobile-brand-icon {
  width: 44px;
  height: 44px;
  border-radius: 14px;
}

.mobile-brand-copy {
  min-width: 0;
}

.mobile-brand-copy .brand-name,
.mobile-brand-copy .brand-caption {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.mobile-header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.mobile-theme-btn {
  color: #111827;
}

.mobile-utility-card {
  flex-direction: column;
  gap: 0;
  padding: 14px 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.payments-shell-dark .mobile-utility-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-profile-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.mobile-profile-pill {
  flex: 1;
  min-width: 0;
  min-height: 56px;
  padding: 10px 12px;
}

.topbar-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 18px 20px;
  border-radius: 26px;
  background: rgba(255, 255, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.payments-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.search-wrap {
  flex: 1;
  min-width: 0;
  max-width: 420px;
}

.search-shell {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 58px;
  padding: 0 18px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(223, 231, 243, 0.92);
}

.payments-shell-dark .search-shell {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.search-shell-icon {
  color: #6b7280;
  flex-shrink: 0;
}

.payments-shell-dark .search-shell-icon {
  color: #8ea3c7;
}

.search-field {
  flex: 1;
}

:deep(.search-field .v-input__control),
:deep(.search-field .v-field),
:deep(.search-field .v-field__field) {
  min-height: auto;
  background: transparent;
  box-shadow: none;
}

.search-field :deep(.v-field__input) {
  min-height: 58px;
  padding-top: 0;
  padding-bottom: 0;
  display: flex;
  align-items: center;
}

.search-field :deep(input) {
  color: #172033;
}

.search-field :deep(.v-label),
.search-field :deep(input::placeholder) {
  color: #111827;
  opacity: 1;
}

.payments-shell-dark .search-field :deep(input),
.payments-shell-dark .search-field :deep(.v-label),
.payments-shell-dark .search-field :deep(input::placeholder) {
  color: #e7eefb;
}

.topbar-tools {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}

.top-icon-btn {
  width: 54px;
  height: 54px;
  border: 1px solid rgba(148, 163, 184, 0.18);
  background: rgba(255, 255, 255, 0.75);
}

.payments-shell-dark .top-icon-btn {
  color: #dce6f7;
  background: rgba(18, 27, 43, 0.92);
  border-color: rgba(74, 92, 126, 0.46);
}

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
}

.payments-shell-dark .top-icon-btn-active {
  color: #7fbcff;
  background: rgba(22, 43, 76, 0.96);
  border-color: rgba(82, 156, 255, 0.44);
}

.icon-badge-wrap {
  position: relative;
  color: #111827;
}

.icon-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  display: grid;
  place-items: center;
  min-width: 21px;
  height: 21px;
  padding: 0 6px;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 700;
  color: white;
  background: #1677ff;
}

.profile-pill {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
  padding: 10px 16px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.82);
}

.payments-shell-dark .profile-pill {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 600;
  color: #172033;
}

.payments-shell-dark .profile-name {
  color: #f2f7ff;
}

.profile-email {
  font-size: 0.9rem;
  color: #7b8798;
  word-break: break-word;
}

.payments-shell-dark .profile-email {
  color: #93a5c3;
}

.payments-card {
  min-width: 0;
  padding: 26px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.payments-shell-dark .payments-card {
  background: rgba(22, 31, 48, 0.72);
  border-color: rgba(74, 92, 126, 0.42);
}

.payments-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.payments-title {
  margin: 0;
  font-size: 2.3rem;
  line-height: 1.1;
  font-weight: 700;
  color: #121826;
}

.payments-shell-dark .payments-title {
  color: #f3f7ff;
}

.payments-subtitle {
  margin-top: 10px;
  font-size: 1rem;
  color: #66748a;
}

.payments-shell-dark .payments-subtitle {
  color: #8fa3c1;
}

.create-btn {
  min-height: 54px;
  padding: 0 24px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-size: 1rem;
  box-shadow: 0 18px 34px rgba(22, 119, 255, 0.28);
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 18px;
}

.summary-card,
.feature-card,
.list-card {
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.92);
  box-shadow: 0 12px 28px rgba(110, 136, 173, 0.08);
}

.payments-shell-dark .summary-card,
.payments-shell-dark .feature-card,
.payments-shell-dark .list-card {
  background: rgba(18, 27, 43, 0.9);
  border: 1px solid rgba(74, 92, 126, 0.42);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.26);
}

.summary-card {
  padding: 18px 20px;
}

.summary-label {
  font-size: 0.85rem;
  color: #7b8798;
}

.payments-shell-dark .summary-label,
.payments-shell-dark .payment-meta,
.payments-shell-dark .list-dialog-subtitle,
.payments-shell-dark .chart-labels {
  color: #8fa3c1;
}

.summary-value {
  margin-top: 8px;
  font-size: 1.5rem;
  font-weight: 700;
  color: #172033;
}

.payments-shell-dark .summary-value,
.payments-shell-dark .feature-title,
.payments-shell-dark .feature-value,
.payments-shell-dark .list-title,
.payments-shell-dark .payment-name,
.payments-shell-dark .payment-amount,
.payments-shell-dark .list-dialog-title {
  color: #eef4ff;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 18px;
  margin-bottom: 18px;
}

.feature-card {
  padding: 22px;
}

.feature-title {
  font-size: 1rem;
  font-weight: 700;
  color: #172033;
}

.feature-value {
  margin-top: 8px;
  font-size: 2rem;
  font-weight: 800;
  color: #172033;
}

.feature-text {
  margin-top: 10px;
  margin-bottom: 18px;
  color: #66748a;
  line-height: 1.55;
}

.payments-shell-dark .feature-text {
  color: #8fa3c1;
}

.feature-btn {
  min-height: 46px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
}

.chart-placeholder {
  height: 184px;
  padding: 14px 14px 10px;
  border-radius: 20px;
  background: linear-gradient(135deg, #e7eeff, #f8fbff);
  border: 1px solid rgba(255, 255, 255, 0.72);
}

.payments-shell-dark .chart-placeholder {
  background: linear-gradient(135deg, rgba(18, 27, 43, 0.96), rgba(27, 39, 61, 0.92));
  border-color: rgba(74, 92, 126, 0.42);
}

.payment-chart {
  width: 100%;
  height: 148px;
}

.chart-grid-line {
  stroke: rgba(148, 163, 184, 0.18);
  stroke-width: 1;
}

.payments-shell-dark .chart-grid-line {
  stroke: rgba(116, 138, 175, 0.18);
}

.chart-area {
  fill: url(#paymentsAreaGradient);
}

.chart-line {
  fill: none;
  stroke: #1677ff;
  stroke-width: 3;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.chart-point {
  fill: white;
  stroke: #1677ff;
  stroke-width: 3;
}

.chart-labels {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  margin-top: 6px;
  color: #7b8798;
  font-size: 0.78rem;
}

.lists-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 18px;
}

.list-card {
  padding: 20px;
}

.list-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.list-title {
  font-size: 1rem;
  font-weight: 700;
  color: #172033;
}

.show-all-btn {
  border: none;
  background: transparent;
  color: #1677ff;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
}

.list-wrap {
  display: flex;
  flex-direction: column;
}

.due-payments-card {
  margin-bottom: 18px;
}

.payment-item-action {
  align-items: stretch;
}

.payment-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px 0;
}

.payment-item + .payment-item {
  border-top: 1px solid rgba(15, 23, 42, 0.08);
}

.payment-name {
  font-weight: 600;
  color: #172033;
}

.payment-main {
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-width: 0;
}

.payment-meta {
  margin-top: 4px;
  font-size: 0.9rem;
  color: #7b8798;
}

.payment-secondary {
  margin-top: 5px;
  font-size: 0.88rem;
  color: #64748b;
}

.payment-deadline {
  margin-top: 6px;
  font-size: 0.82rem;
  font-weight: 600;
  color: #d46b08;
}

.payment-side {
  text-align: right;
}

.payment-action-side {
  display: flex;
  align-items: flex-end;
  gap: 12px;
  min-width: 170px;
}

.payment-status-row {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
  min-width: 72px;
}

.payment-amount {
  font-weight: 700;
  color: #172033;
}

.payment-chip {
  margin-top: 8px;
}

.pay-btn {
  width: 56px;
  min-width: 56px;
  height: 56px;
  min-height: 56px;
  padding: 0;
  border-radius: 18px;
}

.payments-shell-dark .payment-secondary {
  color: #8fa3c1;
}

.payments-shell-dark .payment-deadline {
  color: #ffbf66;
}

.dialog-card {
  border-radius: 24px;
}

.list-dialog-card {
  padding: 24px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.96), rgba(238, 245, 255, 0.92));
  border: 1px solid rgba(255, 255, 255, 0.72);
  box-shadow: 0 20px 45px rgba(76, 104, 148, 0.18);
}

.list-dialog-card-dark {
  background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(22, 31, 48, 0.96));
  border-color: rgba(74, 92, 126, 0.42);
  box-shadow: 0 24px 54px rgba(4, 10, 24, 0.34);
}

.list-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 18px;
}

.list-dialog-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: #172033;
}

.list-dialog-subtitle {
  margin-top: 8px;
  color: #64748b;
  line-height: 1.5;
}

.pay-dialog-card {
  padding-bottom: 10px;
}

.pay-dialog-body {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.pay-training-card {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 18px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.92);
  box-shadow: 0 12px 28px rgba(110, 136, 173, 0.08);
}

.list-dialog-card-dark .pay-training-card {
  background: rgba(18, 27, 43, 0.9);
  border: 1px solid rgba(74, 92, 126, 0.42);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.26);
}

.pay-training-amount {
  margin-top: 14px;
  font-size: 1.45rem;
  font-weight: 800;
  color: #172033;
}

.list-dialog-card-dark .pay-training-amount,
.list-dialog-card-dark .pay-methods-title {
  color: #eef4ff;
}

.pay-methods-title {
  font-size: 0.98rem;
  font-weight: 700;
  color: #172033;
}

.method-options {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

.method-option {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 6px;
  min-height: 92px;
  padding: 16px;
  border: 1px solid rgba(223, 231, 243, 0.92);
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.92);
  text-align: left;
  cursor: pointer;
  transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}

.method-option-active {
  border-color: rgba(22, 119, 255, 0.34);
  background: rgba(232, 242, 255, 0.96);
  box-shadow: 0 14px 24px rgba(22, 119, 255, 0.14);
}

.method-option-disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.list-dialog-card-dark .method-option {
  border-color: rgba(74, 92, 126, 0.42);
  background: rgba(18, 27, 43, 0.9);
}

.list-dialog-card-dark .method-option-active {
  border-color: rgba(82, 156, 255, 0.44);
  background: rgba(22, 43, 76, 0.96);
  box-shadow: 0 16px 28px rgba(19, 44, 84, 0.32);
}

.method-option-label {
  font-size: 0.96rem;
  font-weight: 700;
  color: #172033;
}

.method-option-meta {
  font-size: 0.86rem;
  color: #64748b;
}

.list-dialog-card-dark .method-option-label {
  color: #eef4ff;
}

.list-dialog-card-dark .method-option-meta {
  color: #8fa3c1;
}

.pay-dialog-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.dialog-secondary-btn,
.dialog-primary-btn {
  min-width: 124px;
  min-height: 46px;
  border-radius: 14px;
  text-transform: none;
  letter-spacing: 0;
  padding: 0 18px;
}

.dialog-list-wrap {
  display: flex;
  flex-direction: column;
  max-height: 420px;
  overflow: auto;
  padding-right: 4px;
  scrollbar-width: thin;
  scrollbar-color: rgba(22, 119, 255, 0.45) rgba(226, 232, 240, 0.5);
}

.dialog-list-wrap::-webkit-scrollbar {
  width: 10px;
}

.dialog-list-wrap::-webkit-scrollbar-track {
  border-radius: 999px;
  background: rgba(226, 232, 240, 0.5);
}

.dialog-list-wrap::-webkit-scrollbar-thumb {
  border: 2px solid rgba(226, 232, 240, 0.5);
  border-radius: 999px;
  background: linear-gradient(180deg, rgba(22, 119, 255, 0.7), rgba(15, 95, 227, 0.55));
}

.dialog-list-wrap::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, rgba(22, 119, 255, 0.85), rgba(15, 95, 227, 0.7));
}

@media (max-width: 1280px) {
  .payments-panel {
    grid-template-columns: 210px minmax(0, 1fr);
  }

  .brand-name {
    font-size: 1.08rem;
  }

  .brand-caption {
    font-size: 0.76rem;
  }
}

@media (max-width: 1024px) {
  .payments-page {
    padding: 16px;
  }

  .payments-shell {
    width: 100%;
    max-width: none;
    overflow: hidden;
    border-radius: 28px;
  }

  .payments-panel {
    grid-template-columns: 1fr;
    padding: 18px;
    gap: 18px;
  }

  .sidebar-card,
  .topbar-card {
    display: none;
  }

  .mobile-header-card,
  .mobile-utility-card {
    display: flex;
  }

  .payments-header {
    flex-direction: column;
    align-items: stretch;
  }

  .payments-card {
    padding: 24px;
  }

  .payment-item {
    align-items: center;
    justify-content: space-between;
    flex-direction: row;
  }

  .payment-side {
    text-align: right;
  }

  .payment-action-side {
    flex-direction: column;
    align-items: flex-end;
    min-width: 120px;
  }

  .summary-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .feature-grid,
  .lists-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .payments-title {
    font-size: 2rem;
  }

  .payments-subtitle {
    font-size: 0.94rem;
  }

  .summary-label,
  .feature-title,
  .list-title,
  .show-all-btn {
    font-size: 0.9rem;
  }

  .summary-value {
    font-size: 1.38rem;
  }

  .feature-value {
    font-size: 1.7rem;
  }

  .feature-text,
  .payment-meta,
  .payment-secondary,
  .payment-deadline,
  .profile-email,
  .chart-labels {
    font-size: 0.84rem;
  }

  .payment-name,
  .profile-name {
    font-size: 0.94rem;
  }

  .payment-amount {
    font-size: 0.94rem;
  }
}

@media (max-width: 768px) {
  .payments-page {
    padding: 12px;
  }

  .payments-shell {
    border-radius: 24px;
  }

  .payments-panel {
    padding: 14px;
    gap: 14px;
  }

  .mobile-header-card,
  .mobile-utility-card,
  .payments-card,
  .summary-card,
  .feature-card,
  .list-card {
    border-radius: 20px;
  }

  .payments-card {
    padding: 18px;
  }

  .payments-title {
    font-size: 1.9rem;
  }

  .payments-subtitle {
    font-size: 0.95rem;
  }

  .summary-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .feature-grid,
  .lists-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .feature-card,
  .list-card {
    padding: 18px;
  }

  .feature-value {
    font-size: 1.52rem;
  }

  .summary-label,
  .feature-title,
  .list-title,
  .show-all-btn {
    font-size: 0.82rem;
  }

  .summary-value {
    font-size: 1.2rem;
  }

  .feature-text,
  .payment-meta,
  .payment-secondary,
  .payment-deadline,
  .profile-email,
  .chart-labels {
    font-size: 0.76rem;
  }

  .payment-name,
  .profile-name {
    font-size: 0.86rem;
  }

  .payment-amount {
    font-size: 0.86rem;
  }

  .chart-placeholder {
    height: 168px;
    padding: 12px 12px 10px;
  }

  .payment-chart {
    height: 132px;
  }

  .chart-labels {
    font-size: 0.72rem;
  }

  .list-header {
    align-items: flex-start;
    flex-direction: column;
  }

  .method-options {
    grid-template-columns: 1fr;
  }

}

@media (max-width: 560px) {
  .payments-panel {
    padding: 12px;
    gap: 12px;
  }

  .mobile-header-card,
  .mobile-utility-card {
    padding: 12px 14px;
  }

  .mobile-menu-btn,
  .top-icon-btn {
    width: 44px;
    height: 44px;
  }

  .mobile-brand-icon {
    width: 40px;
    height: 40px;
    border-radius: 13px;
  }

  .mobile-brand-copy .brand-name {
    font-size: 0.96rem;
  }

  .mobile-brand-copy .brand-caption {
    font-size: 0.72rem;
  }

  .mobile-profile-pill {
    min-height: 52px;
    padding: 8px 10px;
  }

  .brand-name {
    font-size: 1rem;
  }

  .brand-caption {
    font-size: 0.72rem;
  }

  .payment-item {
    align-items: center;
    justify-content: space-between;
    flex-direction: row;
    gap: 10px;
    padding: 12px 0;
  }

  .payment-item-action {
    align-items: stretch;
  }

  .summary-grid {
    grid-template-columns: 1fr;
  }

  .feature-grid,
  .lists-grid {
    grid-template-columns: 1fr;
  }

  .payments-title {
    font-size: 1.48rem;
  }

  .payments-card {
    padding: 16px;
  }

  .feature-card,
  .list-card {
    padding: 16px;
  }

  .summary-card {
    padding: 16px;
  }

  .summary-value {
    font-size: 1.08rem;
  }

  .feature-value {
    font-size: 1.26rem;
  }

  .feature-text,
  .payment-meta,
  .payment-secondary,
  .payment-deadline,
  .profile-email {
    font-size: 0.7rem;
  }

  .payment-name {
    font-size: 0.78rem;
  }

  .payment-meta {
    margin-top: 3px;
  }

  .payment-side {
    min-width: fit-content;
    text-align: right;
  }

  .payment-action-side {
    min-width: 96px;
    gap: 8px;
  }

  .payment-amount {
    font-size: 0.78rem;
  }

  .payment-chip {
    margin-top: 6px;
  }

  .summary-label,
  .feature-title,
  .list-title,
  .show-all-btn,
  .profile-name {
    font-size: 0.74rem;
  }

  .pay-dialog-actions {
    flex-direction: column;
  }

  .dialog-secondary-btn,
  .dialog-primary-btn,
  .pay-btn {
    width: 100%;
    min-width: 0;
  }

  .chart-placeholder {
    height: 156px;
    padding: 10px 10px 8px;
  }

  .payment-chart {
    height: 122px;
  }

  .list-dialog-card {
    padding: 18px;
  }

  .list-dialog-header {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
