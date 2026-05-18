// =========================
// SIDEBAR NAVIGATION
// =========================

const navLinks = document.querySelectorAll(".nav-link");
const sections = document.querySelectorAll(".section");

navLinks.forEach(link => {
  link.addEventListener("click", () => {
    navLinks.forEach(n => n.classList.remove("active"));
    link.classList.add("active");

    const target = link.dataset.section;

    sections.forEach(sec => sec.classList.remove("active-section"));
    document.querySelector(`.${target}`).classList.add("active-section");
  });
});


// =========================
// POPUPS HELPERS
// =========================

function openPopup(id) {
  document.getElementById(id).style.display = "flex";
}

function closePopup(el) {
  el.closest(".popup-overlay").style.display = "none";
}

// close buttons
document.querySelectorAll(".close-popup").forEach(btn => {
  btn.addEventListener("click", () => closePopup(btn));
});


// =========================
// ADD COURSE POPUP
// =========================

const addCourseBtn = document.querySelector(".courses-section .add-courses-btn");
const addCoursePopup = document.getElementById("add-course-popup");

addCourseBtn.addEventListener("click", () => {
  openPopup("add-course-popup");
});


// =========================
// CREATE COURSE
// =========================

document.getElementById("create-course-btn").addEventListener("click", () => {

  const input = document.getElementById("course-name-input");
  const courseName = input.value.trim();

  if (!courseName) return;

  const tbody = document.querySelector(".courses-section tbody");

  tbody.innerHTML += `
    <tr>
      <td>${courseName}</td>
      <td>0 EGP</td>
      <td>0</td>
      <td>
        <button class="edit-courses-btn">Edit</button>
        <button class="delete-courses-btn">Delete</button>
      </td>
    </tr>
  `;

  input.value = "";
  closePopup(document.querySelector("#add-course-popup"));
});


// =========================
// EDIT COURSE POPUP
// =========================

const editCoursePopup = document.getElementById("edit-course-popup");

document.addEventListener("click", (e) => {

  if (e.target.classList.contains("edit-courses-btn")) {

    const row = e.target.closest("tr");
    const name = row.children[0].innerText;

    document.getElementById("edit-course-name").value = name;

    editCoursePopup.style.display = "flex";
  }
});


// =========================
// CURRICULUM CONTROLS (FIXED)
// =========================

const chapterPopup = document.getElementById("chapter-popup");
const lessonPopup = document.getElementById("lesson-popup");

const curriculumContainer = document.querySelector(".curriculum-container");
const chapterSelect = document.getElementById("chapter-select");

// IMPORTANT FIX: buttons scoped inside popup section only
const curriculumBtns = document.querySelectorAll(".curriculum-buttons .add-btn");/////////

const createChapterBtn = curriculumBtns[0];
const createLessonBtn = curriculumBtns[1];

createChapterBtn.addEventListener("click", () => {
  openPopup("chapter-popup");
});

createLessonBtn.addEventListener("click", () => {
  openPopup("lesson-popup");
});


// =========================
// CREATE CHAPTER
// =========================

document.getElementById("save-chapter-btn").addEventListener("click", () => {

  const nameInput = document.getElementById("chapter-name-input");
  const chapterName = nameInput.value.trim();

  if (!chapterName) return;

  const id = "chapter-" + Date.now();

  curriculumContainer.innerHTML += `
    <div class="admin-chapter" id="${id}">
      <div class="admin-chapter-header">
        <div class="admin-chapter-title">${chapterName}</div>
      </div>
      <div class="admin-lessons"></div>
    </div>
  `;

  chapterSelect.innerHTML += `
    <option value="${id}">${chapterName}</option>
  `;

  nameInput.value = "";
  closePopup(chapterPopup);
});




// =========================
// CREATE LESSON
// =========================

