<template>
  <v-app>

    <v-navigation-drawer app width="240" color="#f5f7fb">
      <v-list>

        <v-list-item class="mb-6">
          <v-icon size="28" color="primary">mdi-school</v-icon>
          <span class="ml-3 font-weight-bold text-h6">SportSystem</span>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend>
            <v-icon>mdi-home-outline</v-icon>
          </template>
          <v-list-item-title>Home</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend>
            <v-icon>mdi-calendar</v-icon>
          </template>
          <v-list-item-title>Schedule</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend>
            <v-icon>mdi-account-group</v-icon>
          </template>
          <v-list-item-title>Groups</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend>
            <v-icon>mdi-check-circle-outline</v-icon>
          </template>
          <v-list-item-title>Attendance</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend>
            <v-icon>mdi-credit-card-outline</v-icon>
          </template>
          <v-list-item-title>Payments</v-list-item-title>
        </v-list-item>

        <v-list-item link>
          <template v-slot:prepend>
            <v-icon>mdi-bell-outline</v-icon>
          </template>
          <v-list-item-title>Notifications</v-list-item-title>
        </v-list-item>

      </v-list>
    </v-navigation-drawer>


    <v-app-bar app flat color="white">

      <v-text-field
        prepend-inner-icon="mdi-magnify"
        label="Search"
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


    <v-main>
      <v-container>

        <div class="d-flex align-center justify-space-between mb-6">

          <div>
            <h2 class="text-h5 font-weight-bold">Schedule</h2>
            <span class="text-grey">Today · March 12</span>
          </div>

          <v-btn color="primary" prepend-icon="mdi-plus">
            Create training
          </v-btn>

        </div>


        <div class="d-flex align-center justify-space-between mb-6">

          <v-tabs v-model="tab" color="primary">
            <v-tab value="upcoming">Upcoming</v-tab>
            <v-tab value="pending">Pending</v-tab>
            <v-tab value="past">Past</v-tab>
          </v-tabs>

          <div>

            <v-btn
              size="small"
              variant="outlined"
              class="mr-2"
              @click="sortAZ"
            >
              A-Z
            </v-btn>

            <v-btn
              size="small"
              variant="outlined"
              class="mr-2"
              @click="sortZA"
            >
              Z-A
            </v-btn>

            <v-btn
              size="small"
              variant="outlined"
              @click="sortTime"
            >
              Time
            </v-btn>

          </div>

        </div>


        <v-card
          v-for="training in trainings"
          :key="training.id"
          class="pa-4 mb-4"
          elevation="2"
        >

          <div class="d-flex justify-space-between">

            <div>

              <div class="text-caption text-grey">
                {{ training.time }}
              </div>

              <div class="text-h6 font-weight-medium mt-1">
                {{ training.title }}
              </div>

              <div class="text-grey">
                {{ training.description }}
              </div>

            </div>

            <div class="text-right">

              <div class="text-caption text-grey">
                Trainer
              </div>

              <div class="d-flex align-center">

                <v-avatar size="32" class="mr-2">
                  <img src="https://i.pravatar.cc/100">
                </v-avatar>

                <span>{{ training.trainer }}</span>

              </div>

            </div>

          </div>

          <div class="mt-4 d-flex align-center">

            <span class="text-grey mr-4">Children</span>

            <v-avatar
              v-for="i in 4"
              :key="i"
              size="32"
              class="mr-1"
            >
              <img :src="`https://i.pravatar.cc/40?img=${i}`">
            </v-avatar>

            <v-avatar color="primary" size="32">
              +6
            </v-avatar>

          </div>

          <div class="mt-4 d-flex justify-end">

            <v-btn
              variant="outlined"
              color="error"
              class="mr-2"
            >
              Cancel
            </v-btn>

            <v-btn
              variant="outlined"
              color="primary"
              class="mr-2"
            >
              Edit
            </v-btn>

            <v-btn color="primary">
              Attendance
            </v-btn>

          </div>

        </v-card>

      </v-container>
    </v-main>

  </v-app>
</template>


<script setup>
import { ref } from "vue"

const tab = ref("upcoming")

const trainings = ref([
{
id:1,
title:"Football Training U14",
description:"Speed and coordination practice",
trainer:"Jānis Ozols",
time:"17:00"
},
{
id:2,
title:"Basketball Training",
description:"",
trainer:"Alex Johnson",
time:"19:00"
},
{
id:3,
title:"Running Practice",
description:"",
trainer:"Mike Smith",
time:"15:00"
}
])

function sortAZ(){
trainings.value.sort((a,b)=>
a.title.localeCompare(b.title)
)
}

function sortZA(){
trainings.value.sort((a,b)=>
b.title.localeCompare(a.title)
)
}

function sortTime(){
trainings.value.sort((a,b)=>
a.time.localeCompare(b.time)
)
}
</script>


<style>
body{
background:#f3f5fa;
}
</style>