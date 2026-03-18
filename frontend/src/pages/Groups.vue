<template>
  <v-app>

    <v-navigation-drawer app width="240" color="#f5f7fb">
      <v-list>

        <v-list-item class="mb-6">
          <v-icon size="28" color="primary">mdi-school</v-icon>
          <span class="ml-3 font-weight-bold text-h6">SportSystem</span>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend><v-icon>mdi-home-outline</v-icon></template>
          <v-list-item-title>Home</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend><v-icon>mdi-calendar</v-icon></template>
          <v-list-item-title>Schedule</v-list-item-title>
        </v-list-item>

        <v-list-item active>
          <template v-slot:prepend><v-icon>mdi-account-group</v-icon></template>
          <v-list-item-title>Groups</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend><v-icon>mdi-check-circle-outline</v-icon></template>
          <v-list-item-title>Attendance</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend><v-icon>mdi-credit-card-outline</v-icon></template>
          <v-list-item-title>Payments</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend><v-icon>mdi-bell-outline</v-icon></template>
          <v-list-item-title>Notifications</v-list-item-title>
        </v-list-item>

      </v-list>
    </v-navigation-drawer>


    <!-- TOP BAR -->
    <v-app-bar app flat color="white">

      <v-text-field
        v-model="search"
        prepend-inner-icon="mdi-magnify"
        label="Search groups"
        variant="outlined"
        density="compact"
        hide-details
        class="mr-6"
        style="max-width:300px"
      />

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon>mdi-bell-outline</v-icon>
      </v-btn>

      <v-avatar class="ml-4">
        <img src="https://i.pravatar.cc/40">
      </v-avatar>

      <span class="ml-3 mr-2 font-weight-medium">Maksims</span>

    </v-app-bar>


    <!-- MAIN -->
    <v-main>
      <v-container>

        <!-- HEADER -->
        <div class="d-flex align-center justify-space-between mb-6">
          <div>
            <h2 class="text-h5 font-weight-bold">My Groups</h2>
            <span class="text-grey">Groups you manage</span>
          </div>

          <v-btn color="primary" prepend-icon="mdi-plus">
            Create group
          </v-btn>
        </div>


        <!-- SORT -->
        <div class="d-flex justify-end mb-6">
          <v-btn size="small" variant="outlined" class="mr-2" @click="sortAZ">A-Z</v-btn>
          <v-btn size="small" variant="outlined" @click="sortZA">Z-A</v-btn>
        </div>


        <!-- CARDS -->
        <v-row>
          <v-col
            v-for="group in filteredGroups"
            :key="group.id"
            cols="12"
            sm="6"
            md="4"
            lg="3"
          >
            <v-card class="group-card pa-5" elevation="2">

              <!-- TOP -->
              <div class="d-flex align-center mb-4">
                <v-avatar size="44" class="mr-3">
                  <img :src="group.avatar">
                </v-avatar>

                <div>
                  <div class="trainer">
                    {{ group.trainer }}
                  </div>
                  <div class="section">
                    {{ group.section }}
                  </div>
                </div>
              </div>

              <!-- INFO -->
              <div class="info-grid">

                <div class="info-item">
                  <div class="label">Students</div>
                  <div class="value">{{ group.students }}</div>
                </div>

                <div class="info-item">
                  <div class="label">Days</div>
                  <div class="value">{{ group.days }}</div>
                </div>

                <div class="info-item">
                  <div class="label">Time</div>
                  <div class="value">{{ group.time }}</div>
                </div>

                <div class="info-item">
                  <div class="label">Price</div>
                  <div class="value">{{ group.price }} € / month</div>
                </div>

                <div class="info-item">
                  <div class="label">Attendance</div>
                  <div class="value">{{ group.attendance }}%</div>
                </div>

              </div>

            </v-card>
          </v-col>
        </v-row>

      </v-container>
    </v-main>

  </v-app>
</template>


<script setup>
import { ref, computed } from "vue"

const search = ref("")

const groups = ref([
{ id:1, trainer:"Jānis Ozols", section:"Football U14", days:"Mon / Wed", time:"17:00", students:12, price:50, attendance:85, avatar:"https://i.pravatar.cc/100?img=1" },
{ id:2, trainer:"Alex Johnson", section:"Basketball", days:"Tue / Thu", time:"19:00", students:10, price:60, attendance:78, avatar:"https://i.pravatar.cc/100?img=2" },
{ id:3, trainer:"Mike Smith", section:"Running", days:"Mon / Fri", time:"15:00", students:8, price:40, attendance:92, avatar:"https://i.pravatar.cc/100?img=3" },
{ id:4, trainer:"Anna Petrova", section:"Dance", days:"Sat", time:"16:00", students:14, price:45, attendance:88, avatar:"https://i.pravatar.cc/100?img=4" },
{ id:5, trainer:"David Brown", section:"Boxing", days:"Mon / Thu", time:"18:00", students:9, price:55, attendance:80, avatar:"https://i.pravatar.cc/100?img=5" },
{ id:6, trainer:"Olga Ivanova", section:"Yoga", days:"Tue / Sat", time:"10:00", students:11, price:35, attendance:95, avatar:"https://i.pravatar.cc/100?img=6" }
])

const filteredGroups = computed(() => {
return groups.value.filter(g =>
g.section.toLowerCase().includes(search.value.toLowerCase())
)
})

function sortAZ(){
groups.value.sort((a,b)=> a.section.localeCompare(b.section))
}

function sortZA(){
groups.value.sort((a,b)=> b.section.localeCompare(a.section))
}
</script>


<style>
body {
  background: #f3f5fa;
}

.group-card {
  border-radius: 16px;
  transition: 0.25s;
}

.group-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.06);
}

.trainer {
  font-weight: 600;
  font-size: 15px;
}

.section {
  font-size: 13px;
  color: #777;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 16px;
}

.label {
  font-size: 11px;
  color: #999;
}

.value {
  font-size: 14px;
  font-weight: 500;
}
</style>