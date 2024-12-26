<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (session()->has('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: "<?php echo session('error'); ?>", 
                position: 'top', 
                showConfirmButton: false,
                timer: 3000,
                toast: true,
            });
        });
    </script>
<?php endif; ?>

<?php if (session()->has('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: "<?php echo session('success'); ?>", 
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                toast: true,
            });
        });
    </script>
<?php endif; ?>

<?php if (session()->has('alert')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'info',
                title: "<?php echo session('alert'); ?>", 
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                toast: true,
            });
        });
    </script>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var errorMessage = '<ul>';
            <?php foreach (session('errors') as $error): ?>
                errorMessage += '<li><?php echo esc($error); ?></li>'; 
            <?php endforeach; ?>
            errorMessage += '</ul>';

            Swal.fire({
                icon: 'error',
                title: errorMessage, 
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                toast: true,
            });
        });
    </script>
<?php endif; ?>
