<template>
  <v-app>

    <!-- SIDEBAR -->
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

        <v-list-item link>
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


    <!-- MAIN -->
    <v-main>
      <v-container>

        <!-- HEADER -->
        <div class="d-flex align-center justify-space-between mb-6">
          <div>
            <h2 class="text-h5 font-weight-bold">Schedule</h2>
            <span class="text-grey">Today · March 12</span>
          </div>

          <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreate">
            Create training
          </v-btn>
        </div>


        <!-- FILTER + SORT -->
        <div class="d-flex align-center justify-space-between mb-6">

          <v-tabs v-model="tab" color="primary">
            <v-tab value="upcoming">Upcoming</v-tab>
            <v-tab value="pending">Pending</v-tab>
            <v-tab value="past">Past</v-tab>
          </v-tabs>

          <div>
            <v-btn size="small" variant="outlined" class="mr-2" @click="sortAZ">A-Z</v-btn>
            <v-btn size="small" variant="outlined" class="mr-2" @click="sortZA">Z-A</v-btn>
            <v-btn size="small" variant="outlined" @click="sortTime">Time</v-btn>
          </div>

        </div>


        <!-- CARDS -->
        <v-card
          v-for="training in trainings"
          :key="training.id"
          class="pa-4 mb-4"
          elevation="2"
        >

          <div class="d-flex justify-space-between">

            <div>
              <div class="text-caption text-grey">
                {{ `From ${training.start} — To ${training.end}` }}
              </div>
              <div class="text-h6 font-weight-medium mt-1">
                {{ training.title }}
              </div>
              <div class="text-grey">
                {{ training.description }}
              </div>
            </div>

            <div class="text-right">
              <div class="text-caption text-grey">Trainer</div>
              <div class="d-flex align-center">
                <v-avatar size="32" class="mr-2">
                  <img src="https://i.pravatar.cc/100">
                </v-avatar>
                <span>{{ training.trainer }}</span>
              </div>
            </div>

          </div>

          <div class="mt-4 d-flex justify-end">
            <v-btn variant="outlined" color="error" class="mr-2">Cancel</v-btn>

            <v-btn
              variant="outlined"
              color="primary"
              class="mr-2"
              @click="openEdit(training)"
            >
              Edit
            </v-btn>

            <v-btn color="primary">Attendance</v-btn>
          </div>

        </v-card>


        <!-- DIALOG -->
        <v-dialog v-model="dialog" max-width="500">
          <v-card>

            <v-card-title>
              {{ isEditing ? "Edit Training" : "Create Training" }}
            </v-card-title>

            <v-card-text>
              <v-text-field label="Title" v-model="newTraining.title" />
              <v-text-field label="Start time" v-model="newTraining.start" />
              <v-text-field label="End time" v-model="newTraining.end" />
              <v-text-field label="Trainer" v-model="newTraining.trainer" />
              <v-textarea label="Description" v-model="newTraining.description" />
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn @click="dialog = false">Cancel</v-btn>
              <v-btn color="primary" @click="saveTraining">
                {{ isEditing ? "Update" : "Save" }}
              </v-btn>
            </v-card-actions>

          </v-card>
        </v-dialog>

      </v-container>
    </v-main>

  </v-app>
</template>


<script setup>
import { ref } from "vue"

const tab = ref("upcoming")

const dialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const trainings = ref([
{
id:1,
title:"Football Training U14",
description:"Speed and coordination practice",
trainer:"Jānis Ozols",
start:"17:00",
end:"18:30"
},
{
id:2,
title:"Basketball Training",
description:"",
trainer:"Alex Johnson",
start:"19:00",
end:"20:00"
},
{
id:3,
title:"Running Practice",
description:"",
trainer:"Mike Smith",
start:"15:00",
end:"16:00"
}
])

const newTraining = ref({
title:"",
start:"",
end:"",
trainer:"",
description:""
})

function openCreate(){
isEditing.value = false
editingId.value = null

newTraining.value = {
title:"",
start:"",
end:"",
trainer:"",
description:""
}

dialog.value = true
}

function openEdit(training){
isEditing.value = true
editingId.value = training.id

newTraining.value = {
title: training.title,
start: training.start,
end: training.end,
trainer: training.trainer,
description: training.description
}

dialog.value = true
}

function saveTraining(){

if(isEditing.value){

const index = trainings.value.findIndex(t => t.id === editingId.value)

if(index !== -1){
trainings.value[index] = {
...trainings.value[index],
title: newTraining.value.title,
description: newTraining.value.description,
trainer: newTraining.value.trainer,
start: newTraining.value.start,
end: newTraining.value.end
}
}

}else{

trainings.value.push({
id: Date.now(),
title: newTraining.value.title,
description: newTraining.value.description,
trainer: newTraining.value.trainer,
start: newTraining.value.start,
end: newTraining.value.end
})

}

dialog.value = false
isEditing.value = false

newTraining.value = {
title:"",
start:"",
end:"",
trainer:"",
description:""
}
}

function sortAZ(){
trainings.value.sort((a,b)=> a.title.localeCompare(b.title))
}

function sortZA(){
trainings.value.sort((a,b)=> b.title.localeCompare(a.title))
}

function sortTime(){
trainings.value.sort((a,b)=> a.start.localeCompare(b.start))
}
</script>


<style>
body{
background:#f3f5fa;
}
</style>