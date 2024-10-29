jQuery(function($) {

  var file_frame;

  $(document).on('click', '.vdw-metabox a.vdw-add', function(e) {
    e.preventDefault();
    var thisBtn = $(this)
    var metabox = thisBtn.closest('.vdw-metabox')
    var metaboxList = metabox.find('.vdw-metabox__list')
    var inputName = thisBtn.data('input-name')

    if (file_frame) file_frame.close();

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader-title'),
      button: {
        text: $(this).data('uploader-button-text'),
      },
      library: {
        type: ['video/mp4', 'video/avi', 'video/mov', 'video/mkv', 'video/flv', 'video/wmv', 'video/webm', 'image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp']
      },
      multiple: true
    });

    file_frame.on('select', function() {
      var listIndex = metaboxList.find('li').index(metaboxList.find('li:last')),
        selection = file_frame.state().get('selection');

      selection.map(function(attachment, i) {
        attachment = attachment.toJSON()
        let index = listIndex + (i + 1);

        if(attachment.type !== 'image' && attachment.type !== 'video'){
          return;
        }

        metaboxList.append(`
            <li>
              <input type="hidden" data-input-name="${inputName}" name="${inputName.replace('&index', index)}" value="${attachment.url}">
              ${attachment.type === 'image' ? `
                  <img class="image-preview" src="${attachment.sizes.thumbnail.url}">
              ` : `
                <img class="image-preview" src="${attachment.icon}">
              `}
              <a class="change-media button button-small" href="#" data-uploader-title="Change media" data-uploader-button-text="Change media">Change media</a>
              <br>
              <small><a class="remove-image" href="#">Remove media</a></small>
            </li>`);
      });
    });

    makeSortable();

    file_frame.open();

  });

  $(document).on('click', '.vdw-metabox a.change-media', function(e) {

    e.preventDefault();

    var that = $(this);

    if (file_frame) file_frame.close();

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader-title'),
      button: {
        text: $(this).data('uploader-button-text'),
      },
      multiple: false
    });

    file_frame.on( 'select', function() {
      let attachment = file_frame.state().get('selection').first().toJSON();

      if(attachment.type === 'image'){
        that.parent().find('input:hidden').attr('value', attachment.url);
        that.parent().find('img.image-preview').attr('src', attachment.sizes.thumbnail.url);
      } else if(attachment.type === 'video'){
        that.parent().find('input:hidden').attr('value', attachment.url);
        that.parent().find('img.image-preview').attr('src', attachment.icon);
      }

    });

    file_frame.open();

  });

  function resetIndex() {
    $('.vdw-metabox__list li').each(function(i) {
      $(this).find('input:hidden').attr('name', $(this).find('input:hidden').attr('data-input-name').replace('&index', i));
    });
  }

  function makeSortable() {
    $('.vdw-metabox__list').sortable({
      opacity: 0.6,
      stop: function() {
        resetIndex();
      }
    });
  }

  $(document).on('click', '#gallery-metabox a.remove-image', function(e) {
    e.preventDefault();

    $(this).parents('li').animate({ opacity: 0 }, 200, function() {
      $(this).remove();
      resetIndex();
    });
  });

  makeSortable();

});