document.getElementById("save-lesson-btn").addEventListener("click", () => {

  const lessonName = document.getElementById("lesson-name-input").value.trim();
  const selectedChapter = chapterSelect.value;

  const lessonDescription = quill.root.innerHTML;

  if (!lessonName || !selectedChapter) return;

    const lesson = {
        name: lessonName,
        description: lessonDescription,
        isPreview: true};

  const chapter = document.getElementById(selectedChapter);
  const container = chapter.querySelector(".admin-lessons");

container.innerHTML += `
  <div class="admin-lesson preview">

    <div class="admin-lesson-left">
      <i class="fa-solid fa-circle-play"></i>
      <span>${lessonName}</span>
    </div>

    <div class="lesson-actions">

      <label class="switch">
        <input type="checkbox" class="preview-toggle" checked>
        <span class="slider"></span>
      </label>

      <button class="edit-lesson-btn">Edit</button>
      <button class="delete-lesson-btn">Delete</button>

    </div>

  </div>`;

  document.getElementById("lesson-name-input").value = "";
  chapterSelect.value = "";
  quill.root.innerHTML = "";

  closePopup(lessonPopup);
});

// =========================
// FIXED LESSON TOGGLE (IMPORTANT)
// =========================

document.addEventListener("change", (e) => {

  if (e.target.classList.contains("preview-toggle")) {

    const lessonEl = e.target.closest(".admin-lesson");

    if (e.target.checked) {
      lessonEl.classList.add("preview");
    } else {
      lessonEl.classList.remove("preview");
    }
  }
});



//===================
// Discount Toggle
//===================

const discountToggle = document.getElementById("discount-toggle");
const discountInput = document.getElementById("discount-price");

discountToggle.addEventListener("change", () => {
  if (discountToggle.checked) {
    discountInput.style.display = "block";
  } else {
    discountInput.style.display = "none";
    discountInput.value = "";
  }
});




// =========================
// STUDENTS
// =========================

const students = [
  { name:"Ahmed Mohamed", email:"ahmed@gmail.com", phone:"0100000000" },
  { name:"Sara Ali", email:"sara@gmail.com", phone:"0111111111" },
  { name:"Omar Hassan", email:"omar@gmail.com", phone:"0122222222" },
  { name:"Ali Ahmed", email:"ali@gmail.com", phone:"015555555" },
  { name:"Mona Khaled", email:"mona@gmail.com", phone:"010999999" },
  { name:"Student 6", email:"student6@gmail.com", phone:"010000000" },
  { name:"Student 7", email:"student7@gmail.com", phone:"010000000" },
  { name:"Student 8", email:"student8@gmail.com", phone:"010000000" },
  { name:"Student 9", email:"student9@gmail.com", phone:"010000000" },
  { name:"Student 10", email:"student10@gmail.com", phone:"010000000" },
  { name:"Student 11", email:"student11@gmail.com", phone:"010000000" },
  { name:"Student 12", email:"student12@gmail.com", phone:"010000000" },
  { name:"Student 13", email:"student13@gmail.com", phone:"010000000" },
  { name:"Student 14", email:"student14@gmail.com", phone:"010000000" },
  { name:"Student 15", email:"student15@gmail.com", phone:"010000000" }
];

const studentsPerPage = 10;

function loadStudents(page = 1) {

  const p = Number(page);

  const start = (p - 1) * studentsPerPage;
  const end = start + studentsPerPage;

  const sliced = students.slice(start, end);

  const tbody = document.getElementById("students-table-body");

  tbody.innerHTML = "";

  sliced.forEach(student => {

    tbody.innerHTML += `
      <tr>
        <td>
          <div class="student-box">
            <img src="../imgs/users-imgs/user.png">
            <span>${student.name}</span>
          </div>
        </td>
        <td>${student.email}</td>
        <td>${student.phone}</td>
        <td>
          <button class="viewCourses-btn">View Courses</button>
        </td>
        <td>
          <button class="editStudent-btn">Edit</button>
        </td>
      </tr>
    `;
  });
}

// init
loadStudents(1);


// =========================
// PAGINATION FIXED
// =========================

const pageButtons = document.querySelectorAll(".page-btn");

pageButtons.forEach(btn => {

  btn.addEventListener("click", () => {

    pageButtons.forEach(b => b.classList.remove("active-page"));
    btn.classList.add("active-page");

    loadStudents(btn.dataset.page); // string OK
  });
});


// =========================
// QUILL INIT (MUST BE LAST)
// =========================

const quill = new Quill("#lesson-editor", {
  theme: "snow",
  placeholder: "Write lesson description...",
  modules: {
    toolbar: [
      [{ header: [1, 2, 3, false] }],
      ["bold", "italic", "underline"],
      ["blockquote", "code-block"],
      [{ list: "ordered" }, { list: "bullet" }],
      ["link", "image"],
      ["clean"]
    ]
  }
});

