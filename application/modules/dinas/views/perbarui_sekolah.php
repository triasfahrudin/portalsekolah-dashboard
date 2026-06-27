<style>
    .lds-roller {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }

    .lds-roller div {
        animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        transform-origin: 40px 40px;
    }

    .lds-roller div:after {
        content: " ";
        display: block;
        position: absolute;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #fff;
        margin: -4px 0 0 -4px;
    }

    .lds-roller div:nth-child(1) {
        animation-delay: -0.036s;
    }

    .lds-roller div:nth-child(1):after {
        top: 63px;
        left: 63px;
    }

    .lds-roller div:nth-child(2) {
        animation-delay: -0.072s;
    }

    .lds-roller div:nth-child(2):after {
        top: 68px;
        left: 56px;
    }

    .lds-roller div:nth-child(3) {
        animation-delay: -0.108s;
    }

    .lds-roller div:nth-child(3):after {
        top: 71px;
        left: 48px;
    }

    .lds-roller div:nth-child(4) {
        animation-delay: -0.144s;
    }

    .lds-roller div:nth-child(4):after {
        top: 72px;
        left: 40px;
    }

    .lds-roller div:nth-child(5) {
        animation-delay: -0.18s;
    }

    .lds-roller div:nth-child(5):after {
        top: 71px;
        left: 32px;
    }

    .lds-roller div:nth-child(6) {
        animation-delay: -0.216s;
    }

    .lds-roller div:nth-child(6):after {
        top: 68px;
        left: 24px;
    }

    .lds-roller div:nth-child(7) {
        animation-delay: -0.252s;
    }

    .lds-roller div:nth-child(7):after {
        top: 63px;
        left: 17px;
    }

    .lds-roller div:nth-child(8) {
        animation-delay: -0.288s;
    }

    .lds-roller div:nth-child(8):after {
        top: 56px;
        left: 12px;
    }

    @keyframes lds-roller {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="panel panel-primary"> 
    <div class="panel-heading"> 
        <h3 class="panel-title">Perbarui Data Sekolah</h3> 
    </div> 
    <div class="panel-body">
        
        <a class="btn btn-primary" href="#!" role="button" onclick="getSekolah()">Perbarui sekarang</a>
    </div> 
</div>

<script>
    function getSekolah() {
        var loadingIcon = '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';

        // Display loading icon
        showLoadingIcon();

        // Make the AJAX request to getSekolah endpoint
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?php echo site_url('dinas/getsekolah');?>', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Hide loading icon
                hideLoadingIcon();

                // Process the response
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    // Show success notification
                    showNotification('Proses selesai.');
                } else {
                    // Show error notification
                    showNotification('Terjadi kesalahan saat memproses.');
                }
            }
        };
        xhr.send();

        function showLoadingIcon() {
            // Create a container element to hold the loading icon
            var container = document.createElement('div');
            container.className = 'loading-container';

            // Set the loading icon HTML within the container
            container.innerHTML = loadingIcon;

            // Append the container to the body
            document.body.appendChild(container);
        }

        function hideLoadingIcon() {
            // Remove the loading container from the body
            var container = document.querySelector('.loading-container');
            if (container) {
                container.parentNode.removeChild(container);
            }
        }

        function showNotification(message) {
            // Display a notification to the user
            alert(message);
        }
    }
</script>
