document.getElementById('emailForm').addEventListener('submit', async (event) => {
    event.preventDefault(); 

    const formData = new FormData(event.target);

    try {
        const response = await fetch('send_email.php', {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            const result = await response.json();
            if (result.success) {
                alert(result.message); 
            } else {
                alert(result.message); 
            }
        } else {
            alert('Ошибка при отправке данных.');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при отправке данных.');
    }
});