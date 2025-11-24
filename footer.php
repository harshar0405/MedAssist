</div> <!-- closes .container from header.php -->

<!-- Floating SOS Button -->
<button id="sosBtn" class="btn btn-danger" title="Emergency SOS">
    <i class="bi bi-crosshair"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sosBtn = document.getElementById('sosBtn');
    if (!sosBtn) return;

    sosBtn.addEventListener('click', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                Swal.fire({
                    title: "🚨 EMERGENCY ALERT! 🚨",
                    html: `
                        <b>Notifying 108 Ambulance and Nearest Hospital.</b><br><br>
                        Your Simulated Location:<br>
                        Latitude: <b>${lat.toFixed(4)}</b><br>
                        Longitude: <b>${lon.toFixed(4)}</b><br><br>
                        Help is on the way. Stay calm.`,
                    icon: "error",
                    confirmButtonText: "I'm Safe / Dismiss",
                    customClass: { confirmButton: 'btn btn-success' },
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }, error => {
                Swal.fire("Error", "Could not get your location. Please call 108 manually.", "warning");
            });
        } else {
            Swal.fire("Error", "Geolocation is not supported by your browser.", "warning");
        }
    });
});
</script>

</body>
</html>
