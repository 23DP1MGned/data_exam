<template>
  <v-app>
    <v-main class="payments-page">
      <div class="payments-shell">
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
            <div class="topbar-card">
              <div class="search-wrap">
                <div class="search-shell">
                  <v-icon size="20" class="search-shell-icon">mdi-magnify</v-icon>
                  <v-text-field
                    v-model="search"
                    label="Search payments"
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
                  <v-btn icon variant="text" class="top-icon-btn">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-btn>
                  <span class="icon-badge">6</span>
                </div>

                <div class="profile-pill">
                  <v-avatar size="48">
                    <img src="https://i.pravatar.cc/80?img=12" alt="Coach profile">
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
                  <div class="payments-subtitle">Overview of balances, transactions and recent activity</div>
                </div>

                <v-btn color="primary" class="create-btn" prepend-icon="mdi-cog-outline">
                  Manage
                </v-btn>
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
                      <div class="feature-value">€1560</div>
                    </div>
                  </div>
                  <div class="feature-text">
                    This balance is credited for missed trainings and can be used to pay for other upcoming sessions.
                  </div>
                  <v-btn color="primary" class="feature-btn">Manage</v-btn>
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

            <div class="lists-grid">
                <section class="list-card">
                  <div class="list-header">
                    <div class="list-title">Transactions</div>
                    <button type="button" class="show-all-btn" @click="transactionsDialog = true">
                      Show all
                    </button>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="item in previewTransactions"
                      :key="item.id"
                      class="payment-item"
                    >
                      <div>
                        <div class="payment-name">{{ item.name }}</div>
                        <div class="payment-meta">{{ item.date }}</div>
                      </div>

                      <div class="payment-side">
                        <div class="payment-amount">€{{ item.amount }}</div>
                        <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                          {{ item.status }}
                        </v-chip>
                      </div>
                    </article>
                  </div>
                </section>

                <section class="list-card">
                  <div class="list-header">
                    <div class="list-title">Recent Payments</div>
                    <button type="button" class="show-all-btn" @click="paymentsDialog = true">
                      Show all
                    </button>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="item in previewPayments"
                      :key="item.id"
                      class="payment-item"
                    >
                      <div>
                        <div class="payment-name">{{ item.name }}</div>
                        <div class="payment-meta">{{ item.method }}</div>
                      </div>

                      <div class="payment-side">
                        <div class="payment-amount">€{{ item.amount }}</div>
                        <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                          {{ item.status }}
                        </v-chip>
                      </div>
                    </article>
                  </div>
                </section>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="transactionsDialog" max-width="720">
          <v-card class="dialog-card list-dialog-card">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Transactions</div>
                <div class="list-dialog-subtitle">Complete list of payment transactions for your trainings.</div>
              </div>

              <v-btn icon variant="text" @click="transactionsDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article
                v-for="item in transactions"
                :key="`dialog-transaction-${item.id}`"
                class="payment-item"
              >
                <div>
                  <div class="payment-name">{{ item.name }}</div>
                  <div class="payment-meta">{{ item.date }}</div>
                </div>

                <div class="payment-side">
                  <div class="payment-amount">€{{ item.amount }}</div>
                  <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                    {{ item.status }}
                  </v-chip>
                </div>
              </article>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="paymentsDialog" max-width="720">
          <v-card class="dialog-card list-dialog-card">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Recent Payments</div>
                <div class="list-dialog-subtitle">Full list of recent payment methods, amounts and statuses.</div>
              </div>

              <v-btn icon variant="text" @click="paymentsDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article
                v-for="item in payments"
                :key="`dialog-payment-${item.id}`"
                class="payment-item"
              >
                <div>
                  <div class="payment-name">{{ item.name }}</div>
                  <div class="payment-meta">{{ item.method }}</div>
                </div>

                <div class="payment-side">
                  <div class="payment-amount">€{{ item.amount }}</div>
                  <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                    {{ item.status }}
                  </v-chip>
                </div>
              </article>
            </div>
          </v-card>
        </v-dialog>
      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')
const darkMode = ref(false)
const transactionsDialog = ref(false)
const paymentsDialog = ref(false)

const navItems = [
  { label: 'Home', icon: 'mdi-home-outline', to: '/home' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline', to: '/attendance' },
  { label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }
]

const summaryCards = [
  { label: 'Total Paid', value: '€1240' },
  { label: 'Pending', value: '€320' },
  { label: 'Overdue', value: '€90' }
]

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
  { id: 1, name: 'Football Training', date: '12 Mar', amount: 40, status: 'Paid' },
  { id: 2, name: 'Basketball', date: '10 Mar', amount: 35, status: 'Pending' },
  { id: 3, name: 'Swimming', date: '8 Mar', amount: 50, status: 'Overdue' },
  { id: 4, name: 'Boxing Session', date: '6 Mar', amount: 45, status: 'Paid' },
  { id: 5, name: 'Yoga Class', date: '4 Mar', amount: 30, status: 'Paid' },
  { id: 6, name: 'Tennis Practice', date: '2 Mar', amount: 55, status: 'Pending' },
  { id: 7, name: 'Dance Training', date: '28 Feb', amount: 40, status: 'Paid' },
  { id: 8, name: 'Athletics Session', date: '26 Feb', amount: 35, status: 'Overdue' }
])

