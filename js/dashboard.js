// dashboard.js

document.addEventListener('DOMContentLoaded', function () {
    fetch('../admin/stats.php')
        .then(response => response.json())
        .then(data => {

            document.getElementById('totalUsers').textContent = data.totalUsers;
            document.getElementById('totalProducts').textContent = data.totalProducts;
            document.getElementById('activeBookings').textContent = data.activeBookings;

            // График: Пользователи
            const ctxUsers = document.getElementById('usersChart').getContext('2d');
            new Chart(ctxUsers, {
                type: 'line',
                data: {
                    labels: data.usersByMonth.map(r => r.month),
                    datasets: [{
                        label: 'Новых пользователей',
                        data: data.usersByMonth.map(r => r.count),
                        borderColor: '#4CAF50',
                        backgroundColor: 'rgba(76, 175, 80, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true }
            });

            // График: Бронирования
            const ctxBookings = document.getElementById('bookingsChart').getContext('2d');
            new Chart(ctxBookings, {
                type: 'bar',
                data: {
                    labels: data.bookingsByMonth.map(r => r.month),
                    datasets: [{
                        label: 'Бронирования',
                        data: data.bookingsByMonth.map(r => r.count),
                        backgroundColor: '#2196F3'
                    }]
                },
                options: { responsive: true }
            });

            // График: Товары по брендам
            const ctxBrands = document.getElementById('productsByBrandChart').getContext('2d');
            new Chart(ctxBrands, {
                type: 'pie',
                data: {
                    labels: data.productsByBrand.map(r => r.brand),
                    datasets: [{
                        label: 'Количество товаров',
                        data: data.productsByBrand.map(r => r.count),
                        backgroundColor: ['#FF9800', '#E91E63', '#9C27B0', '#03A9F4', '#00BCD4', '#4CAF50']
                    }]
                },
                options: { responsive: true }
            });
        })
        .catch(error => console.error('Ошибка загрузки данных:', error));
});