
document.addEventListener("DOMContentLoaded", () => {
  const nav = document.querySelector(".nav");
  const menu = document.querySelector(".menu");
  if (menu) menu.addEventListener("click", () => nav.classList.toggle("mobile-open"));

  document.querySelectorAll(".nav-links a").forEach(a => {
    a.addEventListener("click", () => nav && nav.classList.remove("mobile-open"));
  });

  const filters = document.querySelectorAll("[data-filter]");
  const items = document.querySelectorAll("[data-category]");
  filters.forEach(btn => btn.addEventListener("click", () => {
    filters.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    const filter = btn.dataset.filter;
    items.forEach(item => {
      item.style.display = (filter === "all" || item.dataset.category === filter) ? "" : "none";
    });
  }));

  document.querySelectorAll("[data-whatsapp]").forEach(form => {
    form.addEventListener("submit", e => {
      e.preventDefault();
      const data = new FormData(form);
      const text = [...data.entries()].map(([k,v]) => `${k}: ${v}`).join("\n");
      const phone = form.dataset.whatsapp;
      window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`, "_blank");
    });
  });
});

/* ELEVEN8 slider */
document.addEventListener("DOMContentLoaded",()=>{const slider=document.querySelector(".site-slider");if(!slider)return;const slides=[...slider.querySelectorAll(".slide")],dots=slider.querySelector(".slider-dots"),progress=slider.querySelector(".slider-progress");let current=0,timer;slides.forEach((_,i)=>{const b=document.createElement("button");b.className="slider-dot"+(i===0?" active":"");b.type="button";b.setAttribute("aria-label","Go to slide "+(i+1));b.onclick=()=>show(i);dots.appendChild(b)});function show(i){current=(i+slides.length)%slides.length;slides.forEach((s,n)=>s.classList.toggle("active",n===current));[...dots.children].forEach((d,n)=>d.classList.toggle("active",n===current));restart()}function restart(){clearInterval(timer);progress.style.width="0";void progress.offsetWidth;progress.style.transition="width 5.5s linear";progress.style.width="100%";timer=setInterval(()=>show(current+1),5500)}slider.querySelector(".next").onclick=()=>show(current+1);slider.querySelector(".prev").onclick=()=>show(current-1);slider.onmouseenter=()=>clearInterval(timer);slider.onmouseleave=restart;restart()});

document.addEventListener("DOMContentLoaded",()=>{const f=document.getElementById("signupForm");if(f)f.addEventListener("submit",e=>{const a=document.getElementById("signupPassword"),b=document.getElementById("signupConfirm");if(a.value!==b.value){e.preventDefault();alert("Passwords do not match.");b.focus();}});const l=document.getElementById("loginForm");if(l)l.addEventListener("submit",e=>{e.preventDefault();alert("Login interface is ready. Connect this form to your authentication backend.");});});
