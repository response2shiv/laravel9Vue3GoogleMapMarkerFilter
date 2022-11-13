<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Yahaal Fullstack Developer Coding Challenge</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body class="antialiased">
    <h2>Listing Page</h2>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="listings-gender-type">Gender</label>
                    <select id="listings-gender-type" class="form-select">
                        <option value="0" selected>All</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="person_name">Person Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" id="person_name" placeholder="Person Name">
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="form-group">
                    <label for="listings-location-wise">People From location (living in a radius of max 2000KM)</label>
                    <select id="listings-location-wise" class="form-select">
                        <option value="0" selected>Select location</option>
                        <option value="London">London</option>
                        <option value="Paris">Paris</option>
                        <option value="Kansas">Kansas</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <p>Total = <span id="people-count"></span></p>
                <ul class="list-group" style="max-height: 450px;overflow: auto;">
                    <li class="list-group-item">
                        Shiv Shanker
                    </li>
                    <li class="list-group-item">
                        Shobha
                    </li>
                </ul>
            </div>
            <div class="col-sm-8">
                <div id="googleMap" style="border:0;width:100%;height:450px;"></div>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
    let initialListings = <?php echo json_encode($people); ?>;
    let listings = <?php echo json_encode($people); ?>;

    // Start: google map api variables
    const centerCoordinates = {
        latitude: 40.730610,
        longitude: -73.935242
    };
    const radius = 2000; // in km unit
    const markerBlueIcon = 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
    const markerPinkIcon = 'https://maps.google.com/mapfiles/ms/icons/pink-dot.png';
    let markers = [];
    var map = '';
    // End: google map api variables
    let filterData = {
        gender: '',
        personName: '',
        location: ''
    };

    jQuery(document).ready(function() {
        loadingHtml();
        setListingsHtml();
        jQuery('#people-count').html(`${listings.length}/${initialListings.length}`);
        jQuery('#listings-gender-type').on('change', function() {
            filterData.gender = jQuery(this).val();
            filterListings();
        });
        jQuery('#person_name').on('keyup', function() {
            filterData.personName = jQuery(this).val();
            filterListings();
        });
        jQuery('#listings-location-wise').on('change', function() {
            filterData.location = jQuery(this).val();
            filterLocationWiseListings();
        });
    });

    const filterListings = () => {
        loadingHtml();
        listings = initialListings;

        if (filterData.gender)
            listings = listings.filter(x => x.gender == filterData.gender);
        if (filterData.personName)
            listings = listings.filter(x => (x.first_name.toLowerCase().indexOf(filterData.personName.toLowerCase()) == 0) || (x.last_name.toLowerCase().indexOf(filterData.personName.toLowerCase()) == 0));

        jQuery('#people-count').html(`${listings.length}/${initialListings.length}`);
        setListingsHtml();
        googleMapRemoveMarkers();
        listingsOnMap();
    };

    const filterLocationWiseListings = () => {
        let center = {latitude: 0, longitude: 0};
        if(filterData.location == 'London')
            center = {latitude: 51.509865, longitude: -0.118092};
        if(filterData.location == 'Paris')
            center = {latitude: 48.864716, longitude: 2.349014};
        if(filterData.location == 'Kansas')
            center = {latitude: 39.0997, longitude: 94.5786};

        googleMapRemoveMarkers();
        updateMarkers(center);
        setListingsHtml();
    };

    const loadingHtml = () => {
        let html = `<li class="list-group-item"><div class="spinner-border current_listing_wrapper_item" role="status">
                    <span class="sr-only">Loading...</span>
                </div></li>`;
        jQuery('.list-group').html(html);
    };

    const setListingsHtml = () => {
        let listingsHtml = '';
        if (listings.length > 0) {
            listings.forEach(function(listing, index) {
                listingsHtml += `<li class="list-group-item">
                                        ${listing.first_name} ${listing.last_name} - (${listing.gender})
                                    </li>`;
            });

            jQuery('.list-group').html(listingsHtml);
        } else {
            let noData = `<li class="list-group-item">No data found</li>`;
            jQuery('.list-group').html(noData);
        }
    };

    const googleMapObject = () => {
        return new google.maps.Map(document.getElementById("googleMap"), {
            center: new google.maps.LatLng(centerCoordinates.latitude, centerCoordinates.longitude),
            zoom: 2,
            mapTypeControl: false,
            // draggable: true,
            // scaleControl: true,
            // scrollwheel: true,
            // navigationControl: true,
            // mapTypeId: google.maps.MapTypeId.ROADMAP
            streetViewControl: false,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_TOP,
            },
            fullscreenControl: false
        });
    };

    const googleMapRemoveMarkers = () => {
        for (let i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers = [];
    };

    function createMarkers($listings) {
        for (let i = 0; i < listings.length; i++) {
            let coordPos = new google.maps.LatLng(listings[i].lat, listings[i].lon);
            let customIcon = ((listings[i].gender == 'Male') ? markerBlueIcon : markerPinkIcon);
            markers.push(new google.maps.Marker({
                position: coordPos,
                icon: customIcon,
                map: map,
            }));
            let markerInfoHtml = `<div class="info-window" style="width: 100px;">${listings[i].first_name} ${listings[i].last_name}</div>`;
            let markerInfo = new google.maps.InfoWindow({
                content: markerInfoHtml,
                position: coordPos,
                disableAutoPan: true,
            });
            markers[i].addListener("click", () => {
                markerInfo.open({
                    anchor: markers[i],
                    map,
                });
            });
        }
        // new markerClusterer.MarkerClusterer({
        //     markers,
        //     map
        // });
    }

    function updateMarkers(center) {
        listings = [];
        if (initialListings.length > 0) {
            for (let i = 0; i < initialListings.length; i++) {
                let marksDistance = parseInt(haversine_distance({latitude: initialListings[i].lat, longitude: initialListings[i].lon}, center));
                if(marksDistance <= radius){
                    listings.push(initialListings[i]);
                }
            }
            if (listings.length > 0)
                createMarkers(listings);
        }
    }

    function haversine_distance(mk1, mk2) {
    //   var R = 3958.8; // Radius of the Earth in miles
      var R = 6371.0710; // Radius of the Earth in km
      var rlat1 = mk1.latitude * (Math.PI/180); // Convert degrees to radians
      var rlat2 = mk2.latitude * (Math.PI/180); // Convert degrees to radians
      var difflat = rlat2-rlat1; // Radian difference (latitudes)
      var difflon = (mk2.longitude-mk1.longitude) * (Math.PI/180); // Radian difference (longitudes)

      var d = 2 * R * Math.asin(Math.sqrt(Math.sin(difflat/2)*Math.sin(difflat/2)+Math.cos(rlat1)*Math.cos(rlat2)*Math.sin(difflon/2)*Math.sin(difflon/2)));
      return d;
    }

    function listingsOnMap() {
        map = googleMapObject();
        if (listings.length > 0) {
            createMarkers(listings);
        } else {
            googleMapRemoveMarkers();
        }
    }

</script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=ZZzzSyBndMU8J0y3dRDrtQsgZjq-X94WznNzmgk&callback=listingsOnMap"></script>

</html>