
function openPatientInfoModal() {
    document.getElementById('patientInfoModal').style.display = 'block';
}

function closePatientInfoModal() {
    document.getElementById('patientInfoModal').style.display = 'none';
}

// Optional: close when clicking outside modal
window.onclick = function(event) {
    const modal = document.getElementById('patientInfoModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}






document.addEventListener('DOMContentLoaded', function () {    
    const track = document.querySelector('.carousel-track');
    const slides = Array.from(track.children);
    const nextButton = document.querySelector('.carousel-button-right');
    const prevButton = document.querySelector('.carousel-button-left');

    let currentIndex = 0;
    let slidesPerView = 3;

    function updateSlidesPerView() {
        if (window.innerWidth < 768) {
            slidesPerView = 1;
        } else {
            slidesPerView = 3;
        }
        moveToSlide(currentIndex);
    }

    function moveToSlide(index) {
        const slideWidth = slides[0].getBoundingClientRect().width;


        if (index < 0) index = 0;
        if (index > slides.length - slidesPerView) {
            index = slides.length - slidesPerView;
        }

        currentIndex = index;
        track.style.transform = `translateX(-${index * slideWidth}px)`;
    }

    nextButton.addEventListener('click', () => {
        if (currentIndex < slides.length - slidesPerView) {
            moveToSlide(currentIndex + 1);
        } else {
            moveToSlide(0);
        }
    });

    prevButton.addEventListener('click', () => {
        if (currentIndex > 0) {
            moveToSlide(currentIndex - 1);
        } else {
            moveToSlide(slides.length - slidesPerView);
        }
    });


    updateSlidesPerView();
    window.addEventListener('resize', updateSlidesPerView);


    let autoSlideInterval = setInterval(() => {
        if (currentIndex < slides.length - slidesPerView) {
            moveToSlide(currentIndex + 1);
        } else {
            moveToSlide(0);
        }
    }, 4000);


    const carouselContainer = document.querySelector('.carousel-container');
    carouselContainer.addEventListener('mouseenter', () => {
        clearInterval(autoSlideInterval);
    });

    carouselContainer.addEventListener('mouseleave', () => {
        autoSlideInterval = setInterval(() => {
            if (currentIndex < slides.length - slidesPerView) {
                moveToSlide(currentIndex + 1);
            } else {
                moveToSlide(0);
            }
        }, 4000);
    });


});



function suggestDentist() {
    const service = document.getElementById('service').value.trim().toLowerCase();
    const doctorSelect = document.getElementById('doctor');

    if (!service) {
        doctorSelect.selectedIndex = 0; // Reset
        return;
    }

    for (let i = 1; i < doctorSelect.options.length; i++) { // Start from 1 to skip "Select"
        const option = doctorSelect.options[i];
        const text = option.textContent.toLowerCase();

        // Check if the specialization matches the service
        if (text.includes(service)) {
            doctorSelect.selectedIndex = i;
            return;
        }
    }

    // If no match, reset
    doctorSelect.selectedIndex = 0;
}


// Appointment Form Handling
document.addEventListener('DOMContentLoaded', function () {
    const appointmentForm = document.getElementById('appointmentForm');

    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const submitBtn = document.getElementById('submitAppointmentBtn');
            // Disable the submit button to prevent multiple clicks
            submitBtn.disabled = true;
            submitBtn.textContent = "Booking...";

            // Collect form data
            const formData = new FormData(appointmentForm);

            // Basic validation for required fields
            if (!formData.get('name') || !formData.get('email') || !formData.get('phone') || !formData.get('preferred_date')) {
                alert("Please fill in all required fields.");
                submitBtn.disabled = false;  // Re-enable button if validation fails
                submitBtn.textContent = "Book Appointment";
                return;
            }

            // Send data to PHP for processing
            fetch('submit_appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Appointment booked successfully!');
                    appointmentForm.reset();
                } else {
                    alert('Error: ' + data.message);
                }
                // Re-enable the submit button after response
                submitBtn.disabled = false;
                submitBtn.textContent = "Book Appointment";
            })
            .catch(error => {
                alert('Network error: ' + error);
                // Re-enable the submit button if network error
                submitBtn.disabled = false;
                submitBtn.textContent = "Book Appointment";
            });
        });
    }
});



// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);

        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Image lazy loading for testimonials and team members
document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('img[loading="lazy"]');

    if ('loading' in HTMLImageElement.prototype) {
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    } else {
        // Fallback for browsers that don't support lazy loading
        // You could implement a custom lazy loading solution here
    }
});

// Validate phone number input
const phoneInput = document.getElementById('phone');
if (phoneInput) {
    phoneInput.addEventListener('input', function (e) {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9+()-]/g, '');
    });
}

// Set minimum date for appointment booking
const dateInput = document.getElementById('preferred_date');
if (dateInput) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    dateInput.min = tomorrow.toISOString().split('T')[0];
}

document.addEventListener("DOMContentLoaded", () => {
    const sections = document.querySelectorAll('.fade-section'); // Select all sections with fade effect
    const options = {
        root: null,
        rootMargin: "0px",
        threshold: 0.1 // When 50% of the section is visible
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add 'visible' class when the section comes into view
                entry.target.classList.add('visible');
            } else {
                // Remove the 'visible' class when the section goes out of view
                entry.target.classList.remove('visible');
            }
        });
    }, options);

    sections.forEach(section => observer.observe(section)); // Observe each section
});
