<template>
  <v-app>
    <v-main class="admin-payments-page">
      <div class="admin-payments-shell" :class="{ 'admin-payments-shell-dark': darkMode }">
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
                <div class="brand-caption">Admin workspace</div>
              </div>
            </div>

            <nav class="mobile-nav-list">
              <v-btn
                v-for="item in navItems"
                :key="`mobile-${item.label}`"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/admin-payments' }"
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
                <img :src="avatarFor(profileSeed, profileName)" alt="Admin profile">
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

        <div class="admin-payments-panel">
          <aside class="sidebar-card">
            <div class="brand-block">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">Admin workspace</div>
              </div>
            </div>

            <nav class="nav-list">
              <v-btn
                v-for="item in navItems"
                :key="item.label"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/admin-payments' }"
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
                  <div class="brand-caption">Admin Payments</div>
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
                    <img :src="avatarFor(profileSeed, profileName)" alt="Admin profile">
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
                    <img :src="avatarFor(profileSeed, profileName)" alt="Admin profile">
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
                  <h1 class="payments-title">Admin Payments</h1>
                  <div class="payments-subtitle">
                    System-wide overview of collected, pending and overdue payments.
                  </div>
                </div>
              </div>

              <div class="overview-stats-grid">
                <article v-for="item in overviewStats" :key="item.label" class="overview-stat-card">
                  <div class="summary-label">{{ item.label }}</div>
                  <div class="summary-value">{{ item.value }}</div>
                </article>
              </div>

              <div class="finder-card">
                <div>
                  <div class="list-title">Person Ledger</div>
                  <div class="payments-subtitle">Open a dedicated search window to review debts, receipts and payment history for one person.</div>
                </div>

                <v-btn color="primary" class="person-ledger-btn" prepend-icon="mdi-account-search-outline" @click="personLedgerDialog = true">
                  Find person
                </v-btn>
              </div>

              <div v-if="loading" class="state-wrap loading-state">
                <v-progress-circular indeterminate color="primary" size="28" />
                <span>Loading payments...</span>
              </div>

              <template v-else>
                <div class="lists-grid">
                  <section class="list-card">
                    <div class="list-header">
                      <div class="list-title">Upcoming Payments To Collect</div>
                      <button type="button" class="show-all-btn" @click="dueDialog = true">
                        Show all
                      </button>
                    </div>

                    <div v-if="previewDueTrainings.length" class="list-wrap">
                      <article
                        v-for="item in previewDueTrainings"
                        :key="`due-${item.id}`"
                        class="payment-item"
                      >
                        <div class="payment-main">
                          <div class="payment-name">{{ formatDueItemTitle(item) }}</div>
                          <div class="payment-meta">{{ formatDueItemMeta(item) }}</div>
                          <div class="payment-secondary">{{ item.child_name }}</div>
                          <div v-if="item.parent_name" class="payment-secondary">Parent: {{ item.parent_name }}</div>
                          <div class="payment-secondary">{{ formatDueItemSecondary(item) }}</div>
                          <div class="payment-deadline">{{ formatDueItemDeadlineLabel(item) }}: {{ item.deadline }}</div>
                        </div>

                        <div class="payment-side">
                          <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                          <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                            {{ item.status }}
                          </v-chip>
                        </div>
                      </article>
                    </div>
                    <div v-else class="empty-state">No upcoming unpaid payments.</div>
                  </section>

                  <section class="list-card">
                    <div class="list-header">
                      <div class="list-title">Overdue Debtors</div>
                      <button type="button" class="show-all-btn" @click="breakdownDialog = true">
                        Show all
                      </button>
                    </div>

                    <div v-if="previewOverdueDebtors.length" class="list-wrap">
                      <article
                        v-for="item in previewOverdueDebtors"
                        :key="item.key"
                        class="payment-item"
                      >
                        <div class="payment-main">
                          <div class="payment-name">{{ item.child_name }}</div>
                          <div v-if="item.parent_name" class="payment-meta">Parent: {{ item.parent_name }}</div>
                          <div class="payment-secondary">{{ item.overdue_count }} overdue trainings</div>
                          <div class="payment-secondary">Since {{ formatSinceDate(item.oldest_deadline_at) }}</div>
                          <div class="payment-deadline">Oldest debt: {{ formatOverdueAge(item.max_overdue_days) }}</div>
                        </div>

                        <div class="payment-side">
                          <div class="payment-amount">{{ formatCurrency(item.total_amount) }}</div>
                          <v-chip size="small" color="red" class="payment-chip" dark>
                            Overdue
                          </v-chip>
                        </div>
                      </article>
                    </div>
                    <div v-else class="empty-state">No overdue debtors right now.</div>
                  </section>
                </div>

                <div class="lists-grid">
                  <section class="list-card">
                    <div class="list-header">
                      <div class="list-title">Payment Records</div>
                      <button type="button" class="show-all-btn" @click="recordsDialog = true">
                        Show all
                      </button>
                    </div>

                    <div v-if="previewPaymentRecords.length" class="list-wrap">
                      <article
                        v-for="item in previewPaymentRecords"
                        :key="`payment-${item.id}`"
                        class="payment-item"
                      >
                        <div class="payment-main">
                          <div class="payment-name">{{ item.child_name || `Child #${item.child_id}` }}</div>
                          <div class="payment-meta">
                            Parent: {{ item.parent_name || `User #${item.parent_id}` }}
                          </div>
                          <div class="payment-secondary">
                            {{ item.method }}<span v-if="item.created_at"> · {{ formatDateTime(item.created_at) }}</span>
                          </div>
                        </div>

                        <div class="payment-side">
                          <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                          <v-chip size="small" :color="getStatusColor(capitalize(item.status))" class="payment-chip payment-chip-record" dark>
                            {{ capitalize(item.status) }}
                          </v-chip>
                          <div class="payment-actions payment-actions-record">
                            <v-btn variant="text" class="record-action-btn receipt-action-btn" @click="openReceipt(item)">
                              View receipt
                            </v-btn>
                            <v-btn
                              v-if="item.status === 'paid'"
                              variant="text"
                              color="error"
                              class="record-action-btn"
                              :loading="refundLoadingId === item.id"
                              @click="refundPayment(item)"
                            >
                              Refund
                            </v-btn>
                          </div>
                        </div>
                      </article>
                    </div>
                    <div v-else class="empty-state">No payment records found.</div>
                  </section>

                  <section class="list-card">
                    <div class="list-header">
                      <div class="list-title">Recent Activity</div>
                      <button type="button" class="show-all-btn" @click="activityDialog = true">
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
              </template>
            </div>
          </section>
        </div>

        <v-dialog v-model="activityDialog" max-width="760">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Recent Activity</div>
                <div class="list-dialog-subtitle">Unified timeline of trainings and payment actions.</div>
              </div>
              <v-btn icon variant="text" @click="activityDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article v-for="item in filteredRecentActivity" :key="`dialog-${item.id}`" class="payment-item">
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

        <v-dialog v-model="dueDialog" max-width="760">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Upcoming Payments To Collect</div>
                <div class="list-dialog-subtitle">Pending individual and monthly payments across the system.</div>
              </div>
              <v-btn icon variant="text" @click="dueDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article v-for="item in filteredDueTrainings" :key="`dialog-due-${item.id}`" class="payment-item">
                <div class="payment-main">
                  <div class="payment-name">{{ formatDueItemTitle(item) }}</div>
                  <div class="payment-meta">{{ formatDueItemMeta(item) }}</div>
                  <div class="payment-secondary">{{ item.child_name }}</div>
                  <div v-if="item.parent_name" class="payment-secondary">Parent: {{ item.parent_name }}</div>
                  <div class="payment-secondary">{{ formatDueItemSecondary(item) }}</div>
                  <div class="payment-deadline">{{ formatDueItemDeadlineLabel(item) }}: {{ item.deadline }}</div>
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

        <v-dialog v-model="recordsDialog" max-width="760">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Payment Records</div>
                <div class="list-dialog-subtitle">Processed payment entries with parent and child references.</div>
              </div>
              <v-btn icon variant="text" @click="recordsDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article v-for="item in filteredPaymentRecords" :key="`dialog-payment-${item.id}`" class="payment-item">
                <div class="payment-main">
                  <div class="payment-name">{{ item.child_name || `Child #${item.child_id}` }}</div>
                  <div class="payment-meta">Parent: {{ item.parent_name || `User #${item.parent_id}` }}</div>
                  <div class="payment-secondary">
                    {{ item.method }}<span v-if="item.created_at"> · {{ formatDateTime(item.created_at) }}</span>
                  </div>
                </div>

                <div class="payment-side">
                  <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                  <v-chip size="small" :color="getStatusColor(capitalize(item.status))" class="payment-chip payment-chip-record" dark>
                    {{ capitalize(item.status) }}
                  </v-chip>
                  <div class="payment-actions payment-actions-record">
                    <v-btn variant="text" class="record-action-btn receipt-action-btn" @click="openReceipt(item)">
                      View receipt
                    </v-btn>
                    <v-btn
                      v-if="item.status === 'paid'"
                      variant="text"
                      color="error"
                      class="record-action-btn"
                      :loading="refundLoadingId === item.id"
                      @click="refundPayment(item)"
                    >
                      Refund
                    </v-btn>
                  </div>
                </div>
              </article>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="personDueDialog" max-width="760">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Debts</div>
                <div class="list-dialog-subtitle">
                  {{ selectedPersonOption ? `Overdue debts for ${selectedPersonOption.label}.` : 'Overdue debts for the selected person.' }}
                </div>
              </div>
              <v-btn icon variant="text" @click="personDueDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article v-for="item in personDueItems" :key="`person-due-dialog-${item.id}`" class="payment-item">
                <div class="payment-main">
                  <div class="payment-name">{{ formatDueItemTitle(item) }}</div>
                  <div class="payment-meta">{{ formatDueItemMeta(item) }}</div>
                  <div class="payment-secondary">{{ item.child_name }}</div>
                  <div v-if="item.parent_name" class="payment-secondary">Parent: {{ item.parent_name }}</div>
                  <div class="payment-secondary">{{ formatDueItemSecondary(item) }}</div>
                  <div class="payment-deadline">{{ formatDueItemDeadlineLabel(item) }}: {{ item.deadline }}</div>
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

        <v-dialog v-model="personTransactionsDialog" max-width="760">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Transactions</div>
                <div class="list-dialog-subtitle">
                  {{ selectedPersonOption ? `Transaction history for ${selectedPersonOption.label}.` : 'Transactions for the selected person.' }}
                </div>
              </div>
              <v-btn icon variant="text" @click="personTransactionsDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article v-for="item in personPaymentRecords" :key="`person-payment-dialog-${item.id}`" class="payment-item">
                <div class="payment-main">
                  <div class="payment-name">{{ item.child_name || `Child #${item.child_id}` }}</div>
                  <div class="payment-meta">Parent: {{ item.parent_name || `User #${item.parent_id}` }}</div>
                  <div class="payment-secondary">
                    {{ item.method }}<span v-if="item.created_at"> · {{ formatDateTime(item.created_at) }}</span>
                  </div>
                </div>

                <div class="payment-side">
                  <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                  <v-chip size="small" :color="getStatusColor(capitalize(item.status))" class="payment-chip" dark>
                    {{ capitalize(item.status) }}
                  </v-chip>
                  <div class="payment-actions">
                    <v-btn variant="text" class="record-action-btn receipt-action-btn" @click="openReceipt(item)">
                      View receipt
                    </v-btn>
                    <v-btn
                      v-if="item.status === 'paid'"
                      variant="text"
                      color="error"
                      class="record-action-btn"
                      :loading="refundLoadingId === item.id"
                      @click="refundPayment(item)"
                    >
                      Refund
                    </v-btn>
                  </div>
                </div>
              </article>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="personLedgerDialog" max-width="860">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">Find Person Payments</div>
                <div class="list-dialog-subtitle">Search a parent or child to review debts, transactions and receipts.</div>
              </div>
              <v-btn icon variant="text" @click="personLedgerDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="person-ledger-body">
              <v-autocomplete
                v-model="selectedPersonFilter"
                :items="personOptions"
                item-title="label"
                item-value="value"
                label="Parent or child"
                variant="outlined"
                class="person-field"
                placeholder="Search by name"
                clearable
              />

              <div v-if="selectedPersonOption" class="person-ledger-summary">
                <div class="payment-name">{{ selectedPersonOption.label }}</div>
                <div class="payment-meta">
                  {{ personDueItems.length }} debts · {{ personPaymentRecords.length }} transactions
                </div>
              </div>

              <div class="person-ledger-grid">
                <section class="list-card">
                  <div class="list-header">
                    <div class="list-title">Debts</div>
                    <button
                      v-if="selectedPersonFilter && personDueItems.length"
                      type="button"
                      class="show-all-btn"
                      @click="personDueDialog = true"
                    >
                      View all
                    </button>
                  </div>

                  <div v-if="personDueItems.length" class="dialog-list-wrap person-dialog-list">
                    <article
                      v-for="item in personDueItems"
                      :key="`person-due-${item.id}`"
                      class="payment-item"
                    >
                      <div class="payment-main">
                        <div class="payment-name">{{ formatDueItemTitle(item) }}</div>
                        <div class="payment-meta">{{ formatDueItemMeta(item) }}</div>
                        <div class="payment-secondary">{{ item.child_name }}</div>
                        <div v-if="item.parent_name" class="payment-secondary">Parent: {{ item.parent_name }}</div>
                        <div class="payment-secondary">{{ formatDueItemSecondary(item) }}</div>
                        <div class="payment-deadline">{{ formatDueItemDeadlineLabel(item) }}: {{ item.deadline }}</div>
                      </div>

                      <div class="payment-side">
                        <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                        <v-chip size="small" :color="getStatusColor(item.status)" class="payment-chip" dark>
                          {{ item.status }}
                        </v-chip>
                      </div>
                    </article>
                  </div>
                  <div v-else class="empty-state person-empty-state">
                    {{ selectedPersonFilter ? 'No debts for this person.' : 'Select a person to view debts.' }}
                  </div>
                </section>

                <section class="list-card">
                  <div class="list-header">
                    <div class="list-title">Transactions</div>
                    <button
                      v-if="selectedPersonFilter && personPaymentRecords.length"
                      type="button"
                      class="show-all-btn"
                      @click="personTransactionsDialog = true"
                    >
                      View all
                    </button>
                  </div>

                  <div v-if="personPaymentRecords.length" class="dialog-list-wrap person-dialog-list">
                    <article
                      v-for="item in personPaymentRecords"
                      :key="`person-payment-${item.id}`"
                      class="payment-item"
                    >
                      <div class="payment-main">
                        <div class="payment-name">{{ item.child_name || `Child #${item.child_id}` }}</div>
                        <div class="payment-meta">Parent: {{ item.parent_name || `User #${item.parent_id}` }}</div>
                        <div class="payment-secondary">
                          {{ item.method }}<span v-if="item.created_at"> · {{ formatDateTime(item.created_at) }}</span>
                        </div>
                      </div>

                      <div class="payment-side">
                        <div class="payment-amount">{{ formatCurrency(item.amount) }}</div>
                        <v-chip size="small" :color="getStatusColor(capitalize(item.status))" class="payment-chip payment-chip-record" dark>
                          {{ capitalize(item.status) }}
                        </v-chip>
                        <div class="payment-actions payment-actions-record">
                          <v-btn variant="text" class="record-action-btn receipt-action-btn" @click="openReceipt(item)">
                            View receipt
                          </v-btn>
                          <v-btn
                            v-if="item.status === 'paid'"
                            variant="text"
                            color="error"
                            class="record-action-btn"
                            :loading="refundLoadingId === item.id"
                            @click="refundPayment(item)"
                          >
                            Refund
                          </v-btn>
                        </div>
                      </div>
                    </article>
                  </div>
                  <div v-else class="empty-state person-empty-state">
                    {{ selectedPersonFilter ? 'No transactions for this person.' : 'Select a person to view transactions.' }}
                  </div>
                </section>
              </div>
            </div>
          </v-card>
        </v-dialog>

        <v-dialog v-model="breakdownDialog" max-width="720">
          <v-card class="dialog-card list-dialog-card" :class="{ 'list-dialog-card-dark': darkMode }">
            <div class="list-dialog-header">
              <div>
                <div class="list-dialog-title">All Overdue Debtors</div>
                <div class="list-dialog-subtitle">People with overdue unpaid trainings and how long the debt has been outstanding.</div>
              </div>
              <v-btn icon variant="text" @click="breakdownDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="dialog-list-wrap">
              <article
                v-for="item in overdueDebtors"
                :key="`dialog-overdue-debtor-${item.key}`"
                class="payment-item"
              >
                <div class="payment-main">
                  <div class="payment-name">{{ item.child_name }}</div>
                  <div v-if="item.parent_name" class="payment-meta">Parent: {{ item.parent_name }}</div>
                  <div class="payment-secondary">{{ item.overdue_count }} overdue trainings</div>
                  <div class="payment-secondary">Since {{ formatSinceDate(item.oldest_deadline_at) }}</div>
                  <div class="payment-deadline">Oldest debt: {{ formatOverdueAge(item.max_overdue_days) }}</div>
                </div>

                <div class="payment-side">
                  <div class="payment-amount">{{ formatCurrency(item.total_amount) }}</div>
                  <v-chip size="small" color="red" class="payment-chip" dark>
                    Overdue
                  </v-chip>
                </div>
              </article>
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
              <v-alert
                v-if="paymentActionError"
                type="error"
                variant="tonal"
                density="comfortable"
                class="receipt-alert"
              >
                {{ paymentActionError }}
              </v-alert>

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
import { useNotifications } from '../composables/useNotifications'
import { paymentsApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const search = ref('')
const darkMode = ref(false)
const notificationsDialog = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const loading = ref(false)
const activityDialog = ref(false)
const dueDialog = ref(false)
const recordsDialog = ref(false)
const breakdownDialog = ref(false)
const personLedgerDialog = ref(false)
const personDueDialog = ref(false)
const personTransactionsDialog = ref(false)
const receiptDialog = ref(false)
const selectedPaymentId = ref(null)
const selectedPersonFilter = ref(null)
const refundLoadingId = ref(null)
const paymentActionError = ref('')
const darkModeStorageKey = 'app-dark-mode'

const navItems = [
  { label: 'Admin Panel', icon: 'mdi-shield-crown-outline', to: '/admin' },
  { label: 'Admin Users', icon: 'mdi-account-multiple-outline', to: '/admin-users' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/manage-groups' },
  { label: 'Sessions', icon: 'mdi-calendar-clock-outline', to: '/manage-sessions' },
  { label: 'Payments', icon: 'mdi-credit-card-outline', to: '/admin-payments' }
]

const paymentsData = ref({
  summary: {
    total_paid: 0,
    pending: 0,
    overdue: 0,
  },
  due_trainings: [],
  due_monthly_payments: [],
  recent_activity: [],
  spending_breakdown: [],
  payments: [],
})

const avatarFor = (seed, label = seed) => createAvatarDataUri(seed, label)
const { user } = useAuth()
const {
  items: notificationItems,
  loading: notificationsLoading,
  unreadCount,
  loadNotifications,
  markNotificationRead
} = useNotifications()

const profileName = computed(() => {
  if (!user.value) return 'Admin User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const profileEmail = computed(() => user.value?.email ?? 'admin@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)
const normalizedSearch = computed(() => search.value.trim().toLowerCase())
const previewItemsLimit = 3
const dueTrainings = computed(() => paymentsData.value.due_trainings ?? [])
const monthlyDuePayments = computed(() => paymentsData.value.due_monthly_payments ?? [])
const dueItems = computed(() => [...dueTrainings.value, ...monthlyDuePayments.value])
const upcomingDueTrainings = computed(() => dueItems.value.filter((item) => item.status === 'Pending'))
const recentActivity = computed(() => paymentsData.value.recent_activity ?? [])
const paymentRecords = computed(() =>
  (paymentsData.value.payments ?? []).filter((item) => item.status !== 'pending')
)
const selectedPaymentRecord = computed(() => paymentRecords.value.find((item) => item.id === selectedPaymentId.value) ?? null)
const selectedPersonOption = computed(() => personOptions.value.find((item) => item.value === selectedPersonFilter.value) ?? null)
const personOptions = computed(() => {
  const options = []
  const seen = new Set()

  dueItems.value.forEach((item) => {
    if (item.parent_id && item.parent_name) {
      const key = `parent:${item.parent_id}`
      if (!seen.has(key)) {
        seen.add(key)
        options.push({ label: `${item.parent_name} · Parent`, value: key })
      }
    }

    if (item.child_id && item.child_name) {
      const key = `child:${item.child_id}`
      if (!seen.has(key)) {
        seen.add(key)
        options.push({ label: `${item.child_name} · Child`, value: key })
      }
    }
  })

  paymentRecords.value.forEach((item) => {
    if (item.parent_id && item.parent_name) {
      const key = `parent:${item.parent_id}`
      if (!seen.has(key)) {
        seen.add(key)
        options.push({ label: `${item.parent_name} · Parent`, value: key })
      }
    }

    if (item.child_id && item.child_name) {
      const key = `child:${item.child_id}`
      if (!seen.has(key)) {
        seen.add(key)
        options.push({ label: `${item.child_name} · Child`, value: key })
      }
    }
  })

  return options
})

const overviewStats = computed(() => [
  { label: 'Total Paid', value: formatCurrency(paymentsData.value.summary?.total_paid ?? 0) },
  { label: 'Pending', value: formatCurrency(paymentsData.value.summary?.pending ?? 0) },
  { label: 'Overdue', value: formatCurrency(paymentsData.value.summary?.overdue ?? 0) },
  { label: 'Payment Records', value: paymentRecords.value.length },
])

const filteredRecentActivity = computed(() => {
  if (!normalizedSearch.value) return recentActivity.value

  return recentActivity.value.filter((item) =>
    [item.name, item.date, item.method, item.status, item.detail]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(normalizedSearch.value))
  )
})

const filteredDueTrainings = computed(() => {
  if (!normalizedSearch.value) return upcomingDueTrainings.value

  return upcomingDueTrainings.value.filter((item) =>
    [item.name, item.date, item.group, item.trainer, item.child_name, item.status, item.month_label]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(normalizedSearch.value))
  )
})

const filteredPaymentRecords = computed(() => {
  if (!normalizedSearch.value) return paymentRecords.value

  return paymentRecords.value.filter((item) =>
    [item.parent_name, item.child_name, item.method, item.status, item.transaction_id]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(normalizedSearch.value))
  )
})

