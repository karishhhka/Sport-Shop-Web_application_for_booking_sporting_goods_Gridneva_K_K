document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.products_card_btn');

    buttons.forEach(button => {
        button.addEventListener('click', function (e) {
            const product_id = this.getAttribute('data-id');

            if (!product_id) {
                alert('Ошибка: ID товара не найден.');
                return;
            }

            fetch('book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: product_id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Товар успешно забронирован!');
                    location.reload();
                } else {
                    alert('Ошибка: ' + data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});