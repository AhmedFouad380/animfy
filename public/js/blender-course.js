
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



document.querySelectorAll('.popup-overlay').forEach(popup => {
    popup.addEventListener('click', (e) => {
        if(e.target === popup){
            if (typeof window.closePopup === 'function') {
                window.closePopup(popup.id);
            } else {
                popup.classList.remove('show');
                setTimeout(() => {
                    popup.style.display = "none";
                }, 300);
            }
        }
    });
});

