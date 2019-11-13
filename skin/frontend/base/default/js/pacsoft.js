var ServicePointForm = Class.create();
ServicePointForm.prototype = {
    initialize: function (formElementId,
                          loadingSpinnerId,
                          zipInputId,
                          responseDivId,
                          searchBtnId,
                          searchButtonValidationClass,
                          chosenParcelShopId,
                          chosenParcelShopDescriptionId,
                          adress_id,
                          rate_id,
                          country_code,
                          callback_url){

        this.formElement = $(formElementId);
        this.loadingSpinner = $(loadingSpinnerId);
        this.zipInput = $(zipInputId);
        this.responseDiv = $(responseDivId);
        this.searchBtn = $(searchBtnId);
        this.searchButtonValidationClass = searchButtonValidationClass;
        this.chosenParcelShopId = $(chosenParcelShopId);
        this.chosenParcelShopDescriptionId = $(chosenParcelShopDescriptionId)
        this.adress_id = adress_id;
        this.rate_id = rate_id;
        this.country_code = country_code;
        this.callback_url = callback_url;

        this.loadingSpinner.hide();
        this.oldZipLength = this.zipInput.value.length;
        this._addEventListeners();
        this._addValidation();
        this._toggleForm();
    },
    _addEventListeners: function () {
        var self = this;
        $$('input[name="shipping_method['+this.adress_id+']"]').each(
            function(sel){
                Event.observe(sel, 'click', function(){self._toggleForm();});
            }
        );
        $$('input[name="shipping_method"]').each(
            function(sel){
                Event.observe(sel, 'click', function(){self._toggleForm();});
            }
        );
        if(this.searchBtn){
            this.searchBtn.addEventListener('click', function(){
                self._showMap();
            });
        }else{
            if(self.country_code.toUpperCase() == "DK" || self.country_code.toUpperCase() == "NO"){
                var countryZipLength = 4;
            }else{
                var countryZipLength = 5;
            }

            this.zipInput.addEventListener('keyup',function(){
                if(this.oldZipLength != self.zipInput.value.length){
                    this.oldZipLength = self.zipInput.value.length;
                    if(this.oldZipLength == countryZipLength){
                        self._load();
                    }
                }
            });
        }
    },
    _addValidation: function(){
        var self = this;
        Validation.add(this.searchButtonValidationClass,'Please select a delivery point',function(value){
            self.adress_id;
            if(self.chosenParcelShopId.value != "")
            {
                return true;
            }
            return false;
        });
    },
    _validate: function(){

    },
    _toggleForm: function(){
        if($$('input[name="shipping_method['+this.adress_id+']"]:checked').size()){
            var selectedShippingMethodCode = $$('input[name="shipping_method['+this.adress_id+']"]:checked');
        }else{
            var selectedShippingMethodCode = $$('input[name="shipping_method"]:checked');
        }
        if(this.rate_id == selectedShippingMethodCode.pluck('value')){
            this.formElement.show();
        }else{
            this.formElement.hide();
        }
    },
    setLoadWaiting: function(){
        this.loadingSpinner.show();
    },
    _load: function(){
        var self = this;
        this.setLoadWaiting();
        new Ajax.Request(this.callback_url,
            {
                method:'post',
                parameters: {   action      : 'getServicePointElement',
                    zip         : this.zipInput.value,
                    formCode    : this.formElement.id,
                    countryCode : this.country_code},
                onSuccess: function(data){
                    self._setResponse(data);
                },
                onFailure: function(){
                    self._setErrorResponse()
                }
            });
    },
    _showMap: function(){
        var self = this;
        this.setLoadWaiting();
        new Ajax.Request(this.callback_url,
            {
                method:'post',
                parameters: {   action      : 'getMap',
                    zip         : this.zipInput.value,
                    formCode    : this.formElement.id,
                    countryCode : this.country_code},
                onSuccess: function(data){
                    self._setResponse(data);
                },
                onFailure: function(){
                    self._setErrorResponse()
                }
            });
    },
    _setResponse: function(response){
        //If map, then create overlay
        response = response.responseText.evalJSON(true);
        if(response['displayType'] == "map"){
            //Create div and servicePointMap
            var divName = 'servicePointMapDiv'+this.formElement.id;

            //Clean up - remove old div
            var oldDiv = document.getElementById(divName);
            if(oldDiv != null){
                oldDiv.parentNode.removeChild(oldDiv);
            }


            var servicePointMapDiv = document.createElement('div');
            servicePointMapDiv.id = divName;
            $(document.body).appendChild(servicePointMapDiv);
            servicePointMapDiv.innerHTML = response['html'];
            this.servicePointMap = new ServicePointMap(this.formElement, this.chosenParcelShopId, this.chosenParcelShopDescriptionId, servicePointMapDiv, this.country_code, response['servicePoints'], this.callback_url);
            this.servicePointMap._showServicePointMap();
        }else{
            //If selectbox, create selectbox
            this.responseDiv.innerHTML = response['html'];;
        }
        this.loadingSpinner.hide();


    },
    _setErrorResponse: function(){
        //Print error under zipinput
    }
};

