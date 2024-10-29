<?php

  function gallery_metabox_enqueue($hook) {
    if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
      wp_enqueue_script('gallery-metabox', get_stylesheet_directory_uri() . '/lib/gallery-metabox/js/gallery-metabox.js', array('jquery', 'jquery-ui-sortable'));
      wp_enqueue_style('gallery-metabox', get_stylesheet_directory_uri() . '/lib/gallery-metabox/css/gallery-metabox.css');
    }
  }
  add_action('admin_enqueue_scripts', 'gallery_metabox_enqueue');

  function add_gallery_metabox($post_type) {
    $types = array('snack', 'collection', 'country');

    if (in_array($post_type, $types)) {
      add_meta_box(
        'gallery-metabox',
        'Gallery',
        'gallery_meta_callback',
        $post_type,
        'side',
        'low'
      );
    }
  }
  add_action('add_meta_boxes', 'add_gallery_metabox');

  function add_video_metabox($post_type) {
    $types = array('snack', 'collection');

//    if (in_array($post_type, $types)) {
//      add_meta_box(
//        'video-metabox',
//        'Featured video',
//        'video_meta_callback',
//        $post_type,
//        'side',
//        'low'
//      );
//    }
  }
  add_action('add_meta_boxes', 'add_video_metabox');

  function gallery_meta_callback($post) {
    wp_nonce_field( basename(__FILE__), 'gallery_meta_nonce' );
    $urls = get_post_meta($post->ID, 'vdw_gallery_id', true);
    ?>
    <table class="form-table vdw-metabox">
      <tr><td>
        <a class="vdw-add button" href="#" data-input-name="vdw_gallery_id[&index]" data-uploader-title="Add media" data-uploader-button-text="Add media">Add media</a>

        <ul class="vdw-metabox__list">
        <?php if ($urls) : foreach ($urls as $key => $value) : ?>

          <li>
            <input type="hidden" data-input-name="vdw_gallery_id[&index]" name="vdw_gallery_id[<?php echo $key; ?>]" value="<?php echo $value; ?>">
            <img class="image-preview" src="<?php echo in_array(strtolower(pathinfo($value, PATHINFO_EXTENSION)), ['mp4', 'avi', 'mov', 'mkv', 'flv', 'wmv', 'webm']) ? '/wp-includes/images/media/video.png' : $value; ?>">
            <a class="change-media button button-small" href="#" data-multiple data-uploader-title="Change media" data-uploader-button-text="Change media">Change media</a>
            <br>
            <small><a class="remove-image" href="#">Remove media</a></small>
          </li>

        <?php endforeach; endif; ?>
        </ul>

      </td></tr>
    </table>
  <?php }

  function video_meta_callback($post) {
    wp_nonce_field( basename(__FILE__), 'video_meta_nonce' );
    $id = get_post_meta($post->ID, 'vdw_video_id', true);

    ?>
    <table class="form-table vdw-metabox">
      <tr><td>
        <a class="vdw-add button" href="#" data-input-name="vdw_video_id" data-uploader-title="Add media" data-uploader-button-text="Add media">Add media</a>

        <ul class="vdw-metabox__list">
        <?php if($id) :?>

          <li>
            <input type="hidden" data-input-name="vdw_video_id" name="vdw_video_id" value="<?php echo $id; ?>">
            <img class="image-preview" src="<?php echo wp_get_attachment_image_src($id)[0]; ?>">
            <a class="change-media button button-small" href="#" data-uploader-title="Change media" data-uploader-button-text="Change media">Change media</a>
            <br>
            <small><a class="remove-image" href="#">Remove media</a></small>
          </li>

        <?php endif; ?>
        </ul>

      </td></tr>
    </table>
  <?php }

  function gallery_meta_save($post_id) {
    if (!isset($_POST['gallery_meta_nonce']) || !wp_verify_nonce($_POST['gallery_meta_nonce'], basename(__FILE__))) return;

    if (!current_user_can('edit_post', $post_id)) return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if(isset($_POST['vdw_gallery_id'])) {
      update_post_meta($post_id, 'vdw_gallery_id', $_POST['vdw_gallery_id']);
    } else {
      delete_post_meta($post_id, 'vdw_gallery_id');
    }
  }
  add_action('save_post', 'gallery_meta_save');


  function video_meta_save($post_id) {
    if (!isset($_POST['video_meta_nonce']) || !wp_verify_nonce($_POST['video_meta_nonce'], basename(__FILE__))) return;

    if (!current_user_can('edit_post', $post_id)) return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if(isset($_POST['vdw_video_id'])) {
      update_post_meta($post_id, 'vdw_video_id', $_POST['vdw_video_id']);
    } else {
      delete_post_meta($post_id, 'vdw_video_id');
    }
  }
  add_action('save_post', 'video_meta_save');

?>