<div class="wrap">
  <h1>FAB Custom User</h1>

  <?php settings_errors(); ?>

  <form method="post" action="options.php">
    <?php settings_fields( 'fabcustomuser-options' );
    do_settings_sections( 'fabcustomuser-options' );
    ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Ogni (n°) paragrafo</th>
        <td><input type="text" name="every_n_p" value="<?php echo esc_attr( get_option('every_n_p') ); ?>" style="width:100%" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">Prima del titolo</th>
        <td>
          <input type="radio" name="before_title" value="1" <?php checked(1, get_option('before_title'), true); ?>>Si
          <input type="radio" name="before_title" value="0" <?php checked(0, get_option('before_title'), true); ?>>No
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Dopo il titolo</th>
        <td>
          <input type="radio" name="after_title" value="1" <?php checked(1, get_option('after_title'), true); ?>>Si
          <input type="radio" name="after_title" value="0" <?php checked(0, get_option('after_title'), true); ?>>No
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Prima del contenuto</th>
        <td>
          <input type="radio" name="before_content" value="1" <?php checked(1, get_option('before_content'), true); ?>>Si
          <input type="radio" name="before_content" value="0" <?php checked(0, get_option('before_content'), true); ?>>No
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Dopo il contenuto</th>
        <td>
          <input type="radio" name="after_content" value="1" <?php checked(1, get_option('after_content'), true); ?>>Si
          <input type="radio" name="after_content" value="0" <?php checked(0, get_option('after_content'), true); ?>>No
        </td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>
</div>