const personDueItems = computed(() => {
  if (!selectedPersonFilter.value) return []
  return dueItems.value.filter((item) => matchesPersonFilter(item) && item.status === 'Overdue')
})

const personPaymentRecords = computed(() => {
  if (!selectedPersonFilter.value) return []
  return paymentRecords.value.filter((item) => matchesPersonFilter(item))
})

const overdueDebtors = computed(() => {
  const grouped = new Map()

  dueItems.value
    .filter((item) => item.status === 'Overdue')
    .forEach((item) => {
      const key = item.parent_id
        ? `parent:${item.parent_id}:child:${item.child_id}`
        : `child:${item.child_id}`

      if (!grouped.has(key)) {
        grouped.set(key, {
          key,
          parent_id: item.parent_id ?? null,
          parent_name: item.parent_name ?? null,
          child_id: item.child_id ?? null,
          child_name: item.child_name ?? `Child #${item.child_id}`,
          overdue_count: 0,
          total_amount: 0,
          max_overdue_days: 0,
          oldest_deadline_at: item.deadline_at ?? null,
        })
      }

      const debtor = grouped.get(key)
      debtor.overdue_count += 1
      debtor.total_amount += Number(item.amount ?? 0)
      debtor.max_overdue_days = Math.max(debtor.max_overdue_days, Number(item.overdue_days ?? 0))

      if (item.deadline_at && (!debtor.oldest_deadline_at || new Date(item.deadline_at) < new Date(debtor.oldest_deadline_at))) {
        debtor.oldest_deadline_at = item.deadline_at
      }
    })

  return Array.from(grouped.values()).sort((left, right) => {
    if (right.max_overdue_days !== left.max_overdue_days) {
      return right.max_overdue_days - left.max_overdue_days
    }

    return right.total_amount - left.total_amount
  })
})

