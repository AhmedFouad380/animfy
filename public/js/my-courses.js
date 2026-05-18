


const userTrigger = document.getElementById("user-trigger");
const dropdownMenu = document.getElementById("dropdown-menu");

userTrigger.addEventListener("click", () => {

  dropdownMenu.classList.toggle("active");

  userTrigger.classList.toggle("active");

});

document.addEventListener("click", (e) => {

  if (!e.target.closest(".user-dropdown")) {

    dropdownMenu.classList.remove("active");

    userTrigger.classList.remove("active");
  }

});