const fileInput = document.getElementById("fileInput");
const uploadForm = document.getElementById("uploadForm");
const previewWrap = document.getElementById("previewWrap");
const previewImg = document.getElementById("previewImg");
const fileNameEl = document.getElementById("fileName");
const fileInfoEl = document.getElementById("fileInfo");
const uploadBtn = document.getElementById("uploadBtn");
const clearBtn = document.getElementById("clearBtn");
const statusEl = document.getElementById("status");

const MAX_MB = 8;
let selectedFile = null;

function setStatus(msg) {
  statusEl.textContent = msg || "";
}

function resetUI() {
  selectedFile = null;
  fileInput.value = "";
  previewWrap.classList.add("hidden");
  previewImg.src = "";
  fileNameEl.textContent = "";
  fileInfoEl.textContent = "";
  uploadBtn.disabled = true;
  clearBtn.disabled = true;
  setStatus("");
}

function humanSize(bytes) {
  const mb = bytes / (1024 * 1024);
  return `${mb.toFixed(2)} MB`;
}

function handleFile(file) {
  if (!file) return;

  if (!file.type.startsWith("image/")) {
    setStatus("Please choose an image file.");
    return;
  }

  const mb = file.size / (1024 * 1024);
  if (mb > MAX_MB) {
    setStatus(`File too large. Max is ${MAX_MB} MB.`);
    return;
  }

  selectedFile = file;

  const url = URL.createObjectURL(file);
  previewImg.src = url;

  previewWrap.classList.remove("hidden");
  fileNameEl.textContent = file.name;
  fileInfoEl.textContent = `${file.type} • ${humanSize(file.size)}`;

  uploadBtn.disabled = false;
  clearBtn.disabled = false;
  setStatus("Ready to upload.");
}

fileInput.addEventListener("change", (e) => {
  handleFile(e.target.files[0]);
});

// drag & drop
const dropLabel = document.querySelector(".file-drop");
dropLabel.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropLabel.style.borderColor = "var(--primary)";
});
dropLabel.addEventListener("dragleave", () => {
  dropLabel.style.borderColor = "#3a4252";
});
dropLabel.addEventListener("drop", (e) => {
  e.preventDefault();
  dropLabel.style.borderColor = "#3a4252";
  const file = e.dataTransfer.files?.[0];
  handleFile(file);
});

clearBtn.addEventListener("click", resetUI);

uploadForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  if (!selectedFile) return;

  // Placeholder until backend exists
  setStatus("Pretending to upload… (backend not wired yet)");
  uploadBtn.disabled = true;

  setTimeout(() => {
    setStatus("Upload complete! (not really 😇)");
    uploadBtn.disabled = false;
  }, 800);
});

resetUI();
