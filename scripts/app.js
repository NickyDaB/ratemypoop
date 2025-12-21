// ratemypoop.net — UI skeleton interactions (no backend)

const yearEl = document.getElementById("year");
const uploadModal = document.getElementById("uploadModal");
const btnUpload = document.getElementById("btnUpload");
const ctaUpload = document.getElementById("ctaUpload");
const ctaRandom = document.getElementById("ctaRandom");

const pills = document.querySelectorAll(".pill");
const searchInput = document.getElementById("searchInput");
const sortSelect = document.getElementById("sortSelect");
const feedGrid = document.getElementById("feedGrid");
const postCount = document.getElementById("postCount");

yearEl.textContent = new Date().getFullYear();

function openUploadModal(){
  if (typeof uploadModal?.showModal === "function") uploadModal.showModal();
  else alert("Your browser doesn't support <dialog>. Still a skeleton tho 😅");
}

btnUpload?.addEventListener("click", openUploadModal);
ctaUpload?.addEventListener("click", openUploadModal);

ctaRandom?.addEventListener("click", () => {
  const cards = Array.from(document.querySelectorAll(".card"));
  const pick = cards[Math.floor(Math.random() * cards.length)];
  pick?.scrollIntoView({ behavior: "smooth", block: "center" });
  pick?.classList.add("pulse");
  setTimeout(() => pick?.classList.remove("pulse"), 800);
});

// Filter pills (purely visual + simple label)
pills.forEach(pill => {
  pill.addEventListener("click", () => {
    pills.forEach(p => p.classList.remove("is-active"));
    pill.classList.add("is-active");
  });
});

// Search (title/tags dataset)
function applySearch(){
  const q = (searchInput?.value || "").trim().toLowerCase();
  const cards = Array.from(document.querySelectorAll(".card"));

  let visible = 0;
  cards.forEach(card => {
    const title = (card.dataset.title || "").toLowerCase();
    const tags = (card.dataset.tags || "").toLowerCase();
    const hit = !q || title.includes(q) || tags.includes(q);

    card.style.display = hit ? "" : "none";
    if (hit) visible++;
  });

  postCount.textContent = `${visible} posts`;
}

searchInput?.addEventListener("input", applySearch);

// Sort (basic DOM reorder demo)
sortSelect?.addEventListener("change", () => {
  const mode = sortSelect.value;
  const cards = Array.from(document.querySelectorAll(".card"));

  // Extremely fake sort keys for now
  const key = (card) => {
    const title = (card.dataset.title || "").toLowerCase();
    if (mode === "new") return title; // placeholder
    if (mode === "top") return title;
    if (mode === "rating") {
      const v = card.querySelector(".rating__value")?.textContent || "0";
      return Number(v);
    }
    return title; // hot
  };

  cards.sort((a, b) => {
    const ka = key(a);
    const kb = key(b);
    // numeric if rating
    if (mode === "rating") return kb - ka;
    return ka.localeCompare(kb);
  });

  cards.forEach(c => feedGrid.appendChild(c));
});

// Tiny CSS pulse helper (added inline via class)
const style = document.createElement("style");
style.textContent = `
  .pulse{
    outline: 2px solid rgba(0,212,255,.7);
    box-shadow: 0 0 0 6px rgba(0,212,255,.14);
    transition: box-shadow .2s ease;
  }
`;
document.head.appendChild(style);
