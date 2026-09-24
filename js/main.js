
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
