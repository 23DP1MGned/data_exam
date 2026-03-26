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
                <div class="brand-caption">Parent workspace</div>
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
                <img :src="avatarFor(profileSeed, profileName)" alt="Parent profile">
              </v-avatar>
              <div>
                <div class="profile-name">{{ profileName }}</div>
                <div class="profile-email">{{ profileEmail }}</div>
              </div>
            </div>

            <v-btn
              variant="outlined"
              class="mobile-logout-btn"
              prepend-icon="mdi-logout"
              @click="handleMobileLogout"
            >
              Log out
            </v-btn>
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
                <div class="brand-caption">Parent workspace</div>
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
                  <span class="icon-badge">{{ unreadCount }}</span>
                </div>
              </div>
            </div>

            <div class="mobile-utility-card">
              <div class="mobile-profile-row">
                <div class="profile-pill mobile-profile-pill">
                  <v-avatar size="42">
                    <img :src="avatarFor(profileSeed, profileName)" alt="Parent profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">{{ profileName }}</div>
                    <div class="profile-email">{{ profileEmail }}</div>
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
                  <span class="icon-badge">{{ unreadCount }}</span>
                </div>

                <v-btn
                  icon
                  variant="text"
                  class="top-icon-btn logout-btn"
                  @click="handleLogout"
                >
                  <v-icon>mdi-logout</v-icon>
                </v-btn>

                <div class="profile-pill">
                  <v-avatar size="48">
                    <img :src="avatarFor(profileSeed, profileName)" alt="Parent profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">{{ profileName }}</div>
                    <div class="profile-email">{{ profileEmail }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="payments-card">
              <div class="payments-header">
                <div>
                  <h1 class="payments-title">Payments</h1>
                  <div class="payments-subtitle">Pay for individual trainings or a full month, then review receipts and recent payment activity.</div>
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

                  <div class="balance-source-card">
                    <div class="balance-source-header">
                      <div class="balance-source-title">Balance source</div>
                      <button
                        v-if="balanceSources.length > 3"
                        type="button"
                        class="show-all-btn"
                        @click="balanceSourcesDialog = true"
                      >
                        Show all
                      </button>
                    </div>

                    <div v-if="previewBalanceSources.length" class="balance-source-list">
                      <div
                        v-for="item in previewBalanceSources"
                        :key="item.id"
                        class="balance-source-item"
                      >
                        <div class="balance-source-copy">
                          <div class="balance-source-name">{{ item.label }}</div>
                          <div class="balance-source-meta">{{ item.meta }}</div>
                        </div>
                        <div class="balance-source-amount">+{{ formatCurrency(item.amount) }}</div>
                      </div>
                    </div>

                    <div v-else class="balance-source-empty">
                      No recent credits have been added to the balance yet.
                    </div>
                  </div>
                </section>

                <section class="feature-card">
                  <div class="chart-card-header">
                    <div>
                      <div class="feature-title">Payment chart</div>
                      <div class="feature-text">Paid amounts by month for the selected half-year.</div>
                    </div>

                    <div class="chart-period-switch">
                      <v-btn
                        icon
                        variant="text"
                        class="chart-period-btn"
                        @click="changeChartHalfYear(-1)"
                      >
                        <v-icon>mdi-chevron-left</v-icon>
                      </v-btn>

                      <div class="chart-period-label">{{ chartPeriodLabel }}</div>

                      <v-btn
                        icon
                        variant="text"
                        class="chart-period-btn"
                        :disabled="chartHalfYearOffset === 0"
                        @click="changeChartHalfYear(1)"
                      >
                        <v-icon>mdi-chevron-right</v-icon>
                      </v-btn>
                    </div>
                  </div>

                  <div class="chart-summary-row">
                    <div class="chart-summary-pill">
                      <span>Paid this half-year</span>
                      <strong>{{ formatCurrency(chartPeriodTotal) }}</strong>
                    </div>
                    <div class="chart-summary-pill">
                      <span>Peak month</span>
                      <strong>{{ formatCompactCurrency(chartPeriodPeak) }}</strong>
                    </div>
                  </div>

                  <div class="chart-placeholder">
                    <svg
                      class="payment-chart"
                      viewBox="0 0 360 150"
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
                        :key="point.key"
                        :cx="point.x"
                        :cy="point.y"
                        r="4.5"
                        class="chart-point"
                      >
                        <title>{{ `${point.label}: ${formatCurrency(point.value)}` }}</title>
                      </circle>
                    </svg>

                    <div class="chart-labels">
                      <span v-for="point in chartPoints" :key="`${point.key}-label`">{{ point.label }}</span>
                    </div>
                  </div>
                </section>
              </div>

              <div class="payment-view-switch">
                <div class="payment-view-tabs">
                  <v-btn
                    variant="text"
                    class="view-switch-btn"
                    :class="{ 'view-switch-btn-active': activePaymentTab === 'training' }"
                    @click="activePaymentTab = 'training'"
                  >
                    Pay Per Training
                  </v-btn>
                  <v-btn
                    variant="text"
                    class="view-switch-btn"
                    :class="{ 'view-switch-btn-active': activePaymentTab === 'month' }"
                    @click="activePaymentTab = 'month'"
                  >
                    Pay Per Month
                  </v-btn>
                </div>
              </div>

              <section class="list-card due-payments-card">
                <div class="list-header">
                  <div>
                    <div class="list-title">{{ activePaymentTab === 'training' ? 'Trainings to pay' : 'Months to pay' }}</div>
                    <div class="list-dialog-subtitle">
                      {{
                        activePaymentTab === 'training'
                          ? 'Pay one or a few individual trainings that are currently open for payment.'
                          : 'Pay the full monthly group price before the month starts.'
                      }}
                    </div>
                  </div>

                  <div class="pay-section-actions">
                    <button
                      type="button"
                      class="show-all-btn"
                      @click="activePaymentTab === 'training' ? dueTrainingsDialog = true : monthlyPaymentsDialog = true"
                    >
                      Show all
                    </button>

                    <v-btn
                      v-if="activePaymentTab === 'training'"
                      color="primary"
                      class="dialog-primary-btn pay-selected-btn"
                      :disabled="!selectedSingleDueIds.length"
                      @click="openSinglePayDialog()"
                    >
                      Pay selected
                    </v-btn>
                  </div>
                </div>

                <div v-if="activePaymentTab === 'training' && previewDueTrainings.length" class="list-wrap">
                  <article
                    v-for="item in previewDueTrainings"
                    :key="`due-${item.id}`"
                    class="payment-item payment-item-action"
                    :class="{ 'payment-item-selected': isTrainingSelected(item.id) }"
                  >
                    <div class="payment-main">
                      <label class="training-select-control">
                        <input
                          type="checkbox"
                          :checked="isTrainingSelected(item.id)"
                          @change="toggleTrainingSelection(item)"
                        >
                        <span>Select</span>
                      </label>
                      <div class="payment-name">{{ item.name }}</div>
                      <div class="payment-meta">{{ item.date }} · {{ item.group }}</div>
                      <div class="payment-secondary">{{ item.child_name }}</div>
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
                      <v-btn color="primary" class="pay-btn" icon @click="openSinglePayDialog(item)">
                        <v-icon>mdi-credit-card-outline</v-icon>
                      </v-btn>
                    </div>
                  </article>
                </div>

                <div v-else-if="activePaymentTab === 'month' && previewMonthlyDuePayments.length" class="list-wrap">
                  <article
                    v-for="item in previewMonthlyDuePayments"
                    :key="`month-${item.id}`"
                    class="payment-item payment-item-action"
                  >
                    <div class="payment-main">
                      <div class="payment-name">{{ item.group }}</div>
                      <div class="payment-meta">{{ item.month_label }}</div>
                      <div class="payment-secondary">{{ item.child_name }}</div>
                      <div class="payment-secondary">{{ item.covered_sessions_count }} trainings included</div>
                      <div class="payment-deadline">Monthly deadline: {{ item.deadline }}</div>
                    </div>

                    <div class="payment-side payment-action-side">
                      <div class="payment-status-row">
                        <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                        <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                          {{ item.status }}
                        </v-chip>
                      </div>
                      <v-btn color="primary" class="pay-btn" icon @click="openMonthlyPayDialog(item)">
                        <v-icon>mdi-calendar-check-outline</v-icon>
                      </v-btn>
                    </div>
                  </article>
                </div>

                <div v-else class="empty-state">
                  {{
                    activePaymentTab === 'training'
                      ? 'No individual trainings are available for payment right now.'
                      : 'No monthly payments are available right now.'
                  }}
                </div>
              </section>

              <div class="lists-grid">
                <section class="list-card">
                  <div class="list-header">
                    <div class="list-title">Payment History</div>
                    <button type="button" class="show-all-btn" @click="historyDialog = true">
                      Show all
                    </button>
                  </div>

                  <div v-if="previewPaymentHistory.length" class="list-wrap">
                    <article
                      v-for="item in previewPaymentHistory"
                      :key="`history-${item.id}`"
                      class="payment-item"
                    >
                      <div class="payment-main">
                        <div class="payment-name">{{ formatHistoryItemName(item) }}</div>
                        <div class="payment-meta">{{ formatHistoryItemMeta(item) }}</div>
                        <div class="payment-secondary">{{ formatHistoryItemSecondary(item) }}</div>
                      </div>

                      <div class="payment-side payment-side-history">
                        <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                        <v-chip size="small" :color="getStatusColor(capitalize(item.status))" class="payment-chip payment-chip-history" dark>
                          {{ capitalize(item.status) }}
                        </v-chip>
                        <v-btn variant="text" class="history-action-btn" @click="openReceipt(item)">
                          View receipt
                        </v-btn>
                      </div>
                    </article>
                  </div>
                  <div v-else class="empty-state">No payment history yet.</div>
                </section>

                <section class="list-card">
                  <div class="list-header">
                    <div class="list-title">Recent Activity</div>
                    <button type="button" class="show-all-btn" @click="transactionsDialog = true">
                      Show all
                    </button>
                  </div>

                  <div v-if="previewRecentActivity.length" class="list-wrap">
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
                  <div v-else class="empty-state">No recent activity found.</div>
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
                <div class="list-dialog-subtitle">Unified timeline of training, monthly and refund payment actions.</div>
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
                <div class="list-dialog-title">All Trainings To Pay</div>
                <div class="list-dialog-subtitle">Pay one or several available trainings with your card or account balance.</div>
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
                  <label class="training-select-control">
                    <input
                      type="checkbox"
                      :checked="isTrainingSelected(item.id)"
                      @change="toggleTrainingSelection(item)"
                    >
                    <span>Select</span>
                  </label>
                  <div class="payment-name">{{ item.name }}</div>
                  <div class="payment-meta">{{ item.date }} · {{ item.group }}</div>
                  <div class="payment-secondary">{{ item.child_name }}</div>
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
                  <v-btn color="primary" class="pay-btn" icon @click="openSinglePayDialog(item)">
                    <v-icon>mdi-credit-card-outline</v-icon>
                  </v-btn>
                </div>
              </article>
            </div>

            <div class="pay-dialog-actions dialog-footer-actions">
              <v-btn
                color="primary"
                class="dialog-primary-btn"
                :disabled="!selectedSingleDueIds.length"
                @click="openSinglePayDialog()"
              >
                Pay selected
              </v-btn>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="monthlyPaymentsDialog" max-width="760">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Monthly Payments</div>
                <div class="list-dialog-subtitle">Pay the monthly group price for one child and one group at a time.</div>
              </div>

              <v-btn icon variant="text" @click="monthlyPaymentsDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article
                v-for="item in filteredMonthlyDuePayments"
                :key="`dialog-month-${item.id}`"
                class="payment-item payment-item-action"
              >
                <div class="payment-main">
                  <div class="payment-name">{{ item.group }}</div>
                  <div class="payment-meta">{{ item.month_label }}</div>
                  <div class="payment-secondary">{{ item.child_name }}</div>
                  <div class="payment-secondary">{{ item.covered_sessions_count }} trainings included</div>
                  <div class="payment-deadline">Monthly deadline: {{ item.deadline }}</div>
                </div>

                <div class="payment-side payment-action-side">
                  <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                  <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                    {{ item.status }}
                  </v-chip>
                  <v-btn color="primary" class="pay-btn" icon @click="openMonthlyPayDialog(item)">
                    <v-icon>mdi-calendar-check-outline</v-icon>
                  </v-btn>
                </div>
              </article>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="historyDialog" max-width="760">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Payment History</div>
                <div class="list-dialog-subtitle">See session and monthly payments with receipts and statuses.</div>
              </div>

              <v-btn icon variant="text" @click="historyDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article
                v-for="item in filteredPaymentHistory"
                :key="`dialog-history-${item.id}`"
                class="payment-item"
              >
                <div class="payment-main">
                  <div class="payment-name">{{ formatHistoryItemName(item) }}</div>
                  <div class="payment-meta">{{ formatHistoryItemMeta(item) }}</div>
                  <div class="payment-secondary">{{ formatHistoryItemSecondary(item) }}</div>
                </div>

                <div class="payment-side payment-side-history">
                  <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                  <v-chip size="small" :color="getStatusColor(capitalize(item.status))" class="payment-chip payment-chip-history" dark>
                    {{ capitalize(item.status) }}
                  </v-chip>
                  <v-btn variant="text" class="history-action-btn" @click="openReceipt(item)">
                    View receipt
                  </v-btn>
                </div>
              </article>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="balanceSourcesDialog" max-width="680">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Balance Sources</div>
                <div class="list-dialog-subtitle">All credits currently contributing to the parent account balance.</div>
              </div>

              <v-btn icon variant="text" @click="balanceSourcesDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div v-if="balanceSources.length" class="dialog-list-wrap">
              <article
                v-for="item in balanceSources"
                :key="`balance-source-${item.id}`"
                class="payment-item"
              >
                <div class="payment-main">
                  <div class="payment-name">{{ item.label }}</div>
                  <div class="payment-meta">{{ item.meta }}</div>
                </div>

                <div class="payment-side">
                  <div class="payment-amount balance-source-dialog-amount">+{{ formatCurrency(item.amount) }}</div>
                </div>
              </article>
            </div>

            <div v-else class="empty-state">
              No recent credits have been added to the balance yet.
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="payDialog" max-width="620">
          <v-card class="dialog-card list-dialog-card pay-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">{{ payDialogMode === 'month' ? 'Pay Month' : 'Pay Trainings' }}</div>
                <div class="list-dialog-subtitle">{{ payDialogSubtitle }}</div>
              </div>

              <v-btn icon variant="text" @click="payDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

              <div v-if="selectedPayItems.length" class="pay-dialog-body">
                <v-alert
                  v-if="paymentError"
                  type="error"
                  variant="tonal"
                  density="comfortable"
                  class="mb-4"
                >
                  {{ paymentError }}
                </v-alert>

                <div
                  v-for="item in selectedPayItems"
                  :key="`pay-item-${item.id}`"
                  class="pay-training-card"
                >
                  <div class="payment-name">{{ item.name || item.group }}</div>
                  <div class="payment-meta">
                    {{ payDialogMode === 'month' ? item.month_label : `${item.date} · ${item.group}` }}
                  </div>
                  <div class="payment-secondary">{{ item.child_name }}</div>
                  <div v-if="payDialogMode === 'month'" class="payment-secondary">
                    {{ item.covered_sessions_count }} trainings included
                  </div>
                  <div v-else class="payment-secondary">Coach: {{ item.trainer }}</div>
                  <div class="payment-deadline">
                    {{ payDialogMode === 'month' ? 'Monthly deadline' : 'Payment deadline' }}: {{ item.deadline }}
                  </div>
                </div>

              <div class="pay-training-amount">Total: {{ formatCurrency(payDialogTotal) }}</div>

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
                    'method-option-disabled': payDialogTotal > accountBalance
                  }"
                  :disabled="payDialogTotal > accountBalance"
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

        <v-dialog v-model="receiptDialog" max-width="620">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">Payment Receipt</div>
                <div class="list-dialog-subtitle">Receipt details for the selected payment.</div>
              </div>
              <v-btn icon variant="text" @click="receiptDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div v-if="selectedPaymentRecord" class="receipt-body">
              <div class="receipt-summary-card">
                <div class="receipt-top-row">
                  <div>
                    <div class="payment-name">{{ selectedPaymentRecord.child_name || `Child #${selectedPaymentRecord.child_id}` }}</div>
                    <div class="payment-meta">Parent: {{ selectedPaymentRecord.parent_name || `User #${selectedPaymentRecord.parent_id}` }}</div>
                  </div>
                  <v-chip size="small" :color="getStatusColor(capitalize(selectedPaymentRecord.status))" class="payment-chip" dark>
                    {{ capitalize(selectedPaymentRecord.status) }}
                  </v-chip>
                </div>

                <div class="receipt-grid">
                  <div class="info-item">
                    <span class="info-label">Amount</span>
                    <span class="info-value">{{ formatCurrency(selectedPaymentRecord.amount) }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Method</span>
                    <span class="info-value">{{ selectedPaymentRecord.method || 'Not set' }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Transaction ID</span>
                    <span class="info-value">{{ selectedPaymentRecord.transaction_id || 'Not set' }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Created</span>
                    <span class="info-value">{{ selectedPaymentRecord.created_at ? formatDateTime(selectedPaymentRecord.created_at) : 'Not set' }}</span>
                  </div>
                </div>
              </div>

              <div class="list-title receipt-items-title">Receipt items</div>
              <div class="dialog-list-wrap receipt-items-wrap">
                <article
                  v-for="item in selectedPaymentRecord.items || []"
                  :key="`receipt-item-${item.id}`"
                  class="payment-item"
                >
                  <div class="payment-main">
                    <div class="payment-name">{{ formatReceiptItemName(item) }}</div>
                    <div class="payment-meta">{{ formatReceiptItemMeta(item) }}</div>
                  </div>

                  <div class="payment-side">
                    <div class="payment-amount">{{ formatCurrency(item.price) }}</div>
                  </div>
                </article>
              </div>

              <div class="receipt-actions">
                <v-btn
                  color="primary"
                  class="dialog-primary-btn"
                  prepend-icon="mdi-download-outline"
                  @click="downloadReceipt(selectedPaymentRecord)"
                >
                  Download receipt
                </v-btn>
              </div>
            </div>
          </v-card>
        </v-dialog>

        <AppPageFooter :dark-mode="darkMode" />

        <AppNotificationsDialog
          v-model="notificationsDialog"
          :dark-mode="darkMode"
          :notifications="notificationItems"
          :loading="notificationsLoading"
          @notification-click="handleNotificationClick"
        />

      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppNotificationsDialog from '../components/AppNotificationsDialog.vue'
import AppPageFooter from '../components/AppPageFooter.vue'
import { useNotifications } from '../composables/useNotifications'
import { useSelectedChild } from '../composables/useSelectedChild'
import { paymentsApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const search = ref('')
const darkMode = ref(false)
const transactionsDialog = ref(false)
const dueTrainingsDialog = ref(false)
const monthlyPaymentsDialog = ref(false)
const historyDialog = ref(false)
const balanceSourcesDialog = ref(false)
const payDialog = ref(false)
const receiptDialog = ref(false)
const notificationsDialog = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const activePaymentTab = ref('training')
const payDialogMode = ref('training')
const selectedSingleDueIds = ref([])
const selectedMonthlyDueId = ref(null)
const selectedPaymentRecordId = ref(null)
const selectedPaymentMethod = ref('Card')
const darkModeStorageKey = 'app-dark-mode'
const avatarFor = (seed, label = seed) => createAvatarDataUri(seed, label)
const paymentError = ref('')
const loading = ref(false)
const { user } = useAuth()
const {
  items: notificationItems,
  loading: notificationsLoading,
  unreadCount,
  loadNotifications,
  markNotificationRead
} = useNotifications()
const { selectedChildId, syncSelectedChild } = useSelectedChild()
const paymentsData = ref({
  summary: {
    total_paid: 0,
    pending: 0,
    overdue: 0
  },
  account_balance: 0,
  due_trainings: [],
  due_monthly_payments: [],
  recent_activity: [],
  spending_breakdown: [],
  payments: []
})

const chartHalfYearOffset = ref(0)
const chartGridLines = [20, 50, 80, 110]
const chartStartX = 28
const chartEndX = 332
const chartTopY = 16
const chartBottomY = 118
const profileName = computed(() => {
  if (!user.value) return 'SportSystem User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const isParent = computed(() => user.value?.role === 'parent')
const navItems = computed(() => [
  { label: 'Home', icon: 'mdi-home-outline', to: '/home' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline', to: '/attendance' },
  ...(user.value?.role === 'parent'
    ? [{ label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }]
    : [])
])
const profileEmail = computed(() => user.value?.email ?? 'user@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)
const dueTrainings = computed(() =>
  (paymentsData.value.due_trainings ?? []).filter((item) =>
    !isParent.value || !selectedChildId.value || item.child_id === selectedChildId.value
  )
)
const monthlyDuePayments = computed(() =>
  (paymentsData.value.due_monthly_payments ?? []).filter((item) =>
    !isParent.value || !selectedChildId.value || item.child_id === selectedChildId.value
  )
)
const paymentRecords = computed(() =>
  (paymentsData.value.payments ?? []).filter((item) =>
    !isParent.value || !selectedChildId.value || item.child_id === selectedChildId.value
  )
)
const accountBalance = computed(() => Number(paymentsData.value.account_balance ?? 0))
const chartPeriod = computed(() => {
  const now = new Date()
  const currentHalfStartMonth = now.getMonth() < 6 ? 0 : 6
  const start = new Date(now.getFullYear(), currentHalfStartMonth + (chartHalfYearOffset.value * 6), 1)
  const half = start.getMonth() < 6 ? 1 : 2

  return {
    start,
    half,
    label: `H${half} ${start.getFullYear()}`
  }
})
const chartPeriodLabel = computed(() => chartPeriod.value.label)
const chartMonths = computed(() =>
  Array.from({ length: 6 }, (_, index) => {
    const month = new Date(chartPeriod.value.start.getFullYear(), chartPeriod.value.start.getMonth() + index, 1)

    return {
      key: `${month.getFullYear()}-${String(month.getMonth() + 1).padStart(2, '0')}`,
      label: month.toLocaleDateString('en-US', { month: 'short' })
    }
  })
)
const chartMonthlyTotals = computed(() => {
  const totals = paymentRecords.value.reduce((acc, payment) => {
    if (payment.status !== 'paid' || !payment.created_at) return acc
    const date = new Date(payment.created_at)
    const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`
    acc[key] = (acc[key] || 0) + Number(payment.amount)
    return acc
  }, {})

  return chartMonths.value.map((month) => ({
    ...month,
    value: totals[month.key] || 0
  }))
})
const chartPeriodTotal = computed(() =>
  chartMonthlyTotals.value.reduce((sum, month) => sum + Number(month.value || 0), 0)
)
const chartPeriodPeak = computed(() =>
  Math.max(...chartMonthlyTotals.value.map((month) => Number(month.value || 0)), 0)
)
const chartPoints = computed(() => {
  const values = chartMonthlyTotals.value.map((month) => Number(month.value || 0))
  const maxValue = Math.max(...values, 1)
  const step = (chartEndX - chartStartX) / Math.max(chartMonthlyTotals.value.length - 1, 1)
  const chartHeight = chartBottomY - chartTopY

  return chartMonthlyTotals.value.map((month, index) => ({
    key: month.key,
    label: month.label,
    x: chartStartX + index * step,
    y: chartBottomY - ((values[index] / maxValue) * chartHeight),
    value: values[index]
  }))
})

const chartLinePath = computed(() =>
  chartPoints.value.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`).join(' ')
)

const chartAreaPath = computed(() => {
  const line = chartLinePath.value
  const lastPoint = chartPoints.value[chartPoints.value.length - 1]
  const firstPoint = chartPoints.value[0]

  return `${line} L ${lastPoint.x} 128 L ${firstPoint.x} 128 Z`
})

const normalizedSearch = computed(() => search.value.trim().toLowerCase())
const summaryCards = computed(() => [
  {
    label: 'Total Paid',
    value: formatCurrency(paymentRecords.value.filter((item) => item.status === 'paid').reduce((sum, item) => sum + Number(item.amount || 0), 0))
  },
  {
    label: 'Pending',
    value: formatCurrency(
      dueTrainings.value.filter((item) => item.status === 'Pending').reduce((sum, item) => sum + Number(item.amount || 0), 0)
      + monthlyDuePayments.value.filter((item) => item.status === 'Pending').reduce((sum, item) => sum + Number(item.amount || 0), 0)
    )
  },
  {
    label: 'Overdue',
    value: formatCurrency(
      dueTrainings.value.filter((item) => item.status === 'Overdue').reduce((sum, item) => sum + Number(item.amount || 0), 0)
      + monthlyDuePayments.value.filter((item) => item.status === 'Overdue').reduce((sum, item) => sum + Number(item.amount || 0), 0)
    )
  }
])

const recentActivity = computed(() =>
  (paymentsData.value.recent_activity ?? []).filter((item) =>
    !isParent.value || !selectedChildId.value || item.child_id === selectedChildId.value
  )
)
const paymentHistory = computed(() =>
  paymentRecords.value
    .filter((item) => ['paid', 'failed', 'refunded', 'pending'].includes(String(item.status || '').toLowerCase()))
    .sort((left, right) => new Date(right.updated_at || right.created_at || 0) - new Date(left.updated_at || left.created_at || 0))
)
const balanceSources = computed(() => {
  const refundedCredits = paymentHistory.value
    .filter((item) => String(item.status || '').toLowerCase() === 'refunded')
    .map((item) => ({
      id: `refund-${item.id}`,
      label: formatHistoryItemName(item),
      meta: `Refunded · ${item.updated_at ? formatDateTime(item.updated_at) : formatDateTime(item.created_at)}`,
      amount: Number(item.amount || 0),
    }))
    .filter((item) => item.amount > 0)

  const refundedTotal = refundedCredits.reduce((sum, item) => sum + item.amount, 0)
  const carryoverAmount = Math.max(0, Number(accountBalance.value || 0) - refundedTotal)

  if (carryoverAmount > 0) {
    refundedCredits.push({
      id: 'carryover-credit',
      label: 'Carryover credit',
      meta: 'Existing balance from previous training credits',
      amount: carryoverAmount,
    })
  }

  return refundedCredits
    .sort((left, right) => {
      if (left.id === 'carryover-credit') return 1
      if (right.id === 'carryover-credit') return -1
      return right.amount - left.amount
    })
})
const previewBalanceSources = computed(() => balanceSources.value.slice(0, 3))

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
    [item.name, item.date, item.group, item.trainer, item.status, item.child_name]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(normalizedSearch.value))
  )
})

const filteredMonthlyDuePayments = computed(() => {
  if (!normalizedSearch.value) return monthlyDuePayments.value

  return monthlyDuePayments.value.filter((item) =>
    [item.group, item.month_label, item.child_name, item.trainer, item.status]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(normalizedSearch.value))
  )
})

const filteredPaymentHistory = computed(() => {
  if (!normalizedSearch.value) return paymentHistory.value

  return paymentHistory.value.filter((item) =>
    [
      item.child_name,
      item.parent_name,
      item.method,
      item.status,
      item.payment_type,
      ...((item.items || []).flatMap((entry) => [
        entry.session_name,
        entry.group_name,
        entry.month
      ]))
    ]
      .filter(Boolean)
      .some((value) => String(value).toLowerCase().includes(normalizedSearch.value))
  )
})

const selectedSingleDueTrainings = computed(() =>
  dueTrainings.value.filter((item) => selectedSingleDueIds.value.includes(item.id))
)

const selectedMonthlyDuePayment = computed(() =>
  monthlyDuePayments.value.find((item) => item.id === selectedMonthlyDueId.value) ?? null
)

const selectedPaymentRecord = computed(() =>
  paymentHistory.value.find((item) => item.id === selectedPaymentRecordId.value) ?? null
)

const selectedPayItems = computed(() =>
  payDialogMode.value === 'month'
    ? (selectedMonthlyDuePayment.value ? [selectedMonthlyDuePayment.value] : [])
    : selectedSingleDueTrainings.value
)

const previewDueTrainings = computed(() => filteredDueTrainings.value.slice(0, 3))
const previewMonthlyDuePayments = computed(() => filteredMonthlyDuePayments.value.slice(0, 3))
const previewPaymentHistory = computed(() => filteredPaymentHistory.value.slice(0, 3))
const previewRecentActivity = computed(() => filteredRecentActivity.value.slice(0, 3))

const payDialogSubtitle = computed(() => {
  if (payDialogMode.value === 'month') {
    return 'Pay the full monthly group price. Covered trainings from that month will be hidden from single-payment options.'
  }

  return 'Pay one or several selected trainings that are currently open for payment.'
})

const payDialogTotal = computed(() =>
  selectedPayItems.value.reduce((sum, item) => sum + Number(item.amount || 0), 0)
)

const getStatusColor = (status) => {
  if (status === 'Paid') return 'green'
  if (status === 'Refunded') return 'blue-grey'
  if (status === 'Credited') return 'teal'
  if (status === 'Credit reversed') return 'deep-orange'
  if (status === 'Pending') return 'orange'
  if (status === 'Overdue') return 'red'
  if (status === 'Failed') return 'red'
  if (status === 'Cancelled') return 'red'
  if (status === 'Completed') return 'green'

  return 'grey'
}

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
  loadPayments()
  loadNotifications()
})

watch(darkMode, (value) => {
  localStorage.setItem(darkModeStorageKey, String(value))
})

watch(isCompactNav, (value) => {
  if (!value) mobileMenuOpen.value = false
})

watch(payDialog, (value) => {
  if (!value) {
    paymentError.value = ''
  }
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

watch(notificationsDialog, (value) => {
  if (value) {
    loadNotifications(true)
  }
})

watch(selectedChildId, () => {
  selectedSingleDueIds.value = []
  selectedMonthlyDueId.value = null
  selectedPaymentRecordId.value = null
})

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-IE', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0
  }).format(Number(amount || 0))
}

function formatCompactCurrency(amount) {
  return new Intl.NumberFormat('en-IE', {
    style: 'currency',
    currency: 'EUR',
    notation: 'compact',
    maximumFractionDigits: 0
  }).format(Number(amount || 0))
}

function changeChartHalfYear(direction) {
  const nextOffset = chartHalfYearOffset.value + direction
  if (nextOffset > 0) return
  chartHalfYearOffset.value = nextOffset
}

function formatDateTime(value) {
  if (!value) return 'Not set'
  return new Date(value).toLocaleString('en-US', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

async function loadPayments() {
  loading.value = true

  try {
    const data = await paymentsApi.list()
    paymentsData.value = data
    if (isParent.value) {
      syncSelectedChild([
        ...(data?.due_trainings ?? []).map((item) => item.child_id),
        ...(data?.due_monthly_payments ?? []).map((item) => item.child_id),
        ...(data?.payments ?? []).map((item) => item.child_id),
      ].filter(Boolean), { preserveExisting: true })
    }
  } finally {
    loading.value = false
  }
}

function isTrainingSelected(itemId) {
  return selectedSingleDueIds.value.includes(itemId)
}

function toggleTrainingSelection(item) {
  const isSelected = isTrainingSelected(item.id)

  if (isSelected) {
    selectedSingleDueIds.value = selectedSingleDueIds.value.filter((id) => id !== item.id)
    return
  }

  if (!selectedSingleDueIds.value.length) {
    selectedSingleDueIds.value = [item.id]
    return
  }

  const currentItems = dueTrainings.value.filter((entry) => selectedSingleDueIds.value.includes(entry.id))
  const selectedChildId = currentItems[0]?.child_id ?? null

  if (selectedChildId && selectedChildId !== item.child_id) {
    paymentError.value = 'You can only pay selected trainings for one child at a time.'
    return
  }

  selectedSingleDueIds.value = [...selectedSingleDueIds.value, item.id]
}

function resetPayDialogState() {
  paymentError.value = ''
  selectedPaymentMethod.value = 'Card'
}

function openSinglePayDialog(training = null) {
  resetPayDialogState()

  if (training) {
    selectedSingleDueIds.value = [training.id]
  }

  if (!selectedSingleDueIds.value.length) return

  payDialogMode.value = 'training'
  selectedMonthlyDueId.value = null
  selectedPaymentMethod.value = payDialogTotal.value <= accountBalance.value ? 'Account balance' : 'Card'
  payDialog.value = true
}

function openMonthlyPayDialog(item) {
  resetPayDialogState()
  selectedMonthlyDueId.value = item.id
  payDialogMode.value = 'month'
  selectedPaymentMethod.value = Number(item.amount || 0) <= accountBalance.value ? 'Account balance' : 'Card'
  payDialog.value = true
}

async function confirmPayment() {
  if (!selectedPayItems.value.length) return
  if (selectedPaymentMethod.value === 'Account balance' && payDialogTotal.value > accountBalance.value) return

  try {
    const payload =
      payDialogMode.value === 'month'
        ? {
            child_id: selectedMonthlyDuePayment.value.child_id,
            method: selectedPaymentMethod.value,
            items: [
              {
                type: 'month',
                group_id: selectedMonthlyDuePayment.value.group_id,
                month: selectedMonthlyDuePayment.value.month,
                price: selectedMonthlyDuePayment.value.amount
              }
            ]
          }
        : {
            child_id: selectedSingleDueTrainings.value[0].child_id,
            method: selectedPaymentMethod.value,
            items: selectedSingleDueTrainings.value.map((item) => ({
              type: 'session',
              session_id: item.session_id,
              price: item.amount
            }))
          }

    const payment = await paymentsApi.create(payload)

    await loadPayments()
    selectedSingleDueIds.value = []
    selectedMonthlyDueId.value = null
    payDialog.value = false
    openReceipt(payment)
  } catch (error) {
    const errors = error?.response?.data?.errors ?? {}
    paymentError.value = errors.method?.[0] || error?.response?.data?.message || 'Payment failed.'
  }
}

function capitalize(value) {
  if (!value) return ''
  return `${value.charAt(0).toUpperCase()}${value.slice(1)}`
}

function formatReceiptItemName(item) {
  if (item.type === 'month') {
    return item.group_name || 'Monthly payment'
  }

  return item.session_name || 'Training payment'
}

function formatReceiptItemMeta(item) {
  if (item.type === 'month') {
    const parts = [item.month, item.group_name]
    if (item.covered_sessions_count) {
      parts.push(`${item.covered_sessions_count} trainings`)
    }

    return parts.filter(Boolean).join(' · ')
  }

  return [item.group_name, 'Single training'].filter(Boolean).join(' · ')
}

function formatHistoryItemName(item) {
  const firstItem = item.items?.[0]

  if (item.payment_type === 'month') {
    return firstItem?.group_name || 'Monthly payment'
  }

  if (item.items?.length > 1) {
    return `${item.items.length} trainings paid`
  }

  return firstItem?.session_name || 'Training payment'
}

function formatHistoryItemMeta(item) {
  const firstItem = item.items?.[0]
  const date = item.created_at ? formatDateTime(item.created_at) : 'Not set'

  if (item.payment_type === 'month') {
    return [firstItem?.month, firstItem?.group_name, date].filter(Boolean).join(' · ')
  }

  return [item.method, date].filter(Boolean).join(' · ')
}

function formatHistoryItemSecondary(item) {
  const childName = item.child_name || `Child #${item.child_id}`
  const firstItem = item.items?.[0]

  if (item.payment_type === 'month') {
    const trainingsCount = firstItem?.covered_sessions_count ? `${firstItem.covered_sessions_count} trainings` : null
    return [childName, trainingsCount].filter(Boolean).join(' · ')
  }

  return childName
}

function openReceipt(item) {
  selectedPaymentRecordId.value = item.id
  receiptDialog.value = true
}

function escapeHtml(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;')
}

function downloadReceipt(payment) {
  const itemsMarkup = (payment.items || [])
    .map((item) => `
      <tr>
        <td style="padding:12px 0;border-bottom:1px solid #e5edf8;">${escapeHtml(formatReceiptItemName(item))}</td>
        <td style="padding:12px 0;border-bottom:1px solid #e5edf8;color:#6b7a90;">${escapeHtml(formatReceiptItemMeta(item))}</td>
        <td style="padding:12px 0;border-bottom:1px solid #e5edf8;text-align:right;font-weight:700;">${escapeHtml(formatCurrency(item.price))}</td>
      </tr>
    `)
    .join('')

  const html = `<!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <title>Receipt ${escapeHtml(payment.transaction_id || payment.id)}</title>
      <style>
        body { font-family: Arial, sans-serif; margin: 32px; color: #172033; }
        .wrap { max-width: 760px; margin: 0 auto; }
        .top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 24px; }
        .brand { font-size: 28px; font-weight: 700; }
        .muted { color:#6b7a90; }
        .grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:16px; margin-bottom:24px; }
        .card { padding:16px; border:1px solid #dbe5f2; border-radius:16px; background:#f7fbff; }
        table { width:100%; border-collapse:collapse; }
      </style>
    </head>
    <body>
      <div class="wrap">
        <div class="top">
          <div>
            <div class="brand">SportSystem</div>
            <div class="muted">Payment receipt</div>
          </div>
          <div style="text-align:right;">
            <div><strong>Status:</strong> ${escapeHtml(capitalize(payment.status || ''))}</div>
            <div class="muted">${escapeHtml(formatDateTime(payment.created_at))}</div>
          </div>
        </div>
        <div class="grid">
          <div class="card"><strong>Child</strong><br>${escapeHtml(payment.child_name || `Child #${payment.child_id}`)}</div>
          <div class="card"><strong>Parent</strong><br>${escapeHtml(payment.parent_name || `User #${payment.parent_id}`)}</div>
          <div class="card"><strong>Method</strong><br>${escapeHtml(payment.method || 'Not set')}</div>
          <div class="card"><strong>Transaction ID</strong><br>${escapeHtml(payment.transaction_id || 'Not set')}</div>
        </div>
        <table>
          <thead>
            <tr>
              <th style="text-align:left;padding-bottom:12px;">Item</th>
              <th style="text-align:left;padding-bottom:12px;">Details</th>
              <th style="text-align:right;padding-bottom:12px;">Amount</th>
            </tr>
          </thead>
          <tbody>${itemsMarkup}</tbody>
        </table>
        <div style="margin-top:20px;text-align:right;font-size:22px;font-weight:700;">Total: ${escapeHtml(formatCurrency(payment.amount))}</div>
      </div>
    </body>
  </html>`

  const blob = new Blob([html], { type: 'text/html;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const anchor = document.createElement('a')
  anchor.href = url
  anchor.download = `receipt-${payment.transaction_id || payment.id}.html`
  document.body.appendChild(anchor)
  anchor.click()
  document.body.removeChild(anchor)
  URL.revokeObjectURL(url)
}

async function handleNotificationClick(item) {
  if (item?.unread) {
    await markNotificationRead(item.id)
  }
}

async function handleLogout() {
  await logout()
  router.push('/login')
}

async function handleMobileLogout() {
  mobileMenuOpen.value = false
  await handleLogout()
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

.mobile-logout-btn {
  justify-content: flex-start;
  margin-top: 12px;
  min-height: 56px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 600;
}

.payments-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.payments-shell-dark .mobile-logout-btn {
  color: #eef4ff;
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

.logout-btn {
  color: #111827;
}

.payments-shell-dark .logout-btn {
  color: #dce6f7;
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

.payment-view-switch {
  margin-bottom: 18px;
}

.payment-view-tabs {
  display: inline-flex;
  gap: 10px;
  padding: 10px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.92);
  box-shadow: 0 12px 28px rgba(110, 136, 173, 0.08);
}

.payments-shell-dark .payment-view-tabs {
  background: rgba(18, 27, 43, 0.9);
  border: 1px solid rgba(74, 92, 126, 0.42);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.26);
}

.view-switch-btn {
  min-height: 48px;
  padding: 0 18px;
  border-radius: 16px;
  color: #64748b;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 700;
}

.payments-shell-dark .view-switch-btn {
  color: #8fa3c1;
}

.view-switch-btn-active {
  color: white;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 14px 28px rgba(22, 119, 255, 0.24);
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

.balance-source-card {
  padding: 14px;
  border-radius: 18px;
  background: rgba(240, 246, 255, 0.92);
  border: 1px solid rgba(216, 228, 247, 0.96);
}

.payments-shell-dark .balance-source-card {
  background: rgba(22, 31, 48, 0.94);
  border-color: rgba(74, 92, 126, 0.42);
}

.balance-source-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.balance-source-title {
  font-size: 0.88rem;
  font-weight: 700;
  color: #172033;
}

.payments-shell-dark .balance-source-title {
  color: #eef4ff;
}

.balance-source-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 12px;
}

.balance-source-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.balance-source-copy {
  min-width: 0;
  flex: 1;
}

.balance-source-name {
  font-size: 0.86rem;
  font-weight: 700;
  color: #172033;
}

.payments-shell-dark .balance-source-name {
  color: #eef4ff;
}

.balance-source-meta,
.balance-source-empty {
  margin-top: 3px;
  font-size: 0.78rem;
  color: #7b8798;
  line-height: 1.45;
}

.payments-shell-dark .balance-source-meta,
.payments-shell-dark .balance-source-empty {
  color: #8fa3c1;
}

.balance-source-amount {
  flex-shrink: 0;
  font-size: 0.88rem;
  font-weight: 700;
  color: #1677ff;
}

.balance-source-dialog-amount {
  color: #1677ff;
}

.feature-btn {
  min-height: 46px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
}

.chart-card-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 14px;
  margin-bottom: 12px;
}

.chart-card-header .feature-text {
  max-width: 340px;
  margin-top: 6px;
  margin-bottom: 0;
}

.chart-period-switch {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px;
  border-radius: 16px;
  background: rgba(231, 238, 255, 0.82);
  border: 1px solid rgba(205, 220, 245, 0.94);
}

.payments-shell-dark .chart-period-switch {
  background: rgba(27, 39, 61, 0.9);
  border-color: rgba(74, 92, 126, 0.42);
}

.chart-period-btn {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  color: #2a3a55;
  background: rgba(255, 255, 255, 0.72);
}

.chart-period-btn:disabled {
  opacity: 0.42;
}

.payments-shell-dark .chart-period-btn {
  color: #eef4ff;
  background: rgba(18, 27, 43, 0.92);
}

.chart-period-label {
  min-width: 88px;
  text-align: center;
  font-size: 0.88rem;
  font-weight: 700;
  color: #172033;
}

.payments-shell-dark .chart-period-label {
  color: #eef4ff;
}

.chart-summary-row {
  display: flex;
  gap: 10px;
  margin-bottom: 12px;
}

.chart-summary-pill {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 16px;
  background: rgba(240, 246, 255, 0.95);
  border: 1px solid rgba(216, 228, 247, 0.96);
  color: #6c7c93;
  font-size: 0.8rem;
}

.chart-summary-pill strong {
  color: #172033;
  font-size: 0.9rem;
}

.payments-shell-dark .chart-summary-pill {
  background: rgba(22, 31, 48, 0.94);
  border-color: rgba(74, 92, 126, 0.42);
  color: #8fa3c1;
}

.payments-shell-dark .chart-summary-pill strong {
  color: #eef4ff;
}

.chart-placeholder {
  height: 186px;
  padding: 14px;
  position: relative;
  overflow: hidden;
  border-radius: 20px;
  background:
    radial-gradient(circle at top right, rgba(128, 178, 255, 0.2), transparent 28%),
    linear-gradient(180deg, rgba(232, 239, 255, 0.98), rgba(245, 249, 255, 0.95));
  border: 1px solid rgba(255, 255, 255, 0.72);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.58);
}

.payments-shell-dark .chart-placeholder {
  background:
    radial-gradient(circle at top right, rgba(47, 95, 201, 0.22), transparent 28%),
    linear-gradient(180deg, rgba(18, 27, 43, 0.96), rgba(27, 39, 61, 0.92));
  border-color: rgba(74, 92, 126, 0.42);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
}

.payment-chart {
  width: 100%;
  height: 136px;
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
  filter: drop-shadow(0 4px 10px rgba(22, 119, 255, 0.16));
}

.chart-labels {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  margin-top: 2px;
  color: #7b8798;
  font-size: 0.78rem;
  line-height: 1;
  text-align: center;
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

.pay-section-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
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

.payment-item-selected {
  margin: 0 -12px;
  padding: 14px 12px;
  border-radius: 18px;
  background: rgba(232, 242, 255, 0.82);
}

.payments-shell-dark .payment-item-selected {
  background: rgba(22, 43, 76, 0.72);
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

.training-select-control {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  width: fit-content;
  padding: 8px 12px;
  border-radius: 999px;
  background: rgba(232, 242, 255, 0.82);
  border: 1px solid rgba(184, 205, 238, 0.82);
  font-size: 0.82rem;
  font-weight: 600;
  color: #51627d;
}

.training-select-control input {
  appearance: none;
  -webkit-appearance: none;
  width: 16px;
  height: 16px;
  margin: 0;
  border-radius: 5px;
  border: 1.5px solid rgba(95, 122, 168, 0.78);
  background: rgba(255, 255, 255, 0.96);
  display: grid;
  place-items: center;
  cursor: pointer;
  transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}

.training-select-control input::after {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 2px;
  background: white;
  transform: scale(0);
  transition: transform 0.18s ease;
}

.training-select-control input:checked {
  border-color: #1677ff;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 8px 16px rgba(22, 119, 255, 0.22);
}

.training-select-control input:checked::after {
  transform: scale(1);
}

.payments-shell-dark .training-select-control {
  color: #d7e4fb;
  background: rgba(22, 43, 76, 0.64);
  border-color: rgba(81, 117, 180, 0.52);
}

.payments-shell-dark .training-select-control input {
  border-color: rgba(129, 159, 214, 0.72);
  background: rgba(12, 20, 34, 0.94);
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

.payment-side-history {
  display: grid;
  justify-items: end;
  align-content: start;
  min-width: 156px;
  gap: 10px;
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

.payment-chip-history {
  margin-top: 0;
}

.pay-btn {
  width: 56px;
  min-width: 56px;
  height: 56px;
  min-height: 56px;
  padding: 0;
  border-radius: 18px;
}

.pay-selected-btn {
  min-height: 42px;
}

.history-action-btn {
  min-height: 28px;
  margin-top: 0;
  padding: 0;
  color: #111827;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 700;
  line-height: 1;
  justify-content: flex-end;
}

.payments-shell-dark .history-action-btn {
  color: #eef4ff;
}

.empty-state {
  padding: 18px 0 4px;
  color: #7b8798;
}

.payments-shell-dark .empty-state {
  color: #8fa3c1;
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

.dialog-footer-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 18px;
}

.receipt-body {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.receipt-summary-card {
  padding: 18px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.9);
  box-shadow: 0 12px 28px rgba(110, 136, 173, 0.08);
}

.list-dialog-card-dark .receipt-summary-card {
  background: rgba(18, 27, 43, 0.9);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.receipt-top-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 16px;
}

.receipt-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.info-label {
  font-size: 0.82rem;
  color: #7b8798;
}

.info-value {
  font-weight: 600;
  color: #172033;
}

.list-dialog-card-dark .info-label {
  color: #8fa3c1;
}

.list-dialog-card-dark .info-value {
  color: #eef4ff;
}

.receipt-items-title {
  margin-top: 2px;
}

.receipt-items-wrap {
  max-height: 280px;
}

.receipt-actions {
  display: flex;
  justify-content: flex-end;
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

  .payment-side-history {
    min-width: 140px;
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

  .payment-view-tabs {
    display: flex;
    width: 100%;
  }

  .view-switch-btn {
    flex: 1;
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

  .chart-card-header,
  .chart-summary-row {
    flex-direction: column;
  }

  .chart-period-switch {
    align-self: flex-start;
  }

  .pay-section-actions {
    width: 100%;
    justify-content: space-between;
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
    padding: 12px 12px 12px;
  }

  .payment-chart {
    height: 118px;
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

  .receipt-grid {
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

  .chart-period-switch {
    width: 100%;
    justify-content: space-between;
  }

  .chart-period-label {
    flex: 1;
  }

  .payment-view-tabs {
    width: 100%;
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

  .payment-side-history {
    min-width: 132px;
    justify-items: end;
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

  .history-action-btn {
    justify-content: flex-end;
  }

  .chart-placeholder {
    height: 156px;
    padding: 10px 10px 10px;
  }

  .payment-chart {
    height: 108px;
  }

  .list-dialog-card {
    padding: 18px;
  }

  .list-dialog-header {
    flex-direction: column;
    align-items: stretch;
  }

  .receipt-top-row,
  .receipt-actions,
  .dialog-footer-actions {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
