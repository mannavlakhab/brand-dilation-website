   const menuButton = document.getElementById('menu-button');
        const closeButton = document.getElementById('close-button');
        const sidebar = document.getElementById('sidebar');
        const searchButton = document.getElementById('search-button');
        const searchInput = document.getElementById('search-input');

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-hidden');
            document.body.classList.toggle('no-scroll', !sidebar.classList.contains('sidebar-hidden'));
        });

        closeButton.addEventListener('click', () => {
            sidebar.classList.add('sidebar-hidden');
            document.body.classList.remove('no-scroll');
        });

        searchButton.addEventListener('click', () => {
            searchInput.classList.toggle('active');
            searchInput.focus();
        });

