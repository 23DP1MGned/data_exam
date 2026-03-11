<template>

<v-container fluid class="fill-height d-flex align-center justify-center rgb-bg">

<v-row class="login-wrapper" elevation="4">

<v-col cols="6" class="login-left">

<h1 class="logo">Sport System</h1>

<p class="subtitle">
Manage trainings, attendance and payments
in one platform.
</p>

</v-col>

<v-col cols="6" class="login-right">

<v-card class="pa-8" width="400">

<v-card-title class="text-h5 mb-4">
Login
</v-card-title>

<v-text-field
label="Email"
prepend-inner-icon="mdi-email"
variant="outlined"
/>

<v-text-field
label="Password"
type="password"
prepend-inner-icon="mdi-lock"
variant="outlined"
/>

<v-btn
color="primary"
block
size="large"
class="mt-4"
>
Sign in
</v-btn>

<v-btn
variant="text"
block
class="mt-2"
>
Forgot password?
</v-btn>

</v-card>

</v-col>

</v-row>

</v-container>

</template>


<style scoped>

.rgb-bg{
height:100vh;

background: linear-gradient(
120deg,
#0f172a,
#1e3a8a,
#2563eb,
#0ea5e9,
#1d4ed8
);

background-size: 400% 400%;

animation: rgbFlow 16s ease infinite;
}

@keyframes rgbFlow{
0%{
background-position:0% 50%;
}
50%{
background-position:100% 50%;
}
100%{
background-position:0% 50%;
}
}

.login-wrapper{
background:white;
border-radius:12px;
overflow:hidden;
box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

.login-left{
background:#2563eb;
color:white;
display:flex;
flex-direction:column;
justify-content:center;
align-items:center;
padding:40px;
}

.logo{
font-size:36px;
font-weight:700;
margin-bottom:20px;
}

.subtitle{
max-width:260px;
text-align:center;
opacity:0.9;
}

.login-right{
display:flex;
justify-content:center;
align-items:center;
}

</style>