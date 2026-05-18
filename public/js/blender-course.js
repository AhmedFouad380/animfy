
function buyNow(){
    alert('Redirecting to payment...');
}




// =========================
// CHAPTER ACCORDION (ONE OPEN ONLY)
// =========================

const chapters = document.querySelectorAll(".chapter");

chapters.forEach((chapter, index) => {

    const header = chapter.querySelector(".chapter-header");
    const content = chapter.querySelector(".chapter-content");

    // =========================
    // OPEN FIRST CHAPTER BY DEFAULT
    // =========================
    if(index === 0){
        chapter.classList.add("active");
        content.style.maxHeight = content.scrollHeight + "px";
    }

    header.addEventListener("click", () => {

        const isOpen = chapter.classList.contains("active");

        // =========================
        // CLOSE ALL CHAPTERS FIRST
        // =========================
        chapters.forEach(ch => {
            ch.classList.remove("active");
            ch.querySelector(".chapter-content").style.maxHeight = null;
        });

        // =========================
        // OPEN CLICKED ONE (IF IT WAS CLOSED)
        // =========================
        if(!isOpen){
            chapter.classList.add("active");
            content.style.maxHeight = content.scrollHeight + "px";
        }

    });

});



// =========================
// LESSON PREVIEW ICON STATE
// =========================

document.querySelectorAll(".lesson").forEach(lesson => {

    const badge = lesson.querySelector(".preview-badge");
    const icon = lesson.querySelector(".preview-icon");

    if (badge && icon) {
        icon.classList.add("is-preview");
    }

});


// =========================
// Price Card Scroll Smoothly
// =========================

const sidebar = document.querySelector(".sidebar");

window.addEventListener("scroll", () => {
    const scrollY = window.scrollY;

    sidebar.style.transform = `translateY(${scrollY * 0.02}px)`;
});



// =========================
// POPUPS
// =========================

function openPopup(id){
    document.getElementById(id).style.display = "flex";
}

function closePopup(id){
    document.getElementById(id).style.display = "none";
}

document.querySelectorAll('.popup-overlay').forEach(popup => {
    popup.addEventListener('click', (e) => {
        if(e.target === popup){
            popup.style.display = "none";
        }
    });
});


/* =========================
    Code Country
========================= */
const phoneInput = document.querySelector("#phone");

window.intlTelInput(phoneInput, {
    initialCountry: "eg",
    separateDialCode: true,
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js"
});

const input = document.querySelector("#phone");

// منع أي حروف لحظة الكتابة
input.addEventListener("input", function () {
    this.value = this.value.replace(/\D/g, "").slice(0, 15);
});

// منع paste فيه حروف
input.addEventListener("paste", function (e) {
    let pasted = (e.clipboardData || window.clipboardData).getData("text");
    
    if (/\D/.test(pasted)) {
        e.preventDefault();
        this.value = pasted.replace(/\D/g, "").slice(0, 15);
    }
});

