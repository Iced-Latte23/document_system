<link rel="stylesheet" href="/assets/styles/show_message.css">

<!-- Display Messages -->
<?php if ($success_message): ?>
    <div class="notification success-message" id="successMessage"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>
<?php if ($error_message): ?>
    <div class="notification error-message" id="errorMessage"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<script>
    // Hide messages after 2 seconds
    setTimeout(() => {
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        if (successMessage) successMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
    }, 2000);
</script>