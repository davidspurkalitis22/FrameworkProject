document.addEventListener('DOMContentLoaded', function() {
    const kitForm = document.getElementById('kitForm');
    const showTeamBtn = document.getElementById('showTeamBtn');
    const addToCardBtn = document.getElementById('addToCardBtn');
    const notification = document.getElementById('notification');
    const kitImage = document.getElementById('kitImage');

    // Prevent form from submitting normally
    kitForm.addEventListener('submit', (e) => {
        e.preventDefault();
    });

    // Show Team - Direct HTML response example
    showTeamBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        const team = document.getElementById('team').value;
        const kit = document.getElementById('kit').value;

        if (!team || !kit) {
            alert('Please select both team and kit type');
            return;
        }

        const formData = new FormData();
        formData.append('team', team);
        formData.append('kit', kit);
        formData.append('show_team', true);

        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            // Direct HTML response
            const html = await response.text();
            kitImage.innerHTML = html;
        } catch (error) {
            console.error('Error:', error);
        }
    });

    // Add to Cart - JSON response example
    addToCardBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        const team = document.getElementById('team').value;
        const kit = document.getElementById('kit').value;

        if (!team || !kit) {
            alert('Please select both team and kit type');
            return;
        }

        const formData = new FormData();
        formData.append('team', team);
        formData.append('kit', kit);
        formData.append('add_to_card', true);

        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            // JSON response
            const data = await response.json();
            
            notification.classList.remove('hidden');
            if (data.status === 'success') {
                // Use the HTML from JSON response
                notification.innerHTML = data.html;
            } else {
                notification.innerHTML = `<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>
                    Error: ${data.error}
                </div>`;
            }

            // Hide notification after 3 seconds
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);

        } catch (error) {
            console.error('Error:', error);
        }
    });
}); 