var ServicePointMap = Class.create();
ServicePointMap.prototype = {
    initialize: function (formElement, chosenParcelShopId, chosenParcelShopDescription, divElement, country_code, servicePoints, callback_url){
        this.servicePointMapDiv = divElement;
        this.servicePoints = servicePoints;
        this.country_code = country_code;
        this.callback_url = callback_url;
        this.chosenParcelShopLabel = $(chosenParcelShopDescription);
        this.chosenParcelShopId = $(chosenParcelShopId);
        this.markers = [];
        this.markerLookup = [];
        this.timer = null;
        this.selectedMarker = null;
        this._addEventListeners();
        this._initializeMap();
    },
    _addEventListeners: function () {
        var self = this;
        $$('.pacsoft-overlay-content #pacsoft-overlay-content-close').each(
            function(sel){
                Event.observe(sel, 'click', function(){self._hideServicePointMap();});
            }
        );
        $$('.pacsoft-overlay-content #pacsoft-overlay-content-save').each(
            function(sel){
                Event.observe(sel, 'click', function(){self._save();});
            }
        );
    },
    _initializeMap: function(){
        var mapOptions = {
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById('pacsoft-servicepoints-map'), mapOptions);
        this.map = map;

        var self = this;
        google.maps.event.addListener(this.map, 'bounds_changed', function () {
            //Hack to handle the problem with bounds_changed fired multiple times...
            clearTimeout(self.timer);
            self.timer = setTimeout(function() {
                self._loadMapFromCurrentBounds();
            }, 500);
        });

        google.maps.event.addListenerOnce(this.map, 'idle', function() {
            google.maps.event.trigger(this.map, 'resize');
        });

        this._reloadMap();
    },
    _reloadMap: function(){
        this._reloadMarkers();

        //Set center as first service point
        var place_lat_lng = new google.maps.LatLng(this.servicePoints[0].coordinates[0].northing, this.servicePoints[0].coordinates[0].easting);
        this.map.setCenter(place_lat_lng);

        var bounds = new google.maps.LatLngBounds(); // empty bounds object
        for (var i = 0; i < this.markers.length; i++ ) {
            bounds.extend(this.markers[i].getPosition()); // add the marker to the bounds
        }

        //Only extend boundaries if there is more than one marker!
        if(this.markers.length > 1){
            this.map.fitBounds(bounds);
        }


    },
    _reloadMarkers: function(){

        var marker, i;

        // loop through all the places to get markers
        for (var i=0;i<this.servicePoints.length;i++)
        {
            var servicePoint = this.servicePoints[i];

            if(!this._markerAlreadyPresent([servicePoint.coordinates[0].northing, servicePoint.coordinates[0].easting])){

                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(servicePoint.coordinates[0].northing, servicePoint.coordinates[0].easting),
                    map: this.map,
                    animation: google.maps.Animation.DROP,
                    icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                });

                marker.servicePoint = servicePoint;

                var self = this;
                google.maps.event.addListener(marker, 'click', function(){
                    self._markerClicked(this)
                });

                this.markerLookup.push([servicePoint.coordinates[0].northing, servicePoint.coordinates[0].easting]);
                this.markers.push(marker);
                // add the marker
                marker.setMap(this.map);
            }
        }
    },
    _markerAlreadyPresent: function(lok){
        for (var i = 0, l = this.markerLookup.length; i < l; i++) {
            if (this.markerLookup[i][0] === lok[0] && this.markerLookup[i][1] === lok[1]) {
                return true;
            }
        }
        return false;
    },
    _loadMapFromCurrentBounds: function(){

        // Determine the map bounds
        var bounds = this.map.getBounds();
        console.log(bounds);
        // Then the points
        var swPoint = bounds.getSouthWest();
        var nePoint = bounds.getNorthEast();

        // Now, each individual coordinate
        var swLat = swPoint.lat();
        var swLng = swPoint.lng();
        var neLat = nePoint.lat();
        var neLng = nePoint.lng();
        var self = this;
        new Ajax.Request(this.callback_url,
            {
                method:'post',
                parameters: {
                    action : 'getServicePointsWithinBounds',
                    swLat : swLat,
                    swLng : swLng,
                    neLat : neLat,
                    neLng : neLng,
                    countryCode : this.country_code},
                onSuccess: function(response){
                    self.servicePoints = response.responseText.evalJSON(true);
                    self._reloadMarkers();
                },
                onFailure: function(){
                    alert('Could not load service points')
                }
            });

    },
    _save: function(){
        //Chosen parcel shop id
        this.chosenParcelShopId.value = this.selectedMarker.servicePoint.servicePointId;
        //Chosen parcelshop info
        this.chosenParcelShopLabel.innerHTML = this._getMarkerInfo(this.selectedMarker);
        this._hideServicePointMap();
    },
    _hideServicePointMap: function(){
//        this.servicePointMapDiv.hide();
        this.servicePointMapDiv.remove();
        this.map = null;
    },
    _showServicePointMap: function(){
        this.servicePointMapDiv.show();
    },
    _cleanUp: function(){
        this.map = null;
    },
    _markerClicked: function(marker){
        for (var i=0; i<this.markers.length; i++) {
            this.markers[i].setIcon('http://maps.google.com/mapfiles/ms/icons/green-dot.png');
        }
        marker.setIcon('http://maps.google.com/mapfiles/ms/icons/red-dot.png');

        $('pacsoft-servicepoint-info-address').innerHTML = this._getMarkerInfo(marker);

        $$('.pacsoft-servicepoint-info-opening-hours').each(
            function(sel){
                sel.innerHTML = '-';
            }
        );

        marker.servicePoint.openingHours.each(
            function(sel){
                $('pacsoft-servicepoint-info-opening-hours-'+sel.day).innerHTML = sel.from1 + '-' + sel.to1;
            }
        );

        this.selectedMarker = marker;
    },
    _getMarkerInfo: function(marker){
        return marker.servicePoint.name + '</br>' + marker.servicePoint.visitingAddress.streetName + ' ' + marker.servicePoint.visitingAddress.streetNumber + '</br>' + marker.servicePoint.visitingAddress.postalCode + ' ' + 	marker.servicePoint.visitingAddress.city;

    }
};


var DeliveryNoteForm = Class.create();
DeliveryNoteForm.prototype = {
    initialize: function (adress_id,
                          rate_id,
                          formElementId){
        this.adress_id = adress_id;
        this.rate_id = rate_id;
        this.formElement = $(formElementId);
        this._addEventListeners()
        this._toggleForm();
    },
    _addEventListeners: function () {
        var self = this;
        $$('input[name="shipping_method['+this.adress_id+']"]').each(
            function(sel){
                Event.observe(sel, 'click', function(){self._toggleForm();});
            }
        );
        $$('input[name="shipping_method"]').each(
            function(sel){
                Event.observe(sel, 'click', function(){self._toggleForm();});
            }
        );
    },
    _toggleForm: function(){
        if($$('input[name="shipping_method['+this.adress_id+']"]:checked').size()){
            var selectedShippingMethodCode = $$('input[name="shipping_method['+this.adress_id+']"]:checked');
        }else{
            var selectedShippingMethodCode = $$('input[name="shipping_method"]:checked');
        }
        if(this.rate_id == selectedShippingMethodCode.pluck('value')){
            this.formElement.show();
        }else{
            this.formElement.hide();
        }
    }
};

