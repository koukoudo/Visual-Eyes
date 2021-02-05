<section id="container-dash" class="page-container">
    <section id="dash-profile" class="section-container dash">
        <h1>Profile</h1>
        <ul>
            <li>
                <label for="dash-first-name">First Name: </label>
                <input type="text" id="dash-first-name" value="<?php echo $this->session->userdata('firstname'); ?>" disabled>
            </li>
            <li>
                <label for="dash-last-name">Last Name: </label>
                <input type="text" id="dash-last-name" value="<?php echo $this->session->userdata('lastname'); ?>" disabled>
            </li>
            <li>
                <label for="dash-email">Email: </label>
                <input type="text" id="dash-email" value="<?php echo $this->session->userdata('email'); ?>" disabled>
            </li>
            <li>
                <small id="small-verified" for="dash-email">
                    <?php if ($this->session->userdata('verified') == 0) {
                        echo 'class="not-verified">not verified<i class="material-icons">cancel</i>';
                    } else {
                        echo 'class="verified">verified<i class="material-icons">verified_user</i>';
                    } ?>
                </small>
            </li>
            <!-- <li>
                <button id="btn-update-info"><i class="material-icons">check_box</i></button>  
                <button id="btn-dash-edit"><i class="material-icons">edit</i></button>
            </li> -->
        </ul>
    </section>

    <section id="dash-map" class="section-container dash">
        <h1>Location</h1>
        <div id="map-container">
            <script>
            var map, infoWindow;
            function initMap() {
                map = new google.maps.Map(document.getElementById('map-container'), {
                    center: {lat: -34.397, lng: 150.644},
                    zoom: 6
                });
                infoWindow = new google.maps.InfoWindow;

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    infoWindow.setPosition(pos);
                    infoWindow.setContent('Location found.');
                    infoWindow.open(map);
                    map.setCenter(pos);
                    }, function() {
                        handleLocationError(true, infoWindow, map.getCenter());
                    });
                    } else {
                        handleLocationError(false, infoWindow, map.getCenter());
                    }
                }

                function handleLocationError(browserHasGeolocation, infoWindow, pos) {
                infoWindow.setPosition(pos);
                infoWindow.setContent(browserHasGeolocation ?
                                    'Error: The Geolocation service failed.' :
                                    'Error: Your browser doesn\'t support geolocation.');
                infoWindow.open(map);
            }
            </script> 
            
            <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCj55blLeF1dBKVsoXLqI8_RfJpbFL2NLA&callback=initMap">
            </script>
        </div>
    </section>

    <section id="dash-rec" class="section-container dash">
        <h1>Recommended Datasets</h1>
        <p>Select one to start visualizing.</p>
        <ul>
            <?php /*foreach ($datasets as $dataset) {
                echo '<li><h3 class="rec-dataset">'.$dataset[0]['dataTitle'].'</h3></li>';
            } */?>
        </ul> 
    </section>

    <section id="dash-history" class="section-container dash">
        <h1>Visualization History</h1>
        <?php foreach ($charts as $chart) {
            echo '<img class="img-history" alt="'.$chart['title'].'" src="'.base_url('charts/').$chart['fileName'].'">';
        } ?>  
    </section>

    <section id="dash-overlay" class="dash overlay">
        <div id="dash-chart-popup">
            <img id="img-dash-popup"></img>
            <button id="btn-dash-download"><i class="material-icons">get_app</i> Download</button>
        </div>
    </section>
</section>