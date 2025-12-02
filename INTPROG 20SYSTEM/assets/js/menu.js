const tabs = document.querySelectorAll('.tab_btn');
const coffeeSection = document.querySelector('.coffee');
const snacksSection = document.querySelector('.snacks');

// Add event listeners for tab buttons
tabs.forEach((tab, index) => {
  tab.addEventListener('click', () => {
    // Remove "active" class from all buttons
    tabs.forEach(btn => btn.classList.remove('active'));
    tab.classList.add('active');

    // Toggle between coffee and snacks
    if (index === 0) {
      coffeeSection.classList.add('active');
      snacksSection.classList.remove('active');
    } else {
      snacksSection.classList.add('active');
      coffeeSection.classList.remove('active');
    }
  });
});

// Show coffee section by default
coffeeSection.classList.add('active');
snacksSection.classList.remove('active');
