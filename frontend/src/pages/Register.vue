<template>

<div class="login-bg">

<v-container fluid class="fill-height d-flex align-center justify-center">

<v-card width="420" class="login-card pa-8">

<h1 class="login-title">Register</h1>
<p class="login-subtitle">create your account</p>

<v-text-field
label="Email"
variant="outlined"
/>

<v-text-field
label="Password"
type="password"
variant="outlined"
/>

<v-text-field
label="Confirm Password"
type="password"
variant="outlined"
/>

<v-btn
color="primary"
size="large"
block
class="login-btn"
>
Create account →
</v-btn>

<div class="signup">
Already have an account?
<router-link to="/login">Login</router-link>
</div>

</v-card>

</v-container>

</div>

</template>

<style scoped>

.login-bg{
height:100vh;
width:100%;
position:relative;
overflow:hidden;
background:#020617;
}


.login-bg::before,
.login-bg::after{
content:"";
position:absolute;
width:600px;
height:600px;
border-radius:50%;
filter:blur(120px);
opacity:0.6;
animation:liquidMove 18s infinite alternate ease-in-out;
}

.login-bg::before{
background:rgb(37,99,235);
top:-100px;
left:-100px;
}

.login-bg::after{
background:rgb(14,165,233);
bottom:-120px;
right:-120px;
animation-duration:22s;
}

@keyframes liquidMove{
0%{
transform:translate(0,0) scale(1);
}
100%{
transform:translate(120px,80px) scale(1.2);
}
}


.login-card{
border-radius:18px;
background:rgba(120,120,120,0.75);
backdrop-filter:blur(20px);
box-shadow:0 30px 60px rgba(0,0,0,0.2);
}


.login-title{
font-size:40px;
font-weight:700;
margin-bottom:4px;
}

.login-subtitle{
color:#ffffff;
margin-bottom:10px;
}


.login-btn{
height:48px;
font-size:16px;
border-radius:10px;
margin-top:10px;
transition:all .25s;
}

.login-btn:hover{
transform:translateY(-2px);
box-shadow:0 12px 25px rgba(0,0,0,0.15);
}

.signup{
margin-top:20px;
font-size:14px;
color:#000000;
text-align:center;
}

.signup a{
font-weight:600;
text-decoration:none;
color:#000000;
}

</style>