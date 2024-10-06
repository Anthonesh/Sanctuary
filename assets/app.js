import './bootstrap.js';
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// Attend que le contenu du document soit complètement chargé avant d'exécuter le code
document.addEventListener('turbo:load', function () {
    // Gestionnaire d'événements pour le bouton Dons
    const donButton = document.querySelector('.donButton');
    if (donButton) {
        donButton.addEventListener('click', function() {
            window.location.href = `/donations`;
        });
    }
    // Initialisation du carrousel
    const carousel = document.getElementById('carousel');
    if (carousel) {
        const items = Array.from(carousel.getElementsByClassName('carousel-img-container'));
        const prevButton = document.getElementById('prevButton');
        const nextButton = document.getElementById('nextButton');

        if (!carousel.querySelector('.selected')) {
            items[0].classList.add('selected');
        }

        function updateCarouselPositions() {
            const selected = carousel.querySelector('.selected');
            const selectedIndex = items.indexOf(selected);

            items.forEach((item, index) => {
                item.classList.remove('selected', 'prev', 'next', 'hideLeft', 'hideRight', 'prevLeftSecond', 'nextRightSecond');

                if (index === selectedIndex) {
                    item.classList.add('selected');
                } else if (index === selectedIndex - 1 || index === selectedIndex + items.length - 1) {
                    item.classList.add('prev');
                } else if (index === selectedIndex + 1 || index === selectedIndex - items.length + 1) {
                    item.classList.add('next');
                } else if (index === selectedIndex - 2 || index === selectedIndex + items.length - 2) {
                    item.classList.add('prevLeftSecond');
                } else if (index === selectedIndex + 2 || index === selectedIndex - items.length + 2) {
                    item.classList.add('nextRightSecond');
                } else if (index < selectedIndex) {
                    item.classList.add('hideLeft');
                } else {
                    item.classList.add('hideRight');
                }
            });
        }

        prevButton.addEventListener('click', () => {
            const selectedIndex = items.findIndex(item => item.classList.contains('selected'));
            const prevIndex = (selectedIndex - 1 + items.length) % items.length;
            items.forEach(item => item.classList.remove('selected'));
            items[prevIndex].classList.add('selected');
            updateCarouselPositions();
        });

        nextButton.addEventListener('click', () => {
            const selectedIndex = items.findIndex(item => item.classList.contains('selected'));
            const nextIndex = (selectedIndex + 1) % items.length;
            items.forEach(item => item.classList.remove('selected'));
            items[nextIndex].classList.add('selected');
            updateCarouselPositions();
        });

        updateCarouselPositions();
    }

    // Initialisation du calendrier
    const calendarElt = document.querySelector("#calendar");
    if (calendarElt) {
        const eventsData = JSON.parse(calendarElt.getAttribute('data-events'));
        const isOnEventPage = window.location.pathname.includes('/event/planning');
        const isOnVolunteerPage = window.location.pathname.includes('/volunteer/scheduling');

        const calendar = new FullCalendar.Calendar(calendarElt, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                start: 'dayGridMonth,timeGridWeek',
                center: 'title',
                end: 'prev,next today'
            },
            events: eventsData.map(event => ({
                id: event.id,
                title: event.title,
                start: event.startTime,
                end: event.endTime || event.startTime,
                description: event.description,
                places: isOnEventPage ? event.places : event.volunteerPlaces,
                volunteerPlaces: event.volunteerPlaces
            })),
            dateClick: function(info) {
                // Vérifie si la case contient déjà des événements
                const clickedEvents = calendar.getEvents().filter(event => {
                    return event.start >= info.date && event.start < new Date(info.date.getTime() + 24 * 60 * 60 * 1000);
                });

                if (clickedEvents.length === 0) {
                    // Si aucune réservation, redirige vers la page de réservation
                    if (isOnVolunteerPage) {
                        window.location.href = `/volunteer/crud/reserve?date=${info.dateStr}`;
                    } else if (isOnEventPage) {
                        window.location.href = `/calendar/crud/new`;
                    }
                } else {
                    // Ouvre le modal avec les informations de l'événement
                    const firstEvent = clickedEvents[0]; // Prend le premier événement trouvé
                    const startTime = firstEvent.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const endTime = firstEvent.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    
                    let modalBody;

                    if (isOnEventPage) {
                        modalBody = `
                            <p><strong>Débute à</strong> ${startTime} heures</p>
                            <p><strong>Termine à</strong> ${endTime} heures</p>
                            <p><strong>Description:</strong> ${firstEvent.extendedProps.description}</p>
                            <p><strong>Places disponibles:</strong> ${firstEvent.extendedProps.places}</p>
                        `;
                        document.querySelector('#eventDetailsModal .modal-footer').innerHTML = `
                            <button type="button" id="btnReserv" class="btn">Reserver</button>
                        `;
                    } else if (isOnVolunteerPage) {
                        modalBody = `
                            <p><strong>Débute à</strong> ${startTime} heures</p>
                            <p><strong>Termine à</strong> ${endTime} heures</p>
                            <p><strong>Description:</strong> ${firstEvent.extendedProps.description}</p>
                            <p><strong>Places disponibles pour les bénévoles:</strong> ${firstEvent.extendedProps.volunteerPlaces}</p>
                        `;
                        if (firstEvent.extendedProps.volunteerPlaces > 0) {
                            document.querySelector('#eventDetailsModal .modal-footer').innerHTML = `
                                <button type="button" id="btnReservVolunteer" class="btn">Reserver</button>
                            `;
                        } else {
                            modalBody += `<p><strong>Aucune place disponible pour les bénévoles.</strong></p>`;
                            document.querySelector('#eventDetailsModal .modal-footer').innerHTML = '';
                        }
                    }

                    document.querySelector('#eventDetailsModal .modal-header').innerHTML = `
                        <h5 class="modal-title">${firstEvent.title}</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    `;

                    document.querySelector('#eventDetailsModal .modal-body').innerHTML = modalBody;

                    const eventDetailsModal = document.getElementById('eventDetailsModal');
                    eventDetailsModal.style.display = "block";

                    const btnReserv = document.getElementById('btnReserv');
                    if (btnReserv) {
                        btnReserv.onclick = function () {
                            window.location.href = `/reservation/form/crud/new?eventId=${firstEvent.id}`;
                        };
                    }

                    const btnReservVolunteer = document.getElementById('btnReservVolunteer');
                    if (btnReservVolunteer) {
                        btnReservVolunteer.onclick = function () {
                            window.location.href = `/volunteer/crud/new?eventId=${firstEvent.id}`;
                        };
                    }

                    const btnClose = document.querySelector('.btn-close');
                    btnClose.onclick = function () {
                        eventDetailsModal.style.display = "none";
                    };

                    window.onclick = function (event) {
                        if (event.target == eventDetailsModal) {
                            eventDetailsModal.style.display = "none";
                        }
                    };
                }
            },
            eventClick: function(info) {
                const startTime = info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const endTime = info.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                let modalBody;
                if (isOnEventPage) {
                    modalBody = `
                        <p><strong>Débute à</strong> ${startTime} heures</p>
                        <p><strong>Termine à</strong> ${endTime} heures</p>
                        <p><strong>Description:</strong> ${info.event.extendedProps.description}</p>
                        <p><strong>Places disponibles:</strong> ${info.event.extendedProps.places}</p>
                    `;
                    document.querySelector('#eventDetailsModal .modal-footer').innerHTML = `
                        <button type="button" id="btnReserv" class="btn">Reserver</button>
                    `;
                } else if (isOnVolunteerPage) {
                    modalBody = `
                        <p><strong>Débute à</strong> ${startTime} heures</p>
                        <p><strong>Termine à</strong> ${endTime} heures</p>
                        <p><strong>Description:</strong> ${info.event.extendedProps.description}</p>
                        <p><strong>Places disponibles pour les bénévoles:</strong> ${info.event.extendedProps.volunteerPlaces}</p>
                    `;
                    if (info.event.extendedProps.volunteerPlaces > 0) {
                        document.querySelector('#eventDetailsModal .modal-footer').innerHTML = `
                            <button type="button" id="btnReservVolunteer" class="btn">Reserver</button>
                        `;
                    } else {
                        modalBody += `<p><strong>Aucune place disponible pour les bénévoles.</strong></p>`;
                        document.querySelector('#eventDetailsModal .modal-footer').innerHTML = '';
                    }
                }

                document.querySelector('#eventDetailsModal .modal-header').innerHTML = `
                    <h5 class="modal-title">${info.event.title}</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                `;

                document.querySelector('#eventDetailsModal .modal-body').innerHTML = modalBody;

                const eventDetailsModal = document.getElementById('eventDetailsModal');
                eventDetailsModal.style.display = "block";

                const btnReserv = document.getElementById('btnReserv');
                if (btnReserv) {
                    btnReserv.onclick = function () {
                        window.location.href = `/reservation/form/crud/new?eventId=${info.event.id}`;
                    };
                }

                const btnReservVolunteer = document.getElementById('btnReservVolunteer');
                if (btnReservVolunteer) {
                    btnReservVolunteer.onclick = function () {
                        window.location.href = `/volunteer/crud/new?eventId=${info.event.id}`;
                    };
                }

                const btnClose = document.querySelector('.btn-close');
                btnClose.onclick = function () {
                    eventDetailsModal.style.display = "none";
                };

                window.onclick = function (event) {
                    if (event.target == eventDetailsModal) {
                        eventDetailsModal.style.display = "none";
                    }
                };
            },
        });

        calendar.render();
    }

    const btnBackToVolunteer = document.getElementById('btnBackToVolunteer');
    if (btnBackToVolunteer) {
        btnBackToVolunteer.onclick = function () {
            window.location.href = `/volunteer/scheduling`;
        };
    }

    const btnBackToEvent = document.getElementById('btnBackToEvent');
    if (btnBackToEvent) {
    btnBackToEvent.onclick = function () {
        window.location.href = `/event/planning`;
    };
}

    // Menu Burger
    document.querySelector('.burger-menu').addEventListener('click', toggleMenu);

    function toggleMenu() {
        let links = document.querySelector('.navLinks');
        links.classList.toggle('active');
        document.querySelector('.burger-menu').style.display = links.classList.contains('active') ? 'none' : 'block';
    }

    // Toggle Aside
    const asideToggleButton = document.querySelector('.aside-toggle');
    if (asideToggleButton) {
        asideToggleButton.addEventListener('touchstart', toggleAside);
        asideToggleButton.addEventListener('click', toggleAside);
    }

    function toggleAside() {
        let aside = document.querySelector('.aside');
        aside.style.display = aside.style.display === "block" ? "none" : "block";
    }

        // Modals terms and conditions
    // Sélection des éléments du modal
    const termsModal = document.getElementById("termsModal");
    const openTermsModalBtn = document.getElementById("openTermsModal");
    const closeTermsModalBtn = document.getElementById("closeTermsModal");
    const span = document.getElementsByClassName("custom-btn-close")[0];

    // Ouvrir le modal lorsque le bouton est cliqué
    openTermsModalBtn.onclick = function() {
       termsModal.style.display = "flex"; // Utiliser flex pour centrer
    }

    // Fermer le modal lorsque le bouton "Fermer" est cliqué
    closeTermsModalBtn.onclick = function() {
        termsModal.style.display = "none";
    }

    // Fermer le modal lorsque la croix est cliquée
    span.onclick = function() {
        termsModal.style.display = "none";
    }

    // Fermer le modal si l'utilisateur clique en dehors de celui-ci
    window.onclick = function(event) {
        if (event.target == termsModal) {
            termsModal.style.display = "none";
        }
    }
    


    // Stripe
    const stripeDataElement = document.getElementById('stripe-data');
    const stripePublicKey = stripeDataElement.getAttribute('data-stripe-key');
    const clientSecret = stripeDataElement.getAttribute('data-client-secret');

    const stripe = Stripe(stripePublicKey);
    const elements = stripe.elements();

    const cardNumber = elements.create('cardNumber', { style: { base: { color: '#fff' } } });
    const cardExpiry = elements.create('cardExpiry', { style: { base: { color: '#fff' } } });
    const cardCvc = elements.create('cardCvc', { style: { base: { color: '#fff' } } });

    cardNumber.mount('#card-number-element');
    cardExpiry.mount('#card-expiry-element');
    cardCvc.mount('#card-cvc-element');

    cardNumber.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    const form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardNumber,
                billing_details: {
                    name: form.querySelector('input[name="name"]').value,
                    email: form.querySelector('input[name="email"]').value
                }
            }
        }).then(function(result) {
            if (result.error) {
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                if (result.paymentIntent.status === 'succeeded') {
                    form.submit();
                }
            }
        });
    });
});

    // Modals terms and conditions
    // Sélection des éléments du modal
    const termsModal = document.getElementById("termsModal");
    const openTermsModalBtn = document.getElementById("openTermsModal");
    const closeTermsModalBtn = document.getElementById("closeTermsModal");
    const span = document.getElementsByClassName("custom-btn-close")[0];

    // Ouvrir le modal lorsque le bouton est cliqué
    openTermsModalBtn.onclick = function() {
       termsModal.style.display = "flex"; // Utiliser flex pour centrer
    }

    // Fermer le modal lorsque le bouton "Fermer" est cliqué
    closeTermsModalBtn.onclick = function() {
        termsModal.style.display = "none";
    }

    // Fermer le modal lorsque la croix est cliquée
    span.onclick = function() {
        termsModal.style.display = "none";
    }

    // Fermer le modal si l'utilisateur clique en dehors de celui-ci
    window.onclick = function(event) {
        if (event.target == termsModal) {
            termsModal.style.display = "none";
        }
    }