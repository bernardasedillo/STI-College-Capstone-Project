document.addEventListener('DOMContentLoaded', function() {
    const venueCards = document.querySelectorAll(".venue-card");

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("is-visible");
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });

    venueCards.forEach(card => {
        observer.observe(card);
    });

    const venueDetails = {
        "garden": {
            "title": "Garden Venue",
            "description": "A beautiful outdoor space perfect for weddings and special occasions, surrounded by lush greenery."
        },
        "mini_hall": {
            "title": "Mini Function Hall",
            "description": "An intimate and elegant hall ideal for debuts, parties, and corporate events."
        },
        "pavilion": {
            "title": "The Pavilion",
            "description": "A versatile and spacious pavilion suitable for large gatherings and celebrations."
        },
        "hall": {
            "title": "Renato's Hall",
            "description": "Our grand hall, perfect for making a statement with its luxurious ambiance and ample space."
        }
    };

    const eventModal = document.getElementById('event-modal');
    const closeEventModalBtn = document.getElementById('closeEventModal');
    const openEventModalBtns = document.querySelectorAll('.open-event-modal');

    const eventTitle = document.getElementById('event-title');
    const eventDescription = document.getElementById('event-description');

    openEventModalBtns.forEach(function(btn) {
        btn.onclick = function() {
            const eventType = btn.getAttribute('data-event');
            eventTitle.textContent = venueDetails[eventType].title;
            eventDescription.textContent = venueDetails[eventType].description;
            eventModal.style.display = 'flex';
        }
    });

    closeEventModalBtn.onclick = function() {
        eventModal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == eventModal) {
            eventModal.style.display = 'none';
        }
    }
});
