document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('simulationForm');

    function calculateAndDisplayResults() {
        const prixAchat = parseFloat(document.getElementById('prixAchat').value) || 0;
        const fraisInstallation = parseFloat(document.getElementById('fraisInstallation').value) || 0;
        const prixLocation = parseFloat(document.getElementById('prixLocation').value) || 0;
        const joursLocation = parseFloat(document.getElementById('joursLocation').value) || 0;
        const chargesAnnuelles = parseFloat(document.getElementById('chargesAnnuelles').value) || 0;
        const fraisMaintenance = parseFloat(document.getElementById('fraisMaintenance').value) || 0;
        const taxesImpots = parseFloat(document.getElementById('taxesImpots').value) || 0;

        const investissementTotal = prixAchat + fraisInstallation;
        const revenuBrut = prixLocation * joursLocation;
        const revenuNet = revenuBrut - chargesAnnuelles - fraisMaintenance - taxesImpots;
        const rentabilite = (revenuNet / investissementTotal) * 100;
        const delaiRetour = investissementTotal / revenuNet;

        const formatterEuro = new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        document.getElementById('investissementTotal').textContent = formatterEuro.format(investissementTotal);
        document.getElementById('revenuBrut').textContent = formatterEuro.format(revenuBrut);
        document.getElementById('revenuNet').textContent = formatterEuro.format(revenuNet);
        document.getElementById('rentabilite').textContent = isFinite(rentabilite) ? `${rentabilite.toFixed(2)} %` : 'N/A';
        document.getElementById('delaiRetour').textContent = isFinite(delaiRetour) && delaiRetour > 0 ? `${delaiRetour.toFixed(1)} ans` : 'N/A';
    }

    form.addEventListener('input', calculateAndDisplayResults);
    calculateAndDisplayResults(); // Calcul initial au chargement de la page
});