


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


const chapters = document.querySelectorAll(".chapter");

chapters.forEach((chapter) => {
  const header = chapter.querySelector(".chapter-header");

  header.addEventListener("click", () => {

    const isOpen = chapter.classList.contains("open");

    // اقفل الكل
    chapters.forEach(c => c.classList.remove("open"));

    // لو كان مقفول افتحه
    if (!isOpen) {
      chapter.classList.add("open");
    }

  });
});


const popup = document.getElementById("reviewPopup");
const openBtn = document.getElementById("openReview");
const closeBtn = document.getElementById("closeReview");

openBtn.addEventListener("click", () => {
  popup.style.display = "flex";
});

closeBtn.addEventListener("click", () => {
  popup.style.display = "none";
});

// Stars logic
const stars = document.querySelectorAll("#stars span");
let selectedRating = 0;

stars.forEach(star => {
  star.addEventListener("click", () => {
    selectedRating = star.dataset.value;

    stars.forEach(s => s.classList.remove("active"));

    for (let i = 0; i < selectedRating; i++) {
      stars[i].classList.add("active");
    }
  });
});