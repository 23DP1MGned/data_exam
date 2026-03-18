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


    <v-app-bar app flat color="white">

      <v-text-field
        v-model="search"
        prepend-inner-icon="mdi-magnify"
        label="Search trainings"
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
            <span class="text-grey">Upcoming trainings</span>
          </div>

          <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreate">
            Create training
          </v-btn>
        </div>


        <div class="d-flex justify-end mb-6">
          <v-btn size="small" variant="outlined" class="mr-2" @click="sortNearest">
            Nearest
          </v-btn>

          <v-btn size="small" variant="outlined" @click="sortLatest">
            Latest
          </v-btn>
        </div>


        <v-row>
          <v-col
            v-for="training in filteredTrainings"
            :key="training.id"
            cols="12"
            sm="6"
            md="4"
            lg="3"
          >
            <v-card class="group-card pa-5" elevation="2">

              <div class="date mb-2">
                {{ formatDate(training.date) }}
              </div>

              <div class="time mb-2">
                {{ training.start }} - {{ training.end }}
              </div>

              <div class="d-flex align-center mb-3">
                <v-avatar size="44" class="mr-3">
                  <img src="https://i.pravatar.cc/100">
                </v-avatar>

                <div>
                  <div class="trainer">
                    {{ training.title }}
                  </div>
                  <div class="section">
                    {{ training.trainer }}
                  </div>
                </div>
              </div>

              <div v-if="getStatus(training.date)" class="status">
                {{ getStatus(training.date) }}
              </div>

            </v-card>
          </v-col>
        </v-row>


        <v-dialog v-model="dialog" max-width="500">
          <v-card>

            <v-card-title>
              {{ isEditing ? "Edit Training" : "Create Training" }}
            </v-card-title>

            <v-card-text>
              <v-text-field label="Title" v-model="newTraining.title" />
              <v-text-field label="Date (YYYY-MM-DD)" v-model="newTraining.date" />
              <v-text-field label="Start time" v-model="newTraining.start" />
              <v-text-field label="End time" v-model="newTraining.end" />
              <v-text-field label="Trainer" v-model="newTraining.trainer" />
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
import { ref, computed } from "vue"

const search = ref("")
const dialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const trainings = ref([
  {
    id:1,
    title:"Football Training U14",
    trainer:"Jānis Ozols",
    date:"2026-03-20",
    start:"17:00",
    end:"18:30"
  },
  {
    id:2,
    title:"Basketball Training",
    trainer:"Alex Johnson",
    date:"2026-03-18",
    start:"19:00",
    end:"20:00"
  },
  {
    id:3,
    title:"Running Practice",
    trainer:"Mike Smith",
    date:"2026-03-19",
    start:"15:00",
    end:"16:00"
  }
])

const filteredTrainings = computed(() => {
  return trainings.value.filter(t =>
    t.title.toLowerCase().includes(search.value.toLowerCase())
  )
})

function getDateTime(t){
  return new Date(`${t.date}T${t.start}`)
}

function sortNearest(){
  trainings.value.sort((a,b)=> getDateTime(a) - getDateTime(b))
}

function sortLatest(){
  trainings.value.sort((a,b)=> getDateTime(b) - getDateTime(a))
}

function formatDate(date){
  return new Date(date).toLocaleDateString("en-GB", {
    day:"numeric",
    month:"short",
    weekday:"short"
  })
}

function getStatus(date){
  const today = new Date()
  const d = new Date(date)

  const diff = Math.floor((d - today) / (1000*60*60*24))

  if(diff === 0) return "Today"
  if(diff === 1) return "Tomorrow"
  return ""
}

const newTraining = ref({
  title:"",
  date:"",
  start:"",
  end:"",
  trainer:""
})

function openCreate(){
  isEditing.value = false
  editingId.value = null
  newTraining.value = { title:"", date:"", start:"", end:"", trainer:"" }
  dialog.value = true
}

function openEdit(training){
  isEditing.value = true
  editingId.value = training.id
  newTraining.value = { ...training }
  dialog.value = true
}

function saveTraining(){
  if(isEditing.value){
    const index = trainings.value.findIndex(t => t.id === editingId.value)
    if(index !== -1){
      trainings.value[index] = { ...newTraining.value }
    }
  }else{
    trainings.value.push({
      id: Date.now(),
      ...newTraining.value
    })
  }

  dialog.value = false
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
  transform: translateY(-6px);
  box-shadow: 0 12px 25px rgba(0,0,0,0.08);
}

.date {
  font-size:13px;
  color:#777;
}

.time {
  font-size:18px;
  font-weight:600;
}

.trainer {
  font-weight: 600;
  font-size: 15px;
}

.section {
  font-size: 13px;
  color: #777;
}

.status {
  margin-top:8px;
  font-size:12px;
  font-weight:600;
  color:#1976d2;
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