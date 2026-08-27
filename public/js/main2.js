const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
 );
 const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
 );
 // ----------------- Select Elements
 const btnsShow = document.querySelectorAll(".showData");
 const btnsEdit = document.querySelectorAll(".editData");
 const deletsEdit = document.querySelectorAll(".deleteData");
 const usersControlsForm = document.getElementById("usersControlsForm");
 // ----------------- Functions
 // ----------------- Events
 // Show Data
 btnsShow.forEach(btn=> {
    btn.addEventListener('click',function(e){
       console.log(e.target.closest('tr'));
    })
 })
 // Edit Data
 btnsEdit.forEach(btn=> {
    btn.addEventListener('click',function(e){
       console.log(e.target.closest('tr'));
    })
 })
 // Delte Data
 deletsEdit.forEach(btn=> {
    btn.addEventListener('click',function(e){
       console.log(e.target.closest('tr'));
    })
 })