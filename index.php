<?php
require_once 'config.php';
if(isLoggedIn()){if(isAdmin())redirect('supervisor.php');if(isSupervisor())redirect('supervisor.php');if($_SESSION['user']['role']==='expert')redirect('expert.php');}
?>
<!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>کنترل استراحت | shaya</title><link rel="stylesheet" href="app.css"></head><body>
<div class="login-brand-corner"><img src="shaya_logo.png" class="login-corner-logo" alt="shaya"><span>shaya</span></div><div id="loginGlobalAnnouncement" class="global-announcement login-global-announcement hidden"><div class="global-announcement-inner"><span class="global-announcement-icon">📢</span><div><b>اطلاعیه</b><div id="loginGlobalAnnouncementText"></div></div></div></div><div class="login-page"><div class="login-card">
<div class="coffee"><i class="steam s1"></i><i class="steam s2"></i><i class="steam s3"></i><div class="cup"></div></div>
<div class="brandbig">کنترل استراحت</div><p class="muted" style="text-align:center">سامانه مدیریت هوشمند استراحت</p>
<div class="tabs"><button class="tab active" onclick="tab('expert',this)">کارشناس</button><button class="tab" onclick="tab('staff',this)">سرپرست / مدیر</button></div>
<section id="expert"><label>نام کاربری</label><input id="expertUser" class="input" autocomplete="username" placeholder="نام کاربری کارشناس"><label>رمز عبور</label><input id="expertPass" type="password" class="input" autocomplete="current-password" placeholder="رمز عبور"><button id="expertBtn" class="btnx btn-primary" style="width:100%;margin-top:14px" onclick="expertLogin()">ورود</button></section>
<section id="staff" class="hidden"><label>نام کاربری</label><input id="username" class="input" autocomplete="username" placeholder="نام کاربری"><label>رمز عبور</label><input id="password" type="password" class="input" autocomplete="current-password" placeholder="رمز عبور"><button class="btnx btn-primary" style="width:100%;margin-top:14px" onclick="staffLogin()">ورود</button></section>
<div id="msg" class="muted" style="margin-top:14px;text-align:center;min-height:20px"></div><div style="display:flex;justify-content:center;margin-top:12px"><button class="btnx" onclick="toggleTheme()">◐ تغییر حالت</button></div>
</div></div>
<script>
function tab(id,b){document.querySelectorAll('.tabs .tab').forEach(x=>x.classList.remove('active'));b.classList.add('active');document.getElementById('expert').classList.toggle('hidden',id!=='expert');document.getElementById('staff').classList.toggle('hidden',id!=='staff');document.getElementById('msg').textContent=''}
async function post(u,d){let r=await fetch(u,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'},body:new URLSearchParams(d),credentials:'same-origin'});return r.json()}
async function expertLogin(){let b=document.getElementById('expertBtn');b.disabled=true;let r=await post('ajax_expert_login.php',{username:document.getElementById('expertUser').value,password:document.getElementById('expertPass').value});if(r.success)location.href=r.redirect||'expert.php';else{document.getElementById('msg').textContent=r.message||'ورود ناموفق بود';b.disabled=false}}
async function staffLogin(){let r=await post('ajax_login.php',{username:document.getElementById('username').value,password:document.getElementById('password').value});if(r.success)location.href=r.redirect||'supervisor.php';else document.getElementById('msg').textContent=r.message||'ورود ناموفق بود'}
async function loadLoginAnnouncement(){try{let r=await post('ajax_announcement.php',{action:'get'}),a=r.announcement,top=document.getElementById('loginGlobalAnnouncement');if(!a||!String(a.message||'').trim()){top?.classList.add('hidden');return;}if(top){top.classList.remove('hidden');document.getElementById('loginGlobalAnnouncementText').textContent=a.message;}}catch(e){}}
loadLoginAnnouncement();setInterval(loadLoginAnnouncement,5000);
function toggleTheme(){document.body.classList.toggle('light');localStorage.setItem('shaya-theme',document.body.classList.contains('light')?'light':'dark')}
if(localStorage.getItem('shaya-theme')==='light')document.body.classList.add('light');
</script></body></html>
