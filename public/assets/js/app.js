// ===== App.js untuk SwimComp =====

// Notifikasi sederhana saat form disubmit
// document.addEventListener("DOMContentLoaded", () => {
//   document.querySelectorAll("form").forEach(form => {
//     form.addEventListener("submit", (e) => {
//       e.preventDefault(); // cegah reload halaman
//       alert("Data berhasil disimpan ✅");
//       // TODO: diintegrasikan ke API backend (fetch/axios)
//       let modalEl = form.closest(".modal");
//       if (modalEl) {
//         let modal = bootstrap.Modal.getInstance(modalEl);
//         modal.hide();
//       }
//       form.reset();
//     });
//   });
// });

// Contoh fungsi export (dummy)
// function exportResults(format) {
//   alert("Export hasil dalam format: " + format);
//   // TODO: ganti dengan fetch API backend untuk download file
// }

// // Pasang event listener tombol export di results.html
// document.addEventListener("DOMContentLoaded", () => {
//   const btnPdf = document.querySelector(".btn-outline-primary");
//   const btnCsv = document.querySelector(".btn-outline-success");
//   if (btnPdf) btnPdf.addEventListener("click", () => exportResults("PDF"));
//   if (btnCsv) btnCsv.addEventListener("click", () => exportResults("CSV"));
// });

// untuk spinner
function showSpinner() {
  document.getElementById("loadingSpinner").classList.remove("d-none");
}
function hideSpinner() {
  document.getElementById("loadingSpinner").classList.add("d-none");
}

// handle interactive btn dark mode
document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("darkModeToggle");

  toggle?.addEventListener("change", () => {
    document.body.classList.toggle("dark-mode", toggle.checked);
    localStorage.setItem("darkMode", toggle.checked);
  });

  // Restore preference
  if (localStorage.getItem("darkMode") === "true") {
    toggle.checked = true;
    document.body.classList.add("dark-mode");
  }
});

// Data tables.js
$(document).ready(function() {
  $('#dataTable').DataTable({
    responsive: true,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
    }
  });
});

const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
  }
});
