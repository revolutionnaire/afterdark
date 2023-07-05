document.addEventListener('DOMContentLoaded', function() {
    var categoryThumbnailField = document.getElementById('image-id');
    var categoryThumbnailPreview = document.getElementById('image-preview');

    // Handle image upload button click
    document.getElementById('button-image-upload').addEventListener('click', function(event) {
      event.preventDefault();

      // Create the media frame
      var mediaFrame = wp.media({
        title: 'Select or Upload Featured Image',
        button: {
            text: 'Use Image'
        },
        multiple: false
      });

      // Handle image selection
      mediaFrame.on('select', function() {
        var attachment = mediaFrame.state().get('selection').first().toJSON();
        var image = document.createElement('img');
        image.src = attachment.url;
        image.alt = attachment.alt;

        // Check if image already exists and remove it
        if (categoryThumbnailPreview.querySelector('img') != null)
          categoryThumbnailPreview.querySelector('img').remove();

        // Set the selected image ID and preview
        categoryThumbnailField.value = attachment.id;
        categoryThumbnailPreview.appendChild(image);
        categoryThumbnailPreview.classList.remove('bg-white');

        // Rename Upload button
        document.getElementById('button-image-upload').innerHTML = 'Change Image';
      });

      // Open the media frame
      mediaFrame.open();
    });

  // Handle image remove button click
  document.getElementById('button-image-remove').addEventListener('click', function(event) {
    event.preventDefault();

    // Clear the selected image ID and style preview
    categoryThumbnailField.value = '';
    categoryThumbnailPreview.querySelector('img').remove();
    categoryThumbnailPreview.style.height = 'auto';
    categoryThumbnailPreview.classList.add('bg-white');

    // Change the Upload button's text
    document.getElementById('button-image-upload').innerHTML = 'Upload Image';
  });
});
