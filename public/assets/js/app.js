// ===== App.js untuk SwimComp =====

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
// $(document).ready(function() {
//   $('#dataTable').DataTable({
//     responsive: true,
//     language: {
//       url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
//     }
//   });
// });

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

// inisialisasi flatpickr
flatpickr(".tanggal", {
  dateFormat: "Y-m-d",
  maxDate: "today",
  altInput: true,
  altFormat: "d-m-Y",
  allowInput: true
});

// setting default datatables
$.extend(true, $.fn.dataTable.defaults, {
  responsive: true,
  scrollX: true,
  language: {
      processing: 'loading...',
      search: 'Cari:',
      // lengthMenu: 'Tampil _MENU_ data',
      // info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
      infoEmpty: 'Tidak ada data',
      // infoFiltered: "(difilter dari _MAX_ total data)",
      zeroRecords: "Tidak ada data yang cocok",
      paginate: {
          first: "Awal",
          last: "Akhir",
          next: "Berikutnya",
          previous: "Sebelumnya"
      },
  },
});
