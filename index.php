<html>
  <head>
    <title>Park'o top</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <link rel="stylesheet" type="text/css" href="./style.css" />
    <script type="module" src="./dest/index.js"></script>
  </head>
  <body>
  <button id="getUserLocation">Parking near my position</button>
	<input id="searchBox" type="text" value="Paris 1er Arrondissement"></input>
	<button id="getSearchParams"> <- Parking near a location</button>
    <button id="locationButton">Pan to current location</button>
    <div id="map"></div>

    <!-- 
      The `defer` attribute causes the callback to execute after the full HTML
      document has been parsed. For non-blocking uses, avoiding race conditions,
      and consistent behavior across browsers, consider loading using Promises
      with https://www.npmjs.com/package/@googlemaps/js-api-loader.
      -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSd09yCGbrayGGablBGR4JaFP04nTfP5M&callback=initMap&v=weekly"
      defer
    ></script>
  </body>
</html>