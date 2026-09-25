/* ELEVEN8 site interactions, client authentication, activity notepad and WhatsApp booking/payment */
(() => {
  "use strict";
  const WHATSAPP_NUMBER = "2348120167383";
  const USERS_KEY = "eleven8_users", SESSION_KEY = "eleven8_session", LOG_KEY = "eleven8_activity_log";
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

  async function api(path, body) {
    const response = await fetch("api/" + path, {method:"POST", headers:{"Content-Type":"application/json"}, credentials:"same-origin", body:JSON.stringify(body)});
    const data = await response.json().catch(()=>({success:false,message:"Server returned an invalid response."}));
    if (!response.ok) throw new Error(data.message || "Request failed.");
    return data;
  }

  async function getCsrf() {
    const response = await fetch("api/csrf.php", {credentials:"same-origin"});
    const data = await response.json();
    if (!data.success) throw new Error("Could not initialize security token.");
    return data.csrf;
  }

  function saveUsers(users){localStorage.setItem(USERS_KEY,JSON.stringify(users))}
  function safeFormData(form){const data={};new FormData(form).forEach((value,key)=>{if(/password|card|cvv|cvc|pin/i.test(key))return;data[key]=String(value)});return data}
  function logActivity(type,details={}){let entries=[];try{entries=JSON.parse(localStorage.getItem(LOG_KEY)||"[]")}catch(_){}entries.push({timestamp:new Date().toISOString(),type,page:location.pathname.split("/").pop()||"index.html",details});localStorage.setItem(LOG_KEY,JSON.stringify(entries.slice(-500)))}
  function downloadLog(){let entries=[];try{entries=JSON.parse(localStorage.getItem(LOG_KEY)||"[]")}catch(_){}const lines=["ELEVEN8 ACTIVITY NOTEPAD","=========================",""];entries.forEach((entry,i)=>{lines.push((i+1)+". "+entry.timestamp+" | "+entry.type);Object.entries(entry.details||{}).forEach(([key,value])=>lines.push("   "+key+": "+value));lines.push("")});const blob=new Blob([lines.join("\n")],{type:"text/plain;charset=utf-8"}),url=URL.createObjectURL(blob),a=document.createElement("a");a.href=url;a.download="eleven8_activity_log.txt";a.click();setTimeout(()=>URL.revokeObjectURL(url),500)}
  function initNavigation(){const nav=$(".nav"),menu=$(".menu");if(menu)menu.addEventListener("click",()=>nav&&nav.classList.toggle("mobile-open"));$$('.nav-links a').forEach(a=>a.addEventListener("click",()=>nav&&nav.classList.remove("mobile-open")))}
  function initFilters(){const filters=$$("[data-filter]"),items=$$("[data-category]");filters.forEach(btn=>btn.addEventListener("click",()=>{filters.forEach(b=>b.classList.remove("active"));btn.classList.add("active");const filter=btn.dataset.filter;items.forEach(item=>item.style.display=filter==="all"||item.dataset.category===filter?"":"none")}))}
  function initWhatsApp(){$$("[data-whatsapp]").forEach(form=>form.addEventListener("submit",e=>{e.preventDefault();const data=safeFormData(form),text=Object.entries(data).map(([k,v])=>k+": "+v).join("\n");logActivity("WhatsApp booking request",data);window.open("https://wa.me/"+form.dataset.whatsapp+"?text="+encodeURIComponent(text),"_blank")}))}
  function initSlider(){const slider=$(".site-slider");if(!slider)return;const slides=$$(".slide",slider),dots=$(".slider-dots",slider),progress=$(".slider-progress",slider);if(!slides.length||!dots)return;let current=0,timer;slides.forEach((_,i)=>{const b=document.createElement("button");b.className="slider-dot"+(i===0?" active":"");b.type="button";b.setAttribute("aria-label","Go to slide "+(i+1));b.onclick=()=>show(i);dots.appendChild(b)});function restart(){clearInterval(timer);if(progress){progress.style.width="0";void progress.offsetWidth;progress.style.transition="width 5.5s linear";progress.style.width="100%"}timer=setInterval(()=>show(current+1),5500)}function show(i){current=(i+slides.length)%slides.length;slides.forEach((s,n)=>s.classList.toggle("active",n===current));[...dots.children].forEach((d,n)=>d.classList.toggle("active",n===current));restart()}const next=$(".next",slider),prev=$(".prev",slider);if(next)next.onclick=()=>show(current+1);if(prev)prev.onclick=()=>show(current-1);slider.onmouseenter=()=>clearInterval(timer);slider.onmouseleave=restart;restart()}
  function initSignup(){const form=$("#signupForm");if(!form)return;form.addEventListener("submit",async e=>{e.preventDefault();const status=$("#signupStatus"),password=$("#signupPassword"),confirm=$("#signupConfirm");if(password.value.length<8){status.textContent="Password must contain at least 8 characters.";status.className="form-status error";return}if(password.value!==confirm.value){status.textContent="Passwords do not match.";status.className="form-status error";return}try{const csrf=await getCsrf();const r=await api("signup.php",{csrf,first_name:form.elements["First Name"].value.trim(),last_name:form.elements["Last Name"].value.trim(),email:form.elements.Email.value.trim(),phone:form.elements.Phone.value.trim(),password:password.value});status.textContent=r.message;status.className="form-status success";form.reset();setTimeout(()=>location.href="login.html",900)}catch(err){status.textContent=err.message;status.className="form-status error"}})}
  function initLogin(){const form=$("#loginForm");if(!form)return;form.addEventListener("submit",async e=>{e.preventDefault();const status=$("#loginStatus");try{const csrf=await getCsrf();const r=await api("login.php",{csrf,email:form.elements.Email.value.trim(),password:form.elements.Password.value});localStorage.setItem(SESSION_KEY,JSON.stringify(r.user));logActivity("Login success",{Email:r.user.email});status.textContent="Welcome back, "+r.user.first_name+". Login successful.";status.className="form-status success";setTimeout(()=>location.href=(r.user.role==="admin"?"admin/index.php":"dashboard.php"),700)}catch(err){logActivity("Login attempt",{Email:form.elements.Email.value.trim(),result:"failed"});status.textContent=err.message;status.className="form-status error"}})}
  function initLogTools(){const download=$("#downloadLog"),clear=$("#clearLog");if(download)download.addEventListener("click",downloadLog);if(clear)clear.addEventListener("click",()=>{if(confirm("Clear the ELEVEN8 local activity notepad?")){localStorage.removeItem(LOG_KEY);logActivity("Activity log cleared");alert("Local activity log cleared.")}})}
  function initBooking(){
    const form=$("#bookingForm");if(!form)return;
    const packageSelect=$("#bookingPackage"),customField=$("#customAmountField"),customAmount=$("#customAmount"),amountText=$("#bookingAmount"),status=$("#bookingStatus");
    function getAmount(){if(packageSelect.value==="Custom Quote")return Number(customAmount.value||0);return Number(packageSelect.selectedOptions[0]?.dataset.amount||0)}
    function updateAmount(){const custom=packageSelect.value==="Custom Quote";customField.hidden=!custom;customAmount.required=custom;const amount=getAmount();amountText.textContent=amount?"₦"+amount.toLocaleString("en-NG"):"Select a package"}
    packageSelect.addEventListener("change",updateAmount);customAmount.addEventListener("input",updateAmount);updateAmount();
    form.addEventListener("submit",async e=>{
      e.preventDefault();
      const amount=getAmount();
      if(!amount||amount<100){status.textContent="Please select a package or enter a valid custom amount.";status.className="form-status error";return}
      try{
        const csrf=await getCsrf();
        const data={csrf,name:form.elements.Name.value.trim(),email:form.elements.Email.value.trim(),phone:form.elements.Phone.value.trim(),service:form.elements.Service.value,date:form.elements.Date.value,package:form.elements.Package.value,location:form.elements.Location.value.trim(),details:form.elements.Details.value.trim(),amount};
        const saved=await api("booking.php",data);
        logActivity("Booking saved",{BookingID:saved.booking_id,Email:data.email,Amount:"₦"+amount.toLocaleString("en-NG")});
        const message=[
          "Hello ELEVEN8, I would like to proceed with my booking and payment.",
          "",
          "Booking ID: "+saved.booking_id,
          "Name: "+data.name,
          "Email: "+data.email,
          "Phone: "+data.phone,
          "Service: "+data.service,
          "Date: "+data.date,
          "Package: "+data.package,
          "Location: "+(data.location||"N/A"),
          "Amount: ₦"+amount.toLocaleString("en-NG"),
          "Details: "+(data.details||"N/A"),
          "",
          "Please send me the payment instructions and confirm my booking."
        ].join("\n");
        const whatsappUrl="https://wa.me/"+WHATSAPP_NUMBER+"?text="+encodeURIComponent(message);
        status.textContent="Booking saved. Opening WhatsApp to complete your payment arrangements...";
        status.className="form-status success";
        logActivity("WhatsApp payment request",{BookingID:saved.booking_id,WhatsApp:"+2348120167383",Amount:"₦"+amount.toLocaleString("en-NG")});
        window.open(whatsappUrl,"_blank");
      }catch(err){status.textContent=err.message;status.className="form-status error"}
    });
  }
  document.addEventListener("DOMContentLoaded",()=>{initNavigation();initFilters();initWhatsApp();initSlider();initSignup();initLogin();initLogTools();initBooking();logActivity("Page view")});
})();