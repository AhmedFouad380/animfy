
/* =========================
    PROFILE BUTTONS
========================= */

const productsBtn = document.querySelector(".products-btn");
const bioBtn = document.querySelector(".bio-btn");
const portfolioBtn = document.querySelector(".portfolio-btn");

function showProducts(){

    document.getElementById("products-section").style.display = "block";

    document.getElementById("portfolio-section").style.display = "none";

    document.getElementById("bio-section").style.display = "none";

    productsBtn.classList.add("active-btn-profile");

    portfolioBtn.classList.remove("active-btn-profile");

    bioBtn.classList.remove("active-btn-profile");
}

function showBio(){

    document.getElementById("products-section").style.display = "none";

    document.getElementById("portfolio-section").style.display = "none";

    document.getElementById("bio-section").style.display = "block";

    bioBtn.classList.add("active-btn-profile");

    portfolioBtn.classList.remove("active-btn-profile");

    productsBtn.classList.remove("active-btn-profile");
}

function showPortfolio(){

    document.getElementById("products-section").style.display = "none";

    document.getElementById("bio-section").style.display = "none";

    document.getElementById("portfolio-section").style.display = "block";

    portfolioBtn.classList.add("active-btn-profile");

    bioBtn.classList.remove("active-btn-profile");

    productsBtn.classList.remove("active-btn-profile");
}

/* =========================
    PRODUCTS NAVIGATION
========================= */

const tabItems = document.querySelectorAll(".tab-item");

function showTab(tabName, clickedTab){

    /* REMOVE ACTIVE */

    tabItems.forEach(tab => {
        tab.classList.remove("active-btn-products-nav");
    });

    /* ADD ACTIVE */

    clickedTab.classList.add("active-btn-products-nav");

    /* HIDE ALL CONTENT */

    document.getElementById("courses-content").style.display = "none";

    document.getElementById("addons-content").style.display = "none";

    document.getElementById("obj-content").style.display = "none";

    /* SHOW SELECTED CONTENT */

    document.getElementById(tabName + "-content").style.display = "block";
}

/* =========================
    POPUPS
========================= */

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

