// Dynamic Navbar Menu Items
const navbarLinks = [
    { name: "Home", link: "home.php" },
    { name: "About", link: "about.php" },
    { name: "Gallery", link: "gallery.php" },
    { name: "Rooms", link: "room.php" },
    { name: "Venues", link: "venues.php" },
    { name: "Events", link: "#event" },
    { name: "FAQ", link: "#faq" },
    { name: "Book Now", link: "#reservation", class: "btn" },
];

// Generate Navbar Dynamically
const navbar = document.getElementById("navbar");
const currentPage = window.location.pathname.split("/").pop();

navbar.innerHTML = `<ul>
    ${navbarLinks.map(item => `
        <li><a href="${item.link}" class="${item.link === currentPage ? 'active' : ''} ${item.class || ''}">${item.name}</a></li>
    `).join('')}
</ul>`;

// Toggle Menu for Mobile
const menuBtn = document.getElementById("menu-btn");
menuBtn.addEventListener("click", () => {
    navbar.classList.toggle("active");
});