const payments = ref([
  { id: 1, name: 'John Doe', method: 'Card', amount: 40, status: 'Paid' },
  { id: 2, name: 'Anna Smith', method: 'Account balance', amount: 35, status: 'Returned' },
  { id: 3, name: 'Mark Lee', method: 'Card', amount: 50, status: 'Paid' },
  { id: 4, name: 'Sophie Turner', method: 'Account balance', amount: 25, status: 'Returned' },
  { id: 5, name: 'Daniel Young', method: 'Card', amount: 60, status: 'Paid' },
  { id: 6, name: 'Emily Carter', method: 'Card', amount: 45, status: 'Paid' },
  { id: 7, name: 'Oliver Grant', method: 'Account balance', amount: 30, status: 'Returned' },
  { id: 8, name: 'Mia Johnson', method: 'Card', amount: 55, status: 'Paid' }
])

const normalizedSearch = computed(() => search.value.trim().toLowerCase())

const filteredTransactions = computed(() => {
  if (!normalizedSearch.value) return transactions.value

  return transactions.value.filter((item) =>
    [item.name, item.date, item.status].some((value) =>
      value.toLowerCase().includes(normalizedSearch.value)
    )
  )
})

const filteredPayments = computed(() => {
  if (!normalizedSearch.value) return payments.value

  return payments.value.filter((item) =>
    [item.name, item.method, item.status].some((value) =>
      value.toLowerCase().includes(normalizedSearch.value)
    )
  )
})

const previewTransactions = computed(() => filteredTransactions.value.slice(0, 3))
const previewPayments = computed(() => filteredPayments.value.slice(0, 3))

const getStatusColor = (status) => {
  if (status === 'Paid') return 'green'
  if (status === 'Returned') return 'blue-grey'
  if (status === 'Pending') return 'orange'
  if (status === 'Overdue') return 'red'

  return 'grey'
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

.payments-panel {
  display: grid;
  grid-template-columns: 232px minmax(0, 1fr);
  gap: 22px;
  padding: 22px;
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

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
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

.search-shell-icon {
  color: #6b7280;
  flex-shrink: 0;
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

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
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

.profile-name {
  font-size: 0.98rem;
  font-weight: 600;
  color: #172033;
}

.profile-email {
  font-size: 0.9rem;
  color: #7b8798;
  word-break: break-word;
}

.payments-card {
  min-width: 0;
  padding: 26px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
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

.payments-subtitle {
  margin-top: 10px;
  font-size: 1rem;
  color: #66748a;
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

.summary-card {
  padding: 18px 20px;
}

.summary-label {
  font-size: 0.85rem;
  color: #7b8798;
}

.summary-value {
  margin-top: 8px;
  font-size: 1.5rem;
  font-weight: 700;
  color: #172033;
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

.payment-chart {
  width: 100%;
  height: 148px;
}

.chart-grid-line {
  stroke: rgba(148, 163, 184, 0.18);
  stroke-width: 1;
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

.payment-meta {
  margin-top: 4px;
  font-size: 0.9rem;
  color: #7b8798;
}

.payment-side {
  text-align: right;
}

.payment-amount {
  font-weight: 700;
  color: #172033;
}

.payment-chip {
  margin-top: 8px;
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
  .payments-panel {
    grid-template-columns: 1fr;
  }

  .nav-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .topbar-card,
  .payments-header {
    flex-direction: column;
    align-items: stretch;
  }

  .search-wrap {
    max-width: none;
  }

  .topbar-tools {
    flex-direction: column;
    align-items: stretch;
  }

  .profile-pill {
    width: 100%;
  }

  .summary-grid,
  .feature-grid,
  .lists-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .payments-page {
    padding: 12px;
  }

  .payments-card {
    padding: 18px;
  }

  .payments-title {
    font-size: 1.9rem;
  }

  .nav-list {
    grid-template-columns: 1fr;
  }

  .topbar-tools {
    gap: 10px;
  }

  .icon-badge-wrap {
    flex: 1;
  }

  .top-icon-btn {
    width: 100%;
  }
}

@media (max-width: 560px) {
  .payments-shell {
    border-radius: 24px;
  }

  .payments-panel {
    padding: 14px;
    gap: 14px;
  }

  .sidebar-card,
  .topbar-card,
  .payments-card {
    border-radius: 20px;
  }

  .brand-name {
    font-size: 1rem;
  }

  .brand-caption {
    font-size: 0.72rem;
  }

  .payment-item {
    align-items: flex-start;
    flex-direction: column;
  }

  .payment-side {
    text-align: left;
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
