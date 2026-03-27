document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('reservation-form');
    const btnReserver = document.getElementById('btn-reserver');
    const modal = document.getElementById('modal');
    const modalBody = document.getElementById('modal-body');
    const closeModal = document.getElementById('close');

    const allergiesRadios = document.querySelectorAll('[name*="allergiesActive"]');
    const allergiesContainer = document.getElementById('allergies-container');

    if (!form || !btnReserver || !modal) return;

    // --- Limite du nombre de couverts ---
    const nbCouvertInput = form.elements['reservation[nbCouvert]'];
    const maxCouverts = 15;

    nbCouvertInput.addEventListener('input', (e) => {
        let val = parseInt(e.target.value);
        if (isNaN(val)) {
            e.target.value = '';
            return;
        }
        if (val > maxCouverts) {
            // On ne garde que le premier chiffre si > max
            if (val.toString().length > 1) {
                e.target.value = val.toString().substring(0, 1);
            }
        }
    });

    // --- Récupérer le champ date ---
    const dateInput = form.elements['reservation[date]'];

    // --- Bloquer les créneaux complets (70 couverts) et les dimanches ---
    const heureInputs = form.querySelectorAll('input[name="reservation[heure]"]');

    function updateHeures(date) {
        if (!date) return;
        fetch(`/reservation/slots?date=${date}`)
            .then(res => res.json())
            .then(slots => {
                heureInputs.forEach(input => {
                    const label = document.querySelector(`label[for="${input.id}"]`);
                    const count = slots[input.value] || 0;
                    if (count >= 70) {
                        input.disabled = true;
                        label.style.color = '#ccc';
                    } else {
                        input.disabled = false;
                        label.style.color = '';
                    }
                });
            });
    }

    if (dateInput) {
        // Minimum date = aujourd'hui
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        dateInput.addEventListener('input', e => {
            const val = e.target.value;
            if (!val) return;

            const selectedDate = new Date(val);
            if (selectedDate.getDay() === 1) { // lundi
                alert("Le restaurant est fermé le lundi. Veuillez choisir un autre jour.");
                e.target.value = '';
                return;
            }

            // Mettre à jour les heures disponibles
            updateHeures(val);
        });

        // précharger les heures dès le chargement si une date est déjà sélectionnée
        if (dateInput.value) updateHeures(dateInput.value);
    }

    // Toggle allergies
    allergiesRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            const show = document.querySelector('[name*="allergiesActive"]:checked')?.value === '1';
            allergiesContainer.style.display = show ? 'block' : 'none';
        });
    });
    allergiesContainer.style.display = (document.querySelector('[name*="allergiesActive"]:checked')?.value === '1') ? 'block' : 'none';

    // --- Récupérer les données correctes pour modal ---
    function getData() {
        // Récupération via form.elements pour être sûr de trouver les champs
        const nb = form.elements['reservation[nbCouvert]']?.value || '';
        const date = form.elements['reservation[date]']?.value || '';
        const heure = form.querySelector('input[name="reservation[heure]"]:checked')?.value || '';
        const allergiesActive = form.querySelector('input[name="reservation[allergiesActive]"]:checked')?.value === '1';
        const allergies = allergiesActive ? form.elements['reservation[allergies]']?.value || '' : 'Non';

        return { nb, date, heure, allergies };
    }

    // Afficher modal
    btnReserver.addEventListener('click', () => {
        const data = getData();

        modalBody.innerHTML = `
            <h3>Confirmation de votre réservation</h3>
            <p><strong>Couverts :</strong> ${data.nb}</p>
            <p><strong>Date :</strong> ${data.date}</p>
            <p><strong>Heure :</strong> ${data.heure}</p>
            <p><strong>Allergies :</strong> ${data.allergies || 'Non'}</p>

            <div class="modal-buttons" style="margin-top:15px;">
                <button id="confirm" class="btn-confirm">Confirmer</button>
                <button id="cancel" class="btn-cancel">Annuler</button>
            </div>
        `;

        modal.style.display = 'flex';

        document.getElementById('cancel').onclick = () => modal.style.display = 'none';
        document.getElementById('confirm').onclick = sendAjax;
    });

    // --- AJAX ---
    async function sendAjax() {
        modalBody.innerHTML = `<p>⏳ Enregistrement...</p>`;
        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            });

            const text = await res.text();
            let result = null;
            try {
                result = JSON.parse(text);
            } catch {
                modalBody.innerHTML = `<pre style="color:red;">Erreur serveur inattendue : ${text}</pre>`;
                return;
            }

            if (!result.success) {
                modalBody.innerHTML = `
                    <p style="color:red;">${result.message || 'Erreur formulaire'}</p>
                    <pre>${result.errors || ''}</pre>
                    <button id="back" class="btn btn-secondary" style="margin-top:10px;">Retour</button>
                `;
                document.getElementById('back').onclick = () => modal.style.display = 'none';
                return;
            }

            modalBody.innerHTML = `<p style="color:green;">${result.message}</p>`;
            setTimeout(() => window.location.reload(), 1500);

        } catch (err) {
            console.error(err);
            modalBody.innerHTML = `<p style="color:red;">Erreur serveur</p>`;
        }
    }

    // Fermer modal
    closeModal.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
});