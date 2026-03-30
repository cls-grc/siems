    </div>
    
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 College Payment System. All rights reserved. | Program: <?php echo $_SESSION['program'] ?? 'N/A'; ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Auto-dismiss alerts after 5s
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert) alert.remove();
            });
        }, 5000);

        // Fee checkboxes auto-sum
        document.querySelectorAll('input[name="selected_fees[]"]').forEach(cb => {
            cb.addEventListener('change', updateAmount);
        });

        function updateAmount() {
            const total = Array.from(document.querySelectorAll('input[name="selected_fees[]"]:checked'))
                .reduce((sum, cb) => sum + parseFloat(cb.dataset.amount || 0), 0);
            const amountField = document.querySelector('input[name="amount_paid"]');
            if (amountField) amountField.value = total.toFixed(2);
        }
    </script>
</body>
</html>

