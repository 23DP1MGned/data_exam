<template>
  <v-app>
    <v-navigation-drawer app width="240" color="#f5f7fb">
      <v-list>
        <v-list-item class="mb-6">
          <v-icon size="28" color="primary">mdi-school</v-icon>
          <span class="ml-3 font-weight-bold text-h6">SportSystem</span>
        </v-list-item>

        <v-list-item link to="/dashboard">
          <template #prepend><v-icon>mdi-home-outline</v-icon></template>
          <v-list-item-title>Home</v-list-item-title>
        </v-list-item>

        <v-list-item link to="/schedule">
          <template #prepend><v-icon>mdi-calendar</v-icon></template>
          <v-list-item-title>Schedule</v-list-item-title>
        </v-list-item>

        <v-list-item link to="/groups">
          <template #prepend><v-icon>mdi-account-group</v-icon></template>
          <v-list-item-title>Groups</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template #prepend><v-icon>mdi-check-circle-outline</v-icon></template>
          <v-list-item-title>Attendance</v-list-item-title>
        </v-list-item>

        <v-list-item link to="/payments" active color="primary">
          <template #prepend><v-icon>mdi-credit-card-outline</v-icon></template>
          <v-list-item-title>Payments</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template #prepend><v-icon>mdi-bell-outline</v-icon></template>
          <v-list-item-title>Notifications</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar app flat color="white">
      <v-text-field
        v-model="search"
        prepend-inner-icon="mdi-magnify"
        label="Search payments"
        variant="outlined"
        density="compact"
        hide-details
        class="mr-6"
        style="max-width: 300px"
      />

      <v-spacer />

      <v-btn icon>
        <v-icon>mdi-bell-outline</v-icon>
      </v-btn>

      <v-avatar class="ml-4">
        <img src="https://i.pravatar.cc/40" alt="User avatar">
      </v-avatar>

      <span class="ml-3 mr-2 font-weight-medium">Maksims</span>
    </v-app-bar>

    <v-main>
      <v-container class="py-6">
        <div class="d-flex align-center justify-space-between mb-6">
          <div>
            <h2 class="text-h5 font-weight-bold">Payments</h2>
            <span class="text-grey">Overview of balances, transactions and recent activity</span>
          </div>

          <v-btn color="primary" prepend-icon="mdi-cog-outline" rounded>
            Manage
          </v-btn>
        </div>

        <v-row class="mb-6" dense>
          <v-col cols="12" md="4">
            <v-card class="pa-4 rounded-xl payment-summary-card" elevation="2">
              <div class="text-caption text-grey">Total Paid</div>
              <div class="text-h6 font-weight-bold mt-2">€1240</div>
            </v-card>
          </v-col>

          <v-col cols="12" md="4">
            <v-card class="pa-4 rounded-xl payment-summary-card" elevation="2">
              <div class="text-caption text-grey">Pending</div>
              <div class="text-h6 font-weight-bold mt-2">€320</div>
            </v-card>
          </v-col>

          <v-col cols="12" md="4">
            <v-card class="pa-4 rounded-xl payment-summary-card" elevation="2">
              <div class="text-caption text-grey">Overdue</div>
              <div class="text-h6 font-weight-bold mt-2">€90</div>
            </v-card>
          </v-col>
        </v-row>

        <v-row class="mb-6" dense>
          <v-col cols="12" md="6">
            <v-card class="pa-6 rounded-xl" elevation="2">
              <div class="text-subtitle-1 font-weight-medium mb-4">Balance</div>
              <div class="text-h5 font-weight-bold mb-4">€1560</div>
              <div class="text-body-2 text-grey mb-6">
                Current available balance for upcoming trainings and subscriptions.
              </div>
              <v-btn color="primary" rounded>Manage</v-btn>
            </v-card>
          </v-col>

          <v-col cols="12" md="6">
            <v-card class="pa-6 rounded-xl" elevation="2">
              <div class="text-subtitle-1 font-weight-medium mb-4">Income</div>
              <div class="chart-placeholder"></div>
            </v-card>
          </v-col>
        </v-row>

        <v-row class="mb-6" dense>
          <v-col cols="12" md="6">
            <v-card class="pa-4 rounded-xl" elevation="2">
              <div class="text-subtitle-1 font-weight-medium mb-4">Transactions</div>

              <v-list bg-color="transparent">
                <v-list-item
                  v-for="item in filteredTransactions"
                  :key="item.id"
                  class="px-0 transaction-item"
                >
                  <v-list-item-title>{{ item.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ item.date }}</v-list-item-subtitle>

                  <template #append>
                    <div class="text-right">
                      <div class="font-weight-medium">€{{ item.amount }}</div>
                      <v-chip size="small" :color="getStatusColor(item.status)" class="mt-1" dark>
                        {{ item.status }}
                      </v-chip>
                    </div>
                  </template>
                </v-list-item>
              </v-list>

            </v-card>
          </v-col>

          <v-col cols="12" md="6">
            <v-card class="pa-4 rounded-xl" elevation="2">
              <div class="text-subtitle-1 font-weight-medium mb-4">Recent Payments</div>

              <v-list bg-color="transparent">
                <v-list-item
                  v-for="item in filteredPayments"
                  :key="item.id"
                  class="px-0 transaction-item"
                >
                  <v-list-item-title>{{ item.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ item.method }}</v-list-item-subtitle>

                  <template #append>
                    <div class="text-right">
                      <div class="font-weight-medium">€{{ item.amount }}</div>
                      <v-chip size="small" :color="getStatusColor(item.status)" class="mt-1" dark>
                        {{ item.status }}
                      </v-chip>
                    </div>
                  </template>
                </v-list-item>
              </v-list>

            </v-card>
          </v-col>
        </v-row>

      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')

const transactions = ref([
  { id: 1, name: 'Football Training', date: '12 Mar', amount: 40, status: 'Paid' },
  { id: 2, name: 'Basketball', date: '10 Mar', amount: 35, status: 'Pending' },
  { id: 3, name: 'Swimming', date: '8 Mar', amount: 50, status: 'Overdue' }
])

const payments = ref([
  { id: 1, name: 'John Doe', method: 'Card', amount: 40, status: 'Paid' },
  { id: 2, name: 'Anna Smith', method: 'Cash', amount: 35, status: 'Pending' },
  { id: 3, name: 'Mark Lee', method: 'Card', amount: 50, status: 'Paid' }
])

const normalizedSearch = computed(() => search.value.trim().toLowerCase())

const filteredTransactions = computed(() => {
  if (!normalizedSearch.value) return transactions.value

  return transactions.value.filter((item) => {
    return [item.name, item.date, item.status].some((value) =>
      value.toLowerCase().includes(normalizedSearch.value)
    )
  })
})

const filteredPayments = computed(() => {
  if (!normalizedSearch.value) return payments.value

  return payments.value.filter((item) => {
    return [item.name, item.method, item.status].some((value) =>
      value.toLowerCase().includes(normalizedSearch.value)
    )
  })
})

const getStatusColor = (status) => {
  if (status === 'Paid') return 'green'
  if (status === 'Pending') return 'orange'
  if (status === 'Overdue') return 'red'

  return 'grey'
}
</script>

<style scoped>
.chart-placeholder {
  height: 160px;
  border-radius: 12px;
  background: linear-gradient(135deg, #e3e8ff, #f5f7fb);
}

.payment-summary-card {
  min-height: 96px;
}

.transaction-item + .transaction-item {
  border-top: 1px solid rgba(15, 23, 42, 0.08);
}
</style>
