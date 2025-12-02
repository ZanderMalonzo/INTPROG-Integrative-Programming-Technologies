document.addEventListener("click", function(e) {
    const dropdown = document.querySelector(".dropdown");
    const profileBtn = document.querySelector(".profile");
    // Click on profile button → toggle dropdown (tab-like behavior)
    if (e.target.closest(".profile")) {
        dropdown.classList.toggle("show");
        e.preventDefault(); // stop accidental navigation
        return;
    }
    // Click outside → close dropdown
    if (!e.target.closest(".dropdown")) {
        dropdown.classList.remove("show");
    }
});