const previewDueTrainings = computed(() => filteredDueTrainings.value.slice(0, previewItemsLimit))
const previewRecentActivity = computed(() => filteredRecentActivity.value.slice(0, previewItemsLimit))
const previewPaymentRecords = computed(() => filteredPaymentRecords.value.slice(0, previewItemsLimit))
const previewOverdueDebtors = computed(() => overdueDebtors.value.slice(0, previewItemsLimit))

const getStatusColor = (status) => {
  if (status === 'Paid') return 'green'
  if (status === 'Missed') return 'red'
  if (status === 'Returned') return 'blue-grey'
  if (status === 'Refunded') return 'blue-grey'
  if (status === 'Pending') return 'orange'
  if (status === 'Overdue') return 'red'
  if (status === 'Failed') return 'red-darken-1'

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

watch(notificationsDialog, (value) => {
  if (value) {
    loadNotifications(true)
  }
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function formatCurrency(amount) {
  const numericValue = Number(amount ?? 0)

  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: numericValue % 1 === 0 ? 0 : 2,
    maximumFractionDigits: 2,
  }).format(numericValue)
}

function formatOverdueAge(days) {
  const numericDays = Math.floor(Number(days ?? 0))

  if (numericDays <= 0) return 'Due today'
  if (numericDays === 1) return '1 day overdue'

  return `${numericDays} days overdue`
}

function formatDateTime(value) {
  return new Date(value).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

function formatSinceDate(value) {
  if (!value) return 'unknown date'

  return new Date(value).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
  })
}

