document.addEventListener('DOMContentLoaded', function() {
  // Add location event handler
  document.getElementById('location-add-button').addEventListener('click', function(event) {
    event.preventDefault();

    // Create a new location HTML element
    var i = document.querySelectorAll('.location-item').length;
    var locationHTML =
      '<div class="location-item location-item-new" data-index="' + i + '">' +
      '  <input type="text" name="guide-locations-' + i + '-name" placeholder="Name" class="location-name">' +
      '  <textarea id="guide-locations-' + i + '-description" name="guide-locations-' + i + '-description" placeholder="Description"></textarea>' +
      '  <a href="https://support.google.com/maps/answer/144361?hl=en" target="_blank"><span class="dashicons dashicons-info"></span></a>' +
      '  <input type="text" name="guide-locations-' + i + '-map" placeholder="Google Maps Embed Code"> ' +
      '  <label for="guide-locations-' + i + '-wifi">Wi-Fi Availabe</label> <input id="guide-locations-' + i + '-wifi" type="checkbox" name="guide-locations-' + i + '-wifi">' +
      '  <input type="text" name="guide-locations-' + i + '-price" placeholder="Price" class="location-details">' +
      '  <input type="text" name="guide-locations-' + i + '-hours" placeholder="Store Hours" class="location-details">' +
      '  <button class="location-delete-button button" data-index="' + i + '">Delete Location</button>' +
      '</div>';

    // Append the new location element to the container
    document.getElementById('location-container').insertAdjacentHTML('beforeend', locationHTML);

    // Initialize the WordPress editor for the new location
    var settings = {
      textareaName: 'guide-locations-' + i + '-description',
      mediaButtons: true,
      textareaRows: 5,
      tinymce: {
        toolbar1: 'formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | blockquote | link unlink | | pastetext removeformat',
        toolbar2: '',
        toolbar3: '',
        toolbar4: '',
        content_style: 'body { font-family: San Francisco, Segoe UI, DejaVu Sans, Helvetica, Arial, sans-serif; }'
      },
    };
    wp.editor.initialize('guide-locations-' + i + '-description', settings);
  });

  // Delete location event handler
  document.addEventListener('click', function(event) {
    if (event.target.matches('.location-delete-button')) {
      event.preventDefault();

      var editor = wp.data.select('core/editor');
      if (!editor) return;
      var button = event.target;
      var i = parseInt(button.dataset.index);

      // Confirm deletion
      if (!window.confirm('Are you sure you want to delete this location?')) return;

      // Check if the location has been saved
      if (document.querySelector(`.location-item[data-index="${i}"]`).classList.contains('location-item-new')) {
        // Location is newly added and not saved yet, remove the location form
        button.closest('.location-item').remove();
        return;
      }

      // Send a POST request to update the post meta
      fetch(guidePostsREST.restURL + '/delete-location/' + editor.getCurrentPostId() + '/' + i, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': guidePostsREST.nonce // Include the nonce in the request headers
        },
      })
      .then(function(response) {
        // Handle the response
        return response.json()
          .then((json) => {
            if (response.ok)
              return Promise.resolve(json);

            return Promise.reject(json);
        });
      })
      .then(function(data) {
        button.closest('.location-item').remove();
      })
      .catch(function(error) {
        // Handle failure
        wp.data.dispatch( 'core/notices' ).createNotice(
          'error',
          error.message,
          {
            isDismissible: true,
            type: 'snackbar'
          }
        );
      });
    }
  });

  // Subscribe to the save action
  wp.data.subscribe(function() {
    var editor = wp.data.select('core/editor');
    if (!editor) return;
    if (editor.isSavingPost() && !editor.isAutosavingPost()) {
      var locationsData = [];

      // Retrieve all the locations from the DOM
      var locationItems = document.querySelectorAll('.location-item');
      if (locationItems.length > 0) {
        locationItems.forEach(function(locationItem) {
          var i = locationItem.dataset.index;
          var locationData = {
            name: locationItem.querySelector('input[name^="guide-locations-' + i + '"]').value,
            description: wp.editor.getContent('guide-locations-' + i + '-description'),
            map: locationItem.querySelector('input[name^="guide-locations-' + i + '-map"]').value,
            wifi: locationItem.querySelector('input[name^="guide-locations-' + i + '-wifi"]').checked,
            price: locationItem.querySelector('input[name^="guide-locations-' + i + '-price"]').value,
            hours: locationItem.querySelector('input[name^="guide-locations-' + i + '-hours"]').value,
          };
          locationItem.classList.remove('location-item-new');
          locationsData.push(locationData);
        });
      }

      // Send a POST request to update the post meta
      fetch(guidePostsREST.restURL + '/save-locations/' + editor.getCurrentPostId(), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': guidePostsREST.nonce
        },
        body: JSON.stringify(locationsData)
      })
      .then(function(response) {
        return response.json()
          .then((json) => {
            if (response.ok)
              return Promise.resolve(json);

            return Promise.reject(json);
        });
      })
      .then(function(data) {
        // Handle successful request
        wp.data.dispatch('core/notices').createNotice(
          'success',
          data.message,
          {
            isDismissible: true,
            type: 'snackbar'
          }
        );
      })
      .catch(function(error) {
        console.error('An error occurred:', error);
        wp.data.dispatch('core/notices').createNotice(
          'error',
          error.message,
          {
            isDismissible: true,
            type: 'snackbar'
          }
        );
      });
    }
  });
});
