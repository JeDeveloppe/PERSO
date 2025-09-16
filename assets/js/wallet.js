        document.addEventListener('DOMContentLoaded', function() {
            const apiUrlsElement = document.getElementById('data-api-urls');
            const apiUrl = apiUrlsElement.dataset.apiWallet;

            const updateButton = document.getElementById('update-prices-btn');
            const loaderOverlay = document.getElementById('loader-overlay');
            const contentContainer = document.getElementById('content-container');
            const summaryContainer = document.getElementById('summary-container');
            
            // Fonction pour mettre à jour les données de la page
            function updatePageContent(data) {
                const stockData = data.stocks;
                
                // Mise à jour de chaque ligne du tableau
                stockData.forEach(item => {
                    const row = document.querySelector(`tr[data-id="${item.id}"]`);
                    if (row) {
                        const priceCell = row.querySelector('.price-cell');
                        const totalValueCell = row.querySelector('.total-value-cell');
                        const gainOrLossCell = row.querySelector('.gain-or-loss-cell');
                        
                        priceCell.innerHTML = item.actualPrice !== null ? `${item.actualPrice.toFixed(2)} €` : '<span class="text-muted">N/A</span>';
                        totalValueCell.innerHTML = item.totalValue !== null ? `${item.totalValue.toFixed(2)} €` : '<span class="text-muted">N/A</span>';

                        if (item.gainOrLoss !== null) {
                            const formattedGainLoss = item.gainOrLoss.toFixed(2) + ' €';
                            let colorClass = 'text-muted';
                            let icon = '';
                            if (item.gainOrLoss > 0) {
                                colorClass = 'text-success';
                                icon = '<i class="fas fa-caret-up"></i> ';
                            } else if (item.gainOrLoss < 0) {
                                colorClass = 'text-danger';
                                icon = '<i class="fas fa-caret-down"></i> ';
                            }
                            gainOrLossCell.innerHTML = `<span class="${colorClass}">${icon}${formattedGainLoss}</span>`;
                        } else {
                            gainOrLossCell.innerHTML = '<span class="text-muted">N/A</span>';
                        }
                    }
                });

                // Mise à jour des totaux du portefeuille
                document.getElementById('total-value').textContent = data.total_value_of_the_wallet.toFixed(2) + ' €';
                
                const totalGainLossElement = document.getElementById('gain-or-loss-total');
                totalGainLossElement.textContent = data.gain_or_loss_total_value.toFixed(2) + ' €';

                if (data.gain_or_loss_total_value > 0) {
                    totalGainLossElement.classList.add('text-success');
                    totalGainLossElement.classList.remove('text-danger', 'text-muted');
                } else if (data.gain_or_loss_total_value < 0) {
                    totalGainLossElement.classList.add('text-danger');
                    totalGainLossElement.classList.remove('text-success', 'text-muted');
                } else {
                    totalGainLossElement.classList.add('text-muted');
                    totalGainLossElement.classList.remove('text-success', 'text-danger');
                }
                totalGainLossElement.innerHTML = totalGainLossElement.innerHTML;
                if (data.gain_or_loss_total_value > 0) {
                    totalGainLossElement.insertAdjacentHTML('afterbegin', '<i class="fas fa-caret-up me-2"></i>');
                } else if (data.gain_or_loss_total_value < 0) {
                    totalGainLossElement.insertAdjacentHTML('afterbegin', '<i class="fas fa-caret-down me-2"></i>');
                }
                
                const now = new Date();
                document.getElementById('last-updated').textContent = now.toLocaleDateString('fr-FR') + ' ' + now.toLocaleTimeString('fr-FR');
            }

            // Gestion du clic sur le bouton
            if (updateButton) {
                updateButton.addEventListener('click', function() {
                    const btnText = document.getElementById('btn-text');
                    const btnSpinner = document.getElementById('btn-spinner');

                    // 1. Affiche le loader et cache le contenu
                    loaderOverlay.style.display = 'flex';
                    contentContainer.style.display = 'none';
                    summaryContainer.style.display = 'none';
                    
                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    updateButton.disabled = true;

                    // 2. Lancer la requête API
                    fetch(apiUrl)
                        .then(response => response.json())
                        .then(data => {
                            // Appel de la fonction de mise à jour
                            updatePageContent(data);
                        })
                        .catch(error => console.error('Erreur:', error))
                        .finally(() => {
                            // 3. Cache le loader et ré-affiche le contenu
                            loaderOverlay.style.display = 'none';
                            contentContainer.style.display = 'block';
                            summaryContainer.style.display = 'flex';

                            btnText.style.display = 'inline-block';
                            btnSpinner.style.display = 'none';
                            updateButton.disabled = false;
                        });
                });
            }
        });