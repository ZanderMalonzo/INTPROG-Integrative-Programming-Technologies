const searchInput = document.getElementById("searchInput");
const clearSearch = document.getElementById("clearSearch");

const menuCards = document.querySelectorAll(".menu-card, .snack-card");
const menuTitles = document.querySelectorAll(".menu-title");

searchInput.addEventListener("input", () => {
  const filter = searchInput.value.toLowerCase().trim();

  //Filter visible cards
  menuCards.forEach(card => {
    const title = card.querySelector("h3").textContent.toLowerCase();
    card.style.display = title.includes(filter) ? "flex" : "none";
  });

  //Hide or show menu titles based on visible cards
  menuTitles.forEach(title => {
    const grid = title.nextElementSibling;

    if (grid && (grid.classList.contains("menu-grid") || grid.classList.contains("snack-grid"))) {
      const cards = grid.querySelectorAll(".menu-card, .snack-card");
      const anyVisible = Array.from(cards).some(
        card => getComputedStyle(card).display !== "none"
      );

      //hide title and its grid if no visible cards remain
      title.style.display = anyVisible ? "block" : "none";
      grid.style.display = anyVisible ? "grid" : "none";
    }
  });
});

//Reset everything when clearing search
clearSearch.addEventListener("click", () => {
  searchInput.value = "";
  menuCards.forEach(card => (card.style.display = "flex"));
  menuTitles.forEach(title => {
    title.style.display = "block";
    const grid = title.nextElementSibling;
    if (grid && (grid.classList.contains("menu-grid") || grid.classList.contains("snack-grid"))) {
      grid.style.display = "grid";
    }
  });
});