function capitalize(value) {
  if (!value) return ''
  return value.charAt(0).toUpperCase() + value.slice(1)
}

function formatDueItemTitle(item) {
  return item.type === 'month' ? (item.group || 'Monthly payment') : item.name
}

function formatDueItemMeta(item) {
  return item.type === 'month'
    ? `${item.month_label} · ${item.group}`
    : `${item.date} · ${item.group}`
}

function formatDueItemSecondary(item) {
  return item.type === 'month'
    ? `${item.covered_sessions_count} trainings included`
    : `Coach: ${item.trainer}`
}

function formatDueItemDeadlineLabel(item) {
  return item.type === 'month' ? 'Monthly deadline' : 'Deadline'
}

function matchesPersonFilter(item) {
  if (!selectedPersonFilter.value) return true

  const [type, id] = String(selectedPersonFilter.value).split(':')
  const numericId = Number(id)

  if (type === 'parent') return item.parent_id === numericId
  if (type === 'child') return item.child_id === numericId

  return true
}

function openReceipt(item) {
  paymentActionError.value = ''
  selectedPaymentId.value = item.id
  receiptDialog.value = true
}

function formatReceiptItemName(item) {
  if (item.type === 'month') {
    return 'Monthly payment'
  }

  return item.session_name || 'Training payment'
}

