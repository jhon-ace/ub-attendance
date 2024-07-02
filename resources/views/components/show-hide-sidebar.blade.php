
@props(['toggleButtonId', 'sidebarContainerId', 'dashboardContentId', 'toggleIconId'])

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('{{ $toggleButtonId }}');
        const sidebarContainer = document.getElementById('{{ $sidebarContainerId }}');
        const dashboardContent = document.getElementById('{{ $dashboardContentId }}');
        const toggleIcon = document.getElementById('{{ $toggleIconId }}');

        toggleButton.addEventListener('click', function() {
            sidebarContainer.classList.toggle('hidden'); // Toggle the 'hidden' class on the sidebar container
            if (sidebarContainer.classList.contains('hidden')) {
                // If sidebar is hidden, adjust dashboard content margin
                dashboardContent.classList.remove('ml-14', 'md:ml-48');
                toggleIcon.classList.remove('fa-solid', 'fa-bars');
                toggleIcon.classList.add('fa-solid', 'fa-bars');
            } else {
                // If sidebar is shown, apply appropriate margin
                dashboardContent.classList.add('ml-14', 'md:ml-48');
                toggleIcon.classList.remove('fa-solid', 'fa-bars');
                toggleIcon.classList.add('fa-solid', 'fa-bars');
            }
        });
    });
</script>

