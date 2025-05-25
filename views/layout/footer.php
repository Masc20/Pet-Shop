<footer class="gradient-bg text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Pawfect Pet Shop</h5>
                <p>Your trusted partner in pet care and adoption. We connect loving families with adorable pets.</p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo BASE_URL; ?>/pets" class="text-white-50">Adopt Pets</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/products" class="text-white-50">Pet Products</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/adopted-pets" class="text-white-50">Success Stories</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact Info</h5>
                <p class="text-white-50">
                    <i class="fas fa-envelope"></i> info@pawfect.com<br>
                    <i class="fas fa-phone"></i> (555) 123-4567<br>
                    <i class="fas fa-map-marker-alt"></i> 123 Pet Street, City
                </p>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center">
            <p>&copy; 2024 Pawfect Pet Shop. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
<script>
    function addToCart(productId) {
        fetch('<?php echo BASE_URL; ?>/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item added to cart!');
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
    }

    function adoptPet(petId) {
        if (confirm('Are you sure you want to adopt this pet?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo BASE_URL; ?>/pets/adopt';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'pet_id';
            input.value = petId;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');
        let searchTimeout;

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();

                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch('<?php echo BASE_URL; ?>/search?q=' + encodeURIComponent(query))
                        .then(response => response.json())
                        .then(data => {
                            displaySearchResults(data.results);
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            searchResults.style.display = 'none';
                        });
                }, 300);
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
        }

        function displaySearchResults(results) {
            if (results.length === 0) {
                searchResults.innerHTML = '<div class="p-3 text-muted">No results found</div>';
                searchResults.style.display = 'block';
                return;
            }

            let html = '';
            results.forEach(result => {
                html += `
                        <div class="p-3 border-bottom" style="cursor: pointer;" onclick="window.location.href='${result.url}'">
                            <div class="d-flex align-items-center">
                                <img src="${result.image}" alt="${result.title}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${result.title}</h6>
                                    <p class="mb-1 small text-muted">${result.description}</p>
                                    <small class="text-primary fw-bold">${result.price}</small>
                                </div>
                                <span class="badge bg-secondary">${result.type}</span>
                            </div>
                        </div>
                    `;
            });

            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
        }
    });
</script>
</body>

</html>