function formatReceiptItemMeta(item) {
  if (item.type === 'month') {
    return item.month ? `Month: ${item.month}` : 'Monthly charge'
  }

  return item.session_name ? `Session: ${item.session_name}` : 'Session payment'
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
  if (!payment) return

  const childName = payment.child_name || `Child #${payment.child_id}`
  const parentName = payment.parent_name || `User #${payment.parent_id}`
  const createdAt = payment.created_at ? formatDateTime(payment.created_at) : 'Not set'
  const itemsMarkup = (payment.items || [])
    .map((item) => `
      <tr>
        <td>${escapeHtml(formatReceiptItemName(item))}</td>
        <td>${escapeHtml(formatReceiptItemMeta(item))}</td>
        <td style="text-align:right;">${escapeHtml(formatCurrency(item.price))}</td>
      </tr>
    `)
    .join('')

  const documentMarkup = `<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Receipt ${escapeHtml(payment.transaction_id || `payment-${payment.id}`)}</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f7fb; color: #172033; margin: 0; padding: 32px; }
    .receipt { max-width: 760px; margin: 0 auto; background: #ffffff; border-radius: 24px; padding: 32px; box-shadow: 0 16px 40px rgba(23,32,51,0.08); }
    .top { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; margin-bottom: 24px; }
    .title { font-size: 28px; font-weight: 700; margin: 0; }
    .subtitle { color: #6f7f96; margin-top: 6px; }
    .status { padding: 8px 14px; border-radius: 999px; background: #e7eefc; color: #184aa8; font-weight: 700; }
    .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px; }
    .card { padding: 16px; border-radius: 18px; background: #f4f7fd; }
    .label { font-size: 12px; letter-spacing: 0.04em; text-transform: uppercase; color: #7b8798; margin-bottom: 8px; }
    .value { font-size: 16px; font-weight: 600; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { padding: 14px 10px; border-bottom: 1px solid #e4eaf5; text-align: left; }
    th { font-size: 12px; letter-spacing: 0.04em; text-transform: uppercase; color: #7b8798; }
    .total { margin-top: 20px; display: flex; justify-content: flex-end; font-size: 20px; font-weight: 700; }
  </style>
</head>
<body>
  <div class="receipt">
    <div class="top">
      <div>
        <h1 class="title">Payment Receipt</h1>
        <div class="subtitle">SportSystem receipt for recorded payment</div>
      </div>
      <div class="status">${escapeHtml(capitalize(payment.status))}</div>
    </div>

    <div class="grid">
      <div class="card">
        <div class="label">Child</div>
        <div class="value">${escapeHtml(childName)}</div>
      </div>
      <div class="card">
        <div class="label">Parent</div>
        <div class="value">${escapeHtml(parentName)}</div>
      </div>
      <div class="card">
        <div class="label">Method</div>
        <div class="value">${escapeHtml(payment.method || 'Not set')}</div>
      </div>
      <div class="card">
        <div class="label">Created</div>
        <div class="value">${escapeHtml(createdAt)}</div>
      </div>
      <div class="card">
        <div class="label">Transaction ID</div>
        <div class="value">${escapeHtml(payment.transaction_id || 'Not set')}</div>
      </div>
      <div class="card">
        <div class="label">Total Amount</div>
        <div class="value">${escapeHtml(formatCurrency(payment.amount))}</div>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Item</th>
          <th>Details</th>
          <th style="text-align:right;">Amount</th>
        </tr>
      </thead>
      <tbody>${itemsMarkup}</tbody>
    </table>

    <div class="total">Total: ${escapeHtml(formatCurrency(payment.amount))}</div>
  </div>
</body>
</html>`

  const file = new Blob([documentMarkup], { type: 'text/html;charset=utf-8' })
  const url = URL.createObjectURL(file)
  const link = document.createElement('a')
  const safeName = childName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')

  link.href = url
  link.download = `receipt-${safeName || 'payment'}-${payment.id}.html`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

async function loadPayments() {
  loading.value = true

  try {
    paymentsData.value = await paymentsApi.list()
  } finally {
    loading.value = false
  }
}

async function refundPayment(item) {
  const confirmed = window.confirm(`Refund payment ${formatCurrency(item.amount)} for ${item.child_name || `Child #${item.child_id}`}?`)
  if (!confirmed) return

  refundLoadingId.value = item.id
  paymentActionError.value = ''

  try {
    await paymentsApi.refund(item.id)
    await loadPayments()
    selectedPaymentId.value = item.id
  } catch (error) {
    const errors = error?.response?.data?.errors ?? {}
    paymentActionError.value = errors.status?.[0] || error?.response?.data?.message || 'Refund failed.'
  } finally {
    refundLoadingId.value = null
  }
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
.admin-payments-page {
  padding: 24px;
}

.admin-payments-shell {
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

.admin-payments-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.admin-payments-panel {
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

.mobile-nav-list,
.nav-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.mobile-drawer-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 14px;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
}

.admin-payments-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
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

.sidebar-card {
  display: flex;
  flex-direction: column;
  min-height: 100%;
  padding: 18px;
  border-radius: 28px;
  background: rgba(245, 250, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-payments-shell-dark .sidebar-card {
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

.admin-payments-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.admin-payments-shell-dark .brand-caption {
  color: #94a6c4;
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

.admin-payments-shell-dark .nav-item {
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

.admin-payments-shell-dark .mobile-header-card {
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

.admin-payments-shell-dark .mobile-menu-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.mobile-brand-inline {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.mobile-brand-icon {
  width: 46px;
  height: 46px;
  border-radius: 15px;
}

.mobile-brand-copy {
  min-width: 0;
}

.mobile-header-actions,
.topbar-tools {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mobile-utility-card {
  padding: 16px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-payments-shell-dark .mobile-utility-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-profile-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
}

.topbar-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 18px 22px;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-payments-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.search-wrap {
  flex: 1;
  min-width: 0;
}

.search-shell {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 58px;
  padding: 0 18px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.admin-payments-shell-dark .search-shell {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.search-shell-icon {
  color: #111827;
}

.admin-payments-shell-dark .search-shell-icon {
  color: #edf4ff;
}

.search-field {
  flex: 1;
}

.search-field :deep(input) {
  color: #111827;
  opacity: 1;
}

.admin-payments-shell-dark .search-field :deep(input) {
  color: #edf4ff;
}

.search-field :deep(input::placeholder) {
  color: #111827;
  opacity: 1;
}

.admin-payments-shell-dark .search-field :deep(input::placeholder) {
  color: #edf4ff;
}

.search-field :deep(.v-field__input) {
  padding-top: 0;
  padding-bottom: 0;
  min-height: auto;
}

.icon-badge-wrap {
  position: relative;
}

.top-icon-btn {
  width: 54px;
  height: 54px;
  border-radius: 18px;
  color: #111827;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.admin-payments-shell-dark .top-icon-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.top-icon-btn-active {
  color: #0f5fe3;
}

.icon-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  display: grid;
  place-items: center;
  border-radius: 999px;
  background: #1677ff;
  color: white;
  font-size: 0.72rem;
  font-weight: 700;
}

.profile-pill {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.admin-payments-shell-dark .profile-pill {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.mobile-profile-pill {
  flex: 1;
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 700;
  color: #1c2438;
}

.admin-payments-shell-dark .profile-name {
  color: #f3f7ff;
}

.profile-email {
  margin-top: 2px;
  font-size: 0.86rem;
  color: #78859a;
}

.admin-payments-shell-dark .profile-email {
  color: #94a6c4;
}

.payments-card {
  padding: 26px;
  border-radius: 30px;
  background: rgba(249, 252, 255, 0.72);
  border: 1px solid rgba(255, 255, 255, 0.72);
}

.admin-payments-shell-dark .payments-card {
  background: rgba(18, 27, 43, 0.86);
  border-color: rgba(74, 92, 126, 0.42);
}

.payments-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.payments-title {
  margin: 0;
  font-size: 2.1rem;
  line-height: 1.1;
  color: #172033;
}

.admin-payments-shell-dark .payments-title {
  color: #f3f7ff;
}

.payments-subtitle,
.summary-label,
.list-title,
.payment-meta,
.payment-secondary,
.payment-deadline {
  color: #7b8798;
}

.admin-payments-shell-dark .payments-subtitle,
.admin-payments-shell-dark .summary-label,
.admin-payments-shell-dark .list-title,
.admin-payments-shell-dark .payment-meta,
.admin-payments-shell-dark .payment-secondary,
.admin-payments-shell-dark .payment-deadline {
  color: #94a6c4;
}

.overview-stats-grid,
.lists-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.overview-stats-grid {
  margin-bottom: 22px;
}

.finder-card {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(300px, 420px);
  gap: 16px;
  align-items: center;
  margin-bottom: 22px;
  padding: 18px 20px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(229, 236, 246, 0.94);
}

.admin-payments-shell-dark .finder-card {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.person-ledger-btn {
  justify-self: end;
  min-height: 48px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 700;
}

.person-field :deep(.v-field) {
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.72);
  box-shadow: inset 0 0 0 1px rgba(223, 232, 246, 0.95);
}

.person-field :deep(.v-field--focused) {
  box-shadow:
    inset 0 0 0 1px rgba(74, 144, 255, 0.72),
    0 0 0 4px rgba(22, 119, 255, 0.08);
}

.person-field :deep(input),
.person-field :deep(.v-field__input),
.person-field :deep(.v-select__selection-text) {
  color: #172033;
}

.admin-payments-shell-dark .person-field :deep(.v-field) {
  background: rgba(17, 25, 40, 0.86);
  box-shadow: inset 0 0 0 1px rgba(64, 82, 116, 0.72);
}

.admin-payments-shell-dark .person-field :deep(input),
.admin-payments-shell-dark .person-field :deep(.v-field__input),
.admin-payments-shell-dark .person-field :deep(.v-select__selection-text) {
  color: #eef4ff;
}

.overview-stat-card,
.list-card {
  padding: 20px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(229, 236, 246, 0.94);
}

.admin-payments-shell-dark .overview-stat-card,
.admin-payments-shell-dark .list-card {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.summary-value {
  margin-top: 8px;
  font-size: 1.9rem;
  font-weight: 700;
  color: #172033;
}

.admin-payments-shell-dark .summary-value,
.admin-payments-shell-dark .payment-name,
.admin-payments-shell-dark .payment-amount {
  color: #f3f7ff;
}

.state-wrap,
.empty-state {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 180px;
  padding: 18px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.75);
  border: 1px dashed rgba(194, 206, 225, 0.92);
  color: #5f6f88;
}

.admin-payments-shell-dark .state-wrap,
.admin-payments-shell-dark .empty-state {
  background: rgba(13, 20, 34, 0.72);
  border-color: rgba(78, 97, 132, 0.58);
  color: #aac0df;
}

.loading-state {
  flex-direction: column;
  gap: 14px;
}

.list-card {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.list-header,
.list-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 14px;
}

.show-all-btn {
  border: 0;
  padding: 0;
  background: transparent;
  color: #1677ff;
  font-weight: 700;
  cursor: pointer;
}

.list-wrap,
.dialog-list-wrap {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.payment-item {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 14px;
  padding: 14px 16px;
  border-radius: 18px;
  background: rgba(241, 246, 255, 0.78);
  border: 1px solid rgba(223, 232, 246, 0.94);
}

.admin-payments-shell-dark .payment-item,
.list-dialog-card-dark .payment-item {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.payment-main {
  min-width: 0;
}

.payment-name {
  font-weight: 700;
  color: #172033;
}

.payment-secondary,
.payment-deadline {
  margin-top: 4px;
}

.payment-side {
  display: grid;
  justify-items: end;
  align-content: start;
  min-width: 176px;
  gap: 10px;
}

.payment-actions {
  display: flex;
  align-items: center;
  gap: 6px;
  justify-content: flex-end;
}

.payment-actions-record {
  flex-direction: column;
  align-items: flex-end;
}

.record-action-btn {
  min-height: 28px;
  height: 28px;
  padding-inline: 0;
  border-radius: 12px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 600;
  line-height: 1;
}

.receipt-action-btn {
  color: #172033;
  font-size: 0.94rem;
  justify-content: flex-end;
}

.payment-chip-record {
  align-self: end;
}

.admin-payments-shell-dark .receipt-action-btn,
.list-dialog-card-dark .receipt-action-btn {
  color: #eef4ff;
}

.payment-amount {
  font-size: 1rem;
  font-weight: 700;
  color: #172033;
}

.payment-chip {
  font-weight: 700;
}

.dialog-card {
  border-radius: 28px;
  overflow: hidden;
}

.list-dialog-card {
  border: 1px solid rgba(223, 232, 246, 0.95);
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.99), rgba(235, 243, 255, 0.98));
  box-shadow: 0 28px 70px rgba(79, 106, 154, 0.22);
}

.list-dialog-card-dark {
  border-color: rgba(66, 84, 118, 0.64);
  background: linear-gradient(180deg, rgba(16, 23, 37, 0.99), rgba(22, 31, 48, 0.98));
  box-shadow: 0 28px 80px rgba(3, 8, 20, 0.55);
}

.list-dialog-header {
  padding: 24px 24px 18px;
  border-bottom: 1px solid rgba(219, 230, 246, 0.78);
}

.list-dialog-card-dark .list-dialog-header {
  border-bottom-color: rgba(64, 82, 116, 0.68);
}

.list-dialog-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: #172033;
}

.list-dialog-card-dark .list-dialog-title {
  color: #f3f7ff;
}

.list-dialog-subtitle {
  margin-top: 4px;
  color: #7b8798;
}

.list-dialog-card-dark .list-dialog-subtitle {
  color: #94a6c4;
}

.dialog-list-wrap {
  max-height: min(70vh, 620px);
  overflow-y: auto;
  padding: 18px 24px 24px;
}

.receipt-body {
  padding: 18px 24px 24px;
}

.receipt-alert {
  margin-bottom: 16px;
}

.receipt-summary-card {
  padding: 18px;
  border-radius: 20px;
  background: rgba(241, 246, 255, 0.78);
  border: 1px solid rgba(223, 232, 246, 0.94);
}

.list-dialog-card-dark .receipt-summary-card {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.receipt-top-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 14px;
}

.receipt-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  margin-top: 16px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.info-label {
  color: #7b8798;
}

.info-value {
  font-weight: 600;
  color: #172033;
}

.list-dialog-card-dark .info-label {
  color: #94a6c4;
}

.list-dialog-card-dark .info-value {
  color: #f3f7ff;
}

.receipt-items-title {
  margin-top: 18px;
  margin-bottom: 12px;
}

.receipt-items-wrap {
  max-height: none;
  padding: 0;
}

.receipt-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 18px;
}

.dialog-primary-btn {
  min-height: 46px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 700;
}

.person-ledger-body {
  padding: 18px 24px 24px;
  max-height: min(74vh, 720px);
  overflow-y: auto;
  scrollbar-gutter: stable;
  scrollbar-width: thin;
  scrollbar-color: rgba(122, 146, 186, 0.72) rgba(226, 235, 247, 0.68);
}

.person-ledger-summary {
  margin-top: 16px;
  padding: 14px 16px;
  border-radius: 18px;
  background: rgba(241, 246, 255, 0.78);
  border: 1px solid rgba(223, 232, 246, 0.94);
}

.list-dialog-card-dark .person-ledger-summary {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.person-ledger-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
  margin-top: 18px;
}

.person-dialog-list {
  max-height: min(48vh, 420px);
  padding: 0;
  scrollbar-width: thin;
  scrollbar-color: rgba(122, 146, 186, 0.72) rgba(226, 235, 247, 0.68);
}

.person-empty-state {
  min-height: 160px;
}

.person-ledger-body::-webkit-scrollbar,
.person-dialog-list::-webkit-scrollbar {
  width: 10px;
}

.person-ledger-body::-webkit-scrollbar-track,
.person-dialog-list::-webkit-scrollbar-track {
  border-radius: 999px;
  background: rgba(226, 235, 247, 0.68);
}

.person-ledger-body::-webkit-scrollbar-thumb,
.person-dialog-list::-webkit-scrollbar-thumb {
  border: 2px solid rgba(226, 235, 247, 0.68);
  border-radius: 999px;
  background: linear-gradient(180deg, rgba(124, 150, 195, 0.92), rgba(96, 124, 173, 0.94));
}

.person-ledger-body::-webkit-scrollbar-thumb:hover,
.person-dialog-list::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, rgba(98, 134, 196, 0.96), rgba(73, 106, 168, 0.98));
}

.list-dialog-card-dark .person-ledger-body,
.list-dialog-card-dark .person-dialog-list {
  scrollbar-color: rgba(101, 132, 189, 0.78) rgba(27, 38, 58, 0.78);
}

.list-dialog-card-dark .person-ledger-body::-webkit-scrollbar-track,
.list-dialog-card-dark .person-dialog-list::-webkit-scrollbar-track {
  background: rgba(27, 38, 58, 0.78);
}

.list-dialog-card-dark .person-ledger-body::-webkit-scrollbar-thumb,
.list-dialog-card-dark .person-dialog-list::-webkit-scrollbar-thumb {
  border-color: rgba(27, 38, 58, 0.78);
  background: linear-gradient(180deg, rgba(86, 121, 189, 0.96), rgba(54, 87, 146, 0.98));
}

.list-dialog-card-dark .person-ledger-body::-webkit-scrollbar-thumb:hover,
.list-dialog-card-dark .person-dialog-list::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, rgba(104, 140, 211, 0.98), rgba(66, 103, 173, 0.98));
}

@media (max-width: 1180px) {
  .overview-stats-grid,
  .lists-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 1024px) {
  .admin-payments-page {
    padding: 16px;
  }

  .admin-payments-shell {
    overflow: hidden;
  }

  .admin-payments-panel {
    display: block;
    padding: 16px;
  }

  .sidebar-card,
  .topbar-card .search-wrap {
    display: none;
  }

  .mobile-header-card,
  .mobile-utility-card {
    display: block;
  }

  .mobile-header-card {
    display: flex;
  }

  .topbar-card {
    justify-content: flex-end;
    padding: 16px;
  }

  .topbar-tools .profile-pill,
  .logout-btn {
    display: none;
  }
}

@media (max-width: 768px) {
  .payments-card {
    padding: 20px;
  }

  .finder-card,
  .receipt-grid,
  .person-ledger-grid {
    grid-template-columns: 1fr;
  }

  .person-ledger-body {
    max-height: min(80vh, 640px);
    padding-right: 18px;
  }

  .mobile-profile-row,
  .payment-item,
  .receipt-top-row {
    flex-direction: column;
    align-items: stretch;
  }

  .person-ledger-btn {
    justify-self: stretch;
  }

  .payment-side {
    min-width: 0;
    align-items: flex-start;
    justify-items: start;
  }

  .payment-actions {
    justify-content: flex-start;
  }

  .payment-actions-record {
    align-items: flex-start;
  }
}

@media (max-width: 560px) {
  .admin-payments-page {
    padding: 10px;
  }

  .admin-payments-panel {
    padding: 10px;
  }

  .payments-card {
    padding: 16px;
    border-radius: 24px;
  }

  .topbar-card,
  .mobile-header-card,
  .mobile-utility-card {
    border-radius: 20px;
  }

  .payments-title {
    font-size: 1.75rem;
  }

  .summary-value {
    font-size: 1.6rem;
  }
}
